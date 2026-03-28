<?php
namespace Qck\FeedEngine\Core\API;

use Qck\FeedEngine\Manifest;

class ApiManager {
    public function register_endpoints() {
        $dir = Manifest::path() . 'Plugin/API/';
        if ( ! is_dir( $dir ) ) return;

        $files = glob( $dir . '*.php' );
        foreach ( $files as $file ) {
            $class_name = basename( $file, '.php' );
            $full_class = "\\Qck\\FeedEngine\\API\\" . $class_name;

            if ( class_exists( $full_class ) ) {
                $endpoint = new $full_class();
                if ( $endpoint instanceof EndpointInterface ) {
                    register_rest_route( Manifest::PREFIX . '/v1', $endpoint->get_route(), [
                        'methods'             => $endpoint->get_methods(),
                        'callback'            => [ $endpoint, 'handle' ],
                        'args'                => $endpoint->get_args(),
                        'permission_callback' => $endpoint->get_permission_callback(),
                    ]);
                }
            }
        }
    }
}