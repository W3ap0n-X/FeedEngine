<?php

if ( ! defined( 'WPINC' ) ) { die; }

namespace Qck\FeedEngine\Core\Shortcodes;

use Qck\FeedEngine\Manifest;

class ShortcodeManager {
    
    public function register_all() {
        // Scan the Shortcodes directory
        $dir = Manifest::path() . 'src/Shortcodes/';
        $files = glob( $dir . '*.php' );

        foreach ( $files as $file ) {
            $class_name = basename( $file, '.php' );
            $full_class = "\\Qck\\FeedEngine\\Shortcodes\\" . $class_name;

            if ( class_exists( $full_class ) ) {
                $instance = new $full_class();
                
                // Ensure it implements our Interface
                if ( $instance instanceof Shortcode ) {
                    add_shortcode( $instance->get_tag(), [ $instance, 'render' ] );
                }
            }
        }
    }
}