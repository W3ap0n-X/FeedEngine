<?php
namespace Qck\FeedEngine\Hooks;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HookInterface;

class PublicAssets implements HookInterface {
    public function is_filter(): bool { return false ; }
    public function get_hook(): string { return 'admin_enqueue_scripts'; }
    public function get_priority(): int { return 10; }
    public function get_args_count(): int { return 1; }

    public function get_callback(): callable {
        return function() {
            wp_enqueue_style( Manifest::PREFIX . '-feed_preview', Manifest::url() . 'src/assets/css/feed_preview.css' );
            $js_handle = Manifest::PREFIX . '-feed_preview';
            wp_enqueue_script( 
                $js_handle,
                Manifest::url('src/assets/js/feed_preview.js'), 
                ['jquery'], // Added jquery as a dependency since your script uses it
                Manifest::VERSION, 
                true // Move to footer for better performance
            );
                wp_localize_script($js_handle, Manifest::PREFIX . '_vars', [
                'prefix'     => Manifest::PREFIX,
                'rest_url' => esc_url_raw(rest_url(Manifest::PREFIX . '/v1/')),
                'nonce'    => wp_create_nonce('wp_rest'), 
            ]);
        };
    }
}