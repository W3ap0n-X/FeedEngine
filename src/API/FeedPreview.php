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
        // We call our "Brain" (the Controller) just like the Shortcode do
        // es.
        $params = $request->get_params();
        // \Qck\FeedEngine\Core\Debug::logDump( $params, __METHOD__);
        $controller = new FeedController();
        // $items = [];
        $items = $controller->run_adapter_test_logic($params);
        // $return = [];
        // if(in_array('manual', $items)) {
        //     $return['manual'] = $items['manual'];
        // }
        // if(in_array('automatic', $items)) {
        //     $return['automatic'] = $items['automatic'];
        // }

        
        // \Qck\FeedEngine\Core\Debug::logDump( $items, __METHOD__ . ' $items');
        // \Qck\FeedEngine\Core\Debug::logDump( $items['automatic'], __METHOD__ . ' $items');
        // \Qck\FeedEngine\Core\Debug::logDump( $items->automatic, __METHOD__ . ' $items');

        // \Qck\FeedEngine\Core\Debug::logDump( $return, __METHOD__ . ' $return');


        return [
            'success' => true,
            'html'    => ['manual' => $items['manual'] ?? [],'automatic' => $items['automatic'] ?? []],
            'params' => $params
        ];
    }

    public function get_permission_callback(): callable {
        // __return_true is a built-in WP utility function that literally just returns true.
        return function(){ return current_user_can('manage_options'); };
    }
}