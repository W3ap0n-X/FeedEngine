<?php
namespace Qck\FeedEngine\Hooks;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HookInterface;

class PublicAssets implements HookInterface {
    public function get_hook(): string { return 'wp_enqueue_scripts'; }
    public function get_priority(): int { return 10; }
    public function get_args_count(): int { return 1; }

    public function get_callback(): callable {
        return function() {
            wp_enqueue_style( Manifest::PREFIX . '-main', Manifest::url() . 'src/assets/css/public.css' );
            wp_enqueue_script( 
                Manifest::PREFIX . '-main',
                Manifest::url('src/assets/js/public.js'), 
                ['jquery'], // Added jquery as a dependency since your script uses it
                Manifest::VERSION, 
                true // Move to footer for better performance
            );
        };
    }
}