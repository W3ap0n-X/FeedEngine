<?php

namespace Qck\FeedEngine\Core\Api;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Standalone\Admin_Notice;
use Qck\FeedEngine\Core\API\BaseController;
use Qck\FeedEngine\Core\Options\WP_Options;

class SettingsController extends BaseController {

    private $options;

    public function __construct(WP_Options $options) {
        // \Qck\FeedEngine\Core\Debug::logDump($options, __METHOD__);
        $this->options = $options;
    }


    public function register_routes() {
        // \Qck\FeedEngine\Core\Debug::logDump('registering routes', __METHOD__);
        register_rest_route($this->get_namespace(), '/settings', [
            'methods'             => 'POST',
            'callback'            => [$this, 'save_settings'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
        \Qck\FeedEngine\Core\Debug::logDump('/settings', __METHOD__);
    }

    public function check_permission(): bool  {
        return current_user_can('manage_options');
    }

    public function save_settings($request) {
        \Qck\FeedEngine\Core\Debug::logDump($request, __METHOD__);
        $params = $request->get_params();
        $updated = false;

        try {
            // 1. Validation Logic
            if ( empty($params) ) {
                throw new \Exception( __( 'No data provided to save.', Manifest::SLUG ) );
            }

            foreach ($params as $key => $value) {
                foreach (Manifest::DEFAULT_OPTIONS as $section_id => $fields) {
                    if (array_key_exists($key, $fields)) {
                        // 2. The Actual Update
                        // We assume $this->options->set() returns true on success
                        $this->options->set($key, sanitize_text_field($value), $section_id);
                        $updated = true;
                    }
                }
            }

            // 3. Success Response
            return new \WP_REST_Response([
                'success' => true,
                'message' => __( display_admin_notices($updated, 'Settings Saved.'), Manifest::SLUG )
            ], 200);

        } catch (\Exception $e) {
            // 4. Error Response (The Safety Net)
            return new \WP_REST_Response([
                'success' => false,
                'message' => display_admin_notices($updated, $e->getMessage())
            ], 400); // 400 Bad Request
        }
    }

    /**
     * Display an admin notice with the given message and type.
     *
     * @param string $message Message to display.
     * @param string $type    Notice type ('success', 'error', or 'warning').
     */
    protected function render_admin_notice( $message, $type ) {
        $notice = new Admin_Notice( $message, $type );
        return $notice->render();
    }

        /**
     * Display admin notices.
     */
    public function display_admin_notices($updated, $msg) {

        // settings_errors();

            if ( $updated === true  ) {
                $this->render_admin_notice(
                    esc_html( __( 'Success: ' , Manifest::SLUG ) ),
                    Admin_Notice::SUCCESS
                );
            } else {
                /** @noinspection SpellCheckingInspection */
                $this->render_admin_notice(
                    esc_html( __( 'An error occurred: ' , Manifest::SLUG ) ),
                    Admin_Notice::ERROR
                );
            }
        
    }
}