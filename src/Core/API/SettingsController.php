<?php

namespace Qck\FeedEngine\Core\Api;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Utility\AdminNotice;
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
        // \Qck\FeedEngine\Core\Debug::logDump('/settings', __METHOD__);
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
                throw new \Exception( __( 'No data provided to save.', Manifest::PREFIX ) );
            }

            $updated = $this->persist_form_data( $params ) ;
            
            // foreach ($params as $key => $value) {
            //     foreach (Manifest::DEFAULT_OPTIONS as $section_id => $fields) {
            //         if (array_key_exists($key, $fields)) {
            //             // 2. The Actual Update
            //             // We assume $this->options->set() returns true on success
            //             $this->options->set($key, sanitize_text_field($value), $section_id);
            //             $updated = true;
            //         }
            //     }
            // }

            // 3. Success Response
            return new \WP_REST_Response([
                'success' => true,
                'message' => __( $this->display_admin_notices($updated, 'Settings Saved.'), Manifest::PREFIX )
            ], 200);

        } catch (\Exception $e) {
            // 4. Error Response (The Safety Net)
            return new \WP_REST_Response([
                'success' => false,
                'message' => $this->display_admin_notices($updated, $e->getMessage())
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
        $notice = new AdminNotice( $message, $type );
        return $notice->renderHtml();
    }

        /**
     * Display admin notices.
     */
    public function display_admin_notices($updated, $msg) {

        // settings_errors();

            if ( $updated === true  ) {
                return $this->render_admin_notice(
                    esc_html( __( 'Success: ' , Manifest::PREFIX ) ),
                    AdminNotice::SUCCESS
                );
            } else {
                /** @noinspection SpellCheckingInspection */
                return $this->render_admin_notice(
                    esc_html( __( 'An error occurred: ' , Manifest::PREFIX ) ),
                    AdminNotice::ERROR
                );
            }
        
    }

    public function persist_form_data( array $params ) {
        try {
            foreach ( $params as $raw_key => $value ) {
                \Qck\FeedEngine\Core\Debug::logDump(['key' => $raw_key, 'value' => $value], __METHOD__);
                // Regex magic: matches 'prefix_options' and 'debug' from 'prefix_options[debug]'
                if ( preg_match( '/^([^\[]+)\[([^\]]+)\]$/', $raw_key, $matches ) ) {
                    $option_row = $matches[1]; // e.g., qckfe_general_options
                    $data_key   = $matches[2]; // e.g., debug

                    // 1. Get current state
                    $current_data = get_option( $option_row, [] );

                    // 2. Update specific index (handling the checkbox boolean)
                    $current_data[ $data_key ] = ( '1' === $value || true === $value );

                    // 3. Save it back
                    update_option( $option_row, $current_data );
                    
                    \Qck\FeedEngine\Core\Debug::logDump("Saved to $option_row", $data_key);
                }
                return true;
            }
        } catch (\Exception $e) {
            \Qck\FeedEngine\Core\Debug::logDump($e, __METHOD__);
            return false;
        }
    
    
    }
}