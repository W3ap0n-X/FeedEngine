<?php
namespace Qck\FeedEngine\Core\Api;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;

abstract class BaseController implements Actions {

    /**
     * The API Namespace (e.g., qck-feed-engine/v1)
     */
    protected function get_namespace(): string {
        return Manifest::SLUG . '/v1';
    }

    /**
     * Interface Requirement: Hook into WordPress
     */
    public function get_actions(): array {
        return [
            'rest_api_init' => ['register_routes']
        ];
    }

    /**
     * Default Permission: Only admins can touch the API
     * Child classes can override this if they need "Subscriber" access.
     */
    public function check_permission(): bool {
        return current_user_can('manage_options');
    }

    /**
     * Force child classes to define their routes
     */
    abstract public function register_routes();
}