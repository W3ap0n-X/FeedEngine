<?php
namespace Qck\FeedEngine\Hooks;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HookInterface;

class _FeedRenderPreview implements HookInterface {
    public function is_filter(): bool { return true ; }
    public function get_hook(): string { return 'the_content'; }
    public function get_priority(): int { return 10; }
    public function get_args_count(): int { return 1; }

    public function get_callback(): callable {
        return function($content) {
            // Only run this on our specific CPT
            return \Qck\FeedEngine\Core\Debug::logDump( $content, __METHOD__ . ' $content');
            if ( is_singular( 'qckfe_feed' ) ) {
                $post_id = get_the_ID();
                return 'test';

            }
            

            
        };
    }
}