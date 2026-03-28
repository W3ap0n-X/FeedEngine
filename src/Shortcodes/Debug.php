<?php

namespace Qck\FeedEngine\Shortcodes;
// use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Core\Shortcodes\Shortcode;

class Debug implements Shortcode {

    private $prefix = Manifest::PREFIX . '_';



    private $atts = array(
        'plugin_info' => false;
        'label' => null
        'message' => 'null',

    );

    public function get_tag(): string { return $prefix . 'debug'; }

    public function render( $atts, $content = null ): string {
        // This looks exactly like a normal WP callback
        $a = shortcode_atts( $this->atts, $atts );
        $output = ''
        if($a['plugin_info'] !== false){
            $output .= Qck\FeedEngine\Core\Debug::easydump('Plugin Details', Qck\FeedEngine\Core\Debug::details());
        }
        if(isset($a['message'])){
            $output .= Qck\FeedEngine\Core\Debug::easydump($a['label'], $a['message']);
        }
        return $output ;


    }
}