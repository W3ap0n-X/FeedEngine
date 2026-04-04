<?php
namespace Qck\FeedEngine\API;

use Qck\FeedEngine\Core\API\Endpoint;
use Qck\FeedEngine\Public\FeedController;

class FeedPreview implements Endpoint {
    
    public function get_route(): string { return '/feed/preview'; }
    
    public function get_methods(): array { return ['POST']; }

    public function get_args(): array {
        return [
            // 'total' => => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
            // 'categories' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
        ];
    }

    public function handle( \WP_REST_Request $request ) {
        // We call our "Brain" (the Controller) just like the Shortcode does.
        $controller = new FeedController();
        $items = [];
        $items[] = $controller->run_adapter_test_logic();

        return [
            'success' => true,
            'html'    => $items,
        ];
    }

    public function get_permission_callback(): callable {
        // __return_true is a built-in WP utility function that literally just returns true.
        return function(){ return current_user_can('manage_options'); };
    }
}