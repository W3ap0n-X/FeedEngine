<?php
namespace Qck\FeedEngine\Core\Hooks;


// Prevent direct access to files
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;

class HooksManager {

    /**
     * Discovery: Automatically scans the /Hooks folder and registers classes.
     */
    public function load() {
        $dir = Manifest::path() . 'src/Hooks/';
        if ( ! is_dir( $dir ) ) return;

        $files = glob( $dir . '*.php' );

        foreach ( $files as $file ) {
            $class_name = basename( $file, '.php' );
            $full_class = "\\Qck\\FeedEngine\\Hooks\\" . $class_name;

            if ( class_exists( $full_class ) ) {
                // Instantiate and hand off to your existing register logic
                $this->register( new $full_class() );
            }
        }
    }

    /**
     * Register an object.
     *
     * @param object $object
     */
    public function register( $object ) {
        // \Qck\FeedEngine\Core\Debug::logDump($object, __METHOD__);
        if ( $object instanceof Actions ) {
            $this->register_actions( $object );
        }

        if ( $object instanceof Filters ) {
            $this->register_filters( $object );
        }
    }

    /**
     * Register the actions of the given object.
     *
     * @param object $object
     */
    private function register_actions( $object ) {
        $actions = $object->get_actions();

        foreach ( $actions as $action_name => $action_details ) {
            $method        = $action_details[0];
            $priority      = self::default_value( $action_details[1], 10 );
            $accepted_args = self::default_value( $action_details[2], 1 );

            add_action(
                $action_name,
                array( $object, $method ),
                $priority,
                $accepted_args
            );
        }
    }

    /**
     * Register the filters of the given object.
     *
     * @param object $object
     */
    private function register_filters( $object ) {
        $filters = $object->get_filters();

        foreach ( $filters as $filter_name => $filter_details ) {
            $method        = $filter_details[0];
            $priority      = self::default_value( $filter_details[1], 10 );
            $accepted_args = self::default_value( $filter_details[2], 1 );

            add_filter(
                $filter_name,
                array( $object, $method ),
                $priority,
                $accepted_args
            );
        }
    }

	/**
     * Return the given value if it's set, otherwise return the default one.
     *
     * @param mixed $value
     * @param mixed $default
     *
     * @return mixed
     */
    public static function default_value( &$value, $default ) {
        if ( isset( $value ) ) {
            return $value;
        }

        if ( isset( $default ) ) {
            return $default;
        }

        return null;
    }

}