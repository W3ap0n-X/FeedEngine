<?php
namespace Qck\FeedEngine\Core\Data;

use Qck\FeedEngine\Manifest;
use \Qck\FeedEngine\Core\Options\OptionEntry;
use \Qck\FeedEngine\Core\Options\OptionField;
abstract class MetaData {
    abstract public function get_name(): string;
    abstract public function get_title(): string;
    abstract public function get_screen(): string|array; // e.g., 'qckfe_feed'
    abstract public function get_meta_type(): string;

    abstract public function get_meta_hook(): array;
    abstract public function get_meta_save_hook(): array;

    abstract public function get_schema(): array;

    public function get_description(): ?string {
        return null;
    }

    public function save_values($obj_id, $values) {
        
        return update_metadata(
            $this->get_meta_type(), 
            $obj_id, 
            '_' . Manifest::PREFIX . '_' . $this->get_name(), 
            $values
        );
    }

    public function get_values($obj_id): array|string {
        return get_metadata($this->get_meta_type(), $obj_id, '_' . Manifest::PREFIX . '_' . $this->get_name(), true);
    }

    public function get_defaults(){
        $defaults = [];
        foreach ($this->get_schema() as $entry) {

            $defaults = $this->set($defaults, $entry->key , $entry->default);
        }
        return $defaults;
    }

    /**
     * Helper to find a specific field definition by its key.
     */
    public function get_field_definition( string $key ): null|OptionEntry|OptionField {

        $fields = $this->get_schema();
        foreach ($fields as $entry) {
            if($entry->key == $key) {
                return $entry;
            }
        }
        return $fields[$key] ?? null;
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

    public function get($values, $name) {
        
        $entry = $this->get_field_definition($name);

        if ($entry && !empty($entry->path)) {
            // Build the full path: [path_segment, key]
            $full_path = array_merge($entry->path, [$entry->key]);
            return $this->deep_get($values, $full_path, $entry->default);
        }

        // Fallback to top-level or default
        return $values[$name] ?? ($entry->default ?? null);
    }

    public function set($data, $name, $setValue) {
        $entry = $this->get_field_definition( $name );
        $path  = ( $entry && ! empty( $entry->path ) ) ? $entry->path : [];

        // Run the mutation
        $this->deep_set( $data, $path, $name, $setValue );

        // Persistence: This triggers the WordPress update_option filter stack
        return $data;
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
    public function remove($data, $name) {
        
        $entry = $this->get_field_definition( $name );
        $path  = ( $entry && ! empty( $entry->path ) ) ? $entry->path : [];

        $this->deep_unset( $data, $path, $name );
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


    public function get_value_for_entry($obj_id, OptionEntry $entry) {
        $all_data = $this->get_values($obj_id );
        if( !empty($all_data) ) {
            if (empty($entry->path)) {
                return $all_data[$entry->key] ?? $entry->default;
            }
            $current = $all_data;
            foreach ($entry->path as $step) {
                if (isset($current[$step]) && is_array($current[$step])) {
                    $current = $current[$step];
                } else {
                    $updated_data = $this->set($all_data, $entry->key, $entry->default);
                    update_metadata($this->get_meta_type(), $obj_id, '_' . Manifest::PREFIX . '_' . $this->get_name(), $updated_data );
                    return $entry->default; // Path broken, return default
                }
            }
            return $current[$entry->key] ?? $entry->default;
        } else {
            return esc_html($entry->default);
        }
    }

    public function get_value_for_field($obj_id, OptionField $entry) {
        $all_data = $this->get_values($obj_id ); 
        
        // If there's no path, just grab the key from the top level
        if (empty($entry->path)) {
            return $all_data[$entry->key] ?? [];
        }

        // Walk the path
        $current = $all_data;
        foreach ($entry->path as $step) {
            if (isset($current[$step]) && is_array($current[$step])) {
                $current = $current[$step];
            } 
            else {
                return []; // Path broken, return default
            }
        }
        return $current[$entry->key] ?? [];
    }


}