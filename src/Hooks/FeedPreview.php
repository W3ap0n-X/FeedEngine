<?php
namespace Qck\FeedEngine\Hooks;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HookInterface;

class FeedPreview implements HookInterface {
    public function is_filter(): bool { return false ; }
    public function get_hook(): string { return 'edit_form_after_title'; }
    public function get_priority(): int { return 10; }
    public function get_args_count(): int { return 1; }

    public function get_callback(): callable {
        return function($post) {
            // Only run this on our specific CPT
            
            \Qck\FeedEngine\Core\Debug::easydump( 'test', __METHOD__);

            if ( $post->post_type !== 'qckfe-feed' ) {
                return;
            }


            $html = <<<HTML
                <div class="qckfe-main-preview-container">
                    <h2>Feed Preview</h2>
                    <div id="qckfe-preview-loading-overlay" style="display:none;">Scanning...</div>

                    <div id="qckfe-preview-results">

                    </div>

                        
                    <button type="button" id="qckfe-refresh-preview" class="button">
                        Refresh List
                    </button>
                </div>
                

            
            HTML;
            $js = '
                <script>

                </script>
            ';
            
            echo $html;
        };
    }
}