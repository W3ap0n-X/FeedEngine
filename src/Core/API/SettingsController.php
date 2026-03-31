<?php

namespace Qck\FeedEngine\Core\Api;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;

use Qck\FeedEngine\Core\API\BaseController;
use Qck\FeedEngine\Core\Pages\Components\Utility\AdminNotice;

class SettingsController extends BaseController {


    public function __construct() {

    }


    public function register_routes() {
        register_rest_route($this->get_namespace(), '/settings', [
            'methods'             => 'POST',
            'callback'            => [$this, 'save_settings'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
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
                    esc_html( __( 'Success: ' . $msg, Manifest::PREFIX ) ),
                    AdminNotice::SUCCESS
                );
            } else {
                /** @noinspection SpellCheckingInspection */
                return $this->render_admin_notice(
                    esc_html( __( 'An error occurred: ' . $msg , Manifest::PREFIX ) ),
                    AdminNotice::ERROR
                );
            }
        
    }

    public function persist_form_data( array $params ) {
        $rows_to_update = [];

        foreach ( $params as $raw_key => $value ) {
            // 1. Extract the Root Row (e.g., 'qckfe_general_options') 
            // and the remaining path (e.g., '[processing][debug]')
            if ( preg_match( '/^([^\[]+)(.+)$/', $raw_key, $matches ) ) {
                $option_row  = $matches[1]; 
                $path_string = $matches[2]; // e.g., "[processing][debug]"

                // 2. Convert "[processing][debug]" into a clean array: ['processing', 'debug']
                preg_match_all( '/\[([^\]]+)\]/', $path_string, $path_matches );
                $full_path = $path_matches[1]; 

                if ( empty( $full_path ) ) continue;

                // 3. Group by row so we only save once per row
                if ( ! isset( $rows_to_update[$option_row] ) ) {
                    $rows_to_update[$option_row] = get_option( $option_row, [] );
                }

                // 4. Use the "Deep Set" logic to place the value
                // Note: We'll separate the last element as the 'key'
                $key = array_pop( $full_path );
                $this->deep_set_logic( $rows_to_update[$option_row], $full_path, $key, $value );
            }
        }

        // 5. Save each row once
        foreach ( $rows_to_update as $row_name => $data ) {
            update_option( $row_name, $data );
        }

        return ! empty( $rows_to_update );
    }

    /**
     * A local version of the deep_set logic for the controller
     */
    private function deep_set_logic( array &$data, array $path, string $key, $value ) {
        $temp = &$data;
        foreach ( $path as $step ) {
            if ( ! isset( $temp[$step] ) || ! is_array( $temp[$step] ) ) {
                $temp[$step] = [];
            }
            $temp = &$temp[$step];
        }
        
        // Handle boolean conversion for checkboxes ('1' or '0')
        $temp[$key] = ( '1' === $value ) ? true : ( ( '0' === $value ) ? false : $value );
    }
}