<?php

if ( ! defined( 'WPINC' ) ) { die; }

namespace Qck\FeedEngine\Core\Shortcodes;

class ShortcodeManager {
    
    private $shortcodes = [];

    public function add( Shortcode $shortcode ) {
        $this->shortcodes[] = $shortcode;
    }

    public function register() {
        foreach ( $this->shortcodes as $shortcode ) {
            add_shortcode( $shortcode->get_tag(), [ $shortcode, 'render' ] );
        }
    }
}