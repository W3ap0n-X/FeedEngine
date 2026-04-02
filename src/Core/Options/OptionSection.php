<?php
namespace Qck\FeedEngine\Core\Options;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;

abstract class OptionSection implements Options {

    /**
     * The unique suffix for the DB row (e.g., 'general_options')
     */
    abstract public function get_name(): string;

    /**
     * The Title displayed in the Admin UI
     */
    abstract public function get_title(): string;

    abstract public function get_description(): string;

    /**
     * Returns an array of OptionEntry objects
     * @return OptionEntry[]
     */
    abstract public function get_schema(): array;

    /**
     * Returns the full, prefixed database key.
     * Result: qckfe_general_options
     */
    public function get_db_row(): string {
        return Manifest::PREFIX . '_' . $this->get_name();
    }

    /**
     * Fetches the current values from the database.
     */
    public function get_values(): array {
        return get_option( $this->get_db_row(), [] );
    }

    /**
     * Helper to find a specific field definition by its key.
     */
    public function get_field_definition( string $key ): ?OptionEntry {
        $fields = $this->get_schema();
        return $fields[$key] ?? null;
    }

    public function set_defaults() {
        $fields = $this->get_schema();
        foreach ($fields as $field) {
            \Qck\FeedEngine\Core\Debug::logDump($field, __METHOD__);
        }
    }

    /**
     * Deep-searches an array using a path of keys.
     *
     * @param array $data The nested settings array.
     * @param array|string $path The path to the value (e.g., ['services', 'google', 'api_key']).
     * @param mixed $default What to return if the path doesn't exist.
     * @return mixed
     */
    protected function deep_get(array $data, $path, $default = null) {
        // If it's a simple string, just return the top-level value
        if (is_string($path) && !str_contains($path, '.')) {
            return $data[$path] ?? $default;
        }

        // Convert dot notation 'services.google' to ['services', 'google']
        $keys = is_array($path) ? $path : explode('.', $path);

        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }

        return $data;
    }

    

    public function get($name) {
        $values = $this->get_values(); // The raw DB row
        $entry = $this->get_field_definition($name);

        if ($entry && !empty($entry->path)) {
            // Build the full path: [path_segment, key]
            $full_path = array_merge($entry->path, [$entry->key]);
            return $this->deep_get($values, $full_path, $entry->default);
        }

        // Fallback to top-level or default
        return $values[$name] ?? ($entry->default ?? null);
    }

    public function set($name, $setValue) {
        $data  = $this->get_values();
        $entry = $this->get_field_definition( $name );
        $path  = ( $entry && ! empty( $entry->path ) ) ? $entry->path : [];

        // Run the mutation
        $this->deep_set( $data, $path, $name, $value );

        // Persistence: This triggers the WordPress update_option filter stack
        return update_option( $this->get_db_row(), $data );
    }

    /**
     * Sets a value deep within an array based on a path.
     * * @param array &$data The array to modify (passed by reference).
     * @param array $path  The nesting path, e.g., ['services', 'google'].
     * @param string $key  The actual setting key.
     * @param mixed $value The new value.
     */
    protected function deep_set( array &$data, array $path, string $key, $value ) {
        $temp = &$data;

        foreach ( $path as $step ) {
            // If the step isn't an array, make it one so we can keep drilling.
            if ( ! isset( $temp[$step] ) || ! is_array( $temp[$step] ) ) {
                $temp[$step] = [];
            }
            $temp = &$temp[$step];
        }

        $temp[$key] = $value;
    }

    /**
     * Implements remove() from the Options interface.
     */
    public function remove( $name) {
        $data  = $this->get_values();
        $entry = $this->get_field_definition( $name );
        $path  = ( $entry && ! empty( $entry->path ) ) ? $entry->path : [];

        $this->deep_unset( $data, $path, $name );

        return update_option( $this->get_db_row(), $data );
    }

    /**
     * The recursive helper to kill a key deep in the nest.
     */
    protected function deep_unset( array &$data, array $path, string $key ) {
        $temp = &$data;

        foreach ( $path as $step ) {
            if ( ! isset( $temp[$step] ) || ! is_array( $temp[$step] ) ) {
                return; // The path doesn't exist, our work here is done.
            }
            $temp = &$temp[$step];
        }

        unset( $temp[$key] );
    }

    public function get_value_for_entry(OptionEntry $entry) {
        $all_data = $this->get_values(); // Fetches the whole DB row
        
        // If there's no path, just grab the key from the top level
        if (empty($entry->path)) {
            return $all_data[$entry->key] ?? $entry->default;
        }

        // Walk the path
        $current = $all_data;
        foreach ($entry->path as $step) {
            if (isset($current[$step]) && is_array($current[$step])) {
                $current = $current[$step];
            } else {
                return $entry->default; // Path broken, return default
            }
        }

        return $current[$entry->key] ?? $entry->default;
    }
}