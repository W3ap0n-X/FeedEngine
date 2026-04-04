<?php
namespace Qck\FeedEngine\Core\Api;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;

abstract class BaseController implements Actions {

    
    protected function get_namespace(): string {
        return Manifest::PREFIX . '/v1';
    }

    
    public function get_actions(): array {
        return [
            'rest_api_init' => ['register_routes']
        ];
    }

    
    public function check_permission(): bool {
        return current_user_can('manage_options');
    }

    
    abstract public function register_routes();
}