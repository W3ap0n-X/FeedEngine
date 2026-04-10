<?php

namespace Qck\FeedEngine\Shortcodes;
// use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Shortcodes\Shortcode;

class FeedInfo implements Shortcode {

    private $prefix = Manifest::PREFIX . '_';

    private $atts = array(
        'id'=> null,

    );

    public function get_tag(): string { return $this->prefix . 'feed_info'; }

    public function render( $atts, $content = null ): string {
        // This looks exactly like a normal WP callback
        
        
        $a = shortcode_atts( $this->atts, $atts );
        $post_id = isset( $atts['id'] ) ? $atts['id'] : null;
        $output = '';
        
        if ( empty( $a['id'] ) ) { return $output; }
        else {
            $transient = get_transient( 'qckfe_cache_' . $a['id']);
            $output .= \Qck\FeedEngine\Core\Debug::easydump( $transient, "Raw feed Info");
            $settings = get_post_meta( $post_id, '_qckfe_feed_settings', true );
            $output .= \Qck\FeedEngine\Core\Debug::easydump( $settings, 'settings');
            
            // $output .= \Qck\FeedEngine\Core\Debug::easydump( $transient, $a['label']);
        }
        
        // if(isset($a['message'])){
        //     $output .= \Qck\FeedEngine\Core\Debug::easydump( $a['message'], $a['label']);
        // }
        return $output ;


    }

    public function get_name(): string {
        return "Feed Info";
    }
    public function get_description(): string {
        return "Testing";
    }
    public function get_example(): string {
        return "Testing";
    }
}