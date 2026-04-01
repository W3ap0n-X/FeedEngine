<?php
// namespace Qck\FeedEngine\API;

// use Qck\FeedEngine\Core\API\Endpoint;
// // use Qck\FeedEngine\Public\FeedController;

// class Logs implements Endpoint {
    
//     public function get_route(): string { return '/feed'; }
    
//     public function get_methods(): array { return ['GET']; }

//     public function get_args(): array {
//         return [
//             'categories' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
//         ];
//     }

//     public function handle( \WP_REST_Request $request ) {
//         // We call our "Brain" (the Controller) just like the Shortcode does.
//         $controller = new FeedController();
//         return [
//             'success' => true,
//             'html'    => $controller->get_feed( $request->get_params() )
//         ];
//     }

//     public function get_permission_callback(): callable {
//         // __return_true is a built-in WP utility function that literally just returns true.
//         return '__return_true';
//     }
// }