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
        // \Qck\FeedEngine\Core\Debug::logDump( '## SettingsController', __METHOD__ . ' Global Init Check' );
        add_action('admin_init', [$this, 'enqueue_admin_ui_js']);

    }


    public function register_routes() {
        // \Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' register_routes' );
        register_rest_route($this->get_namespace(), '/settings', [
            'methods'             => 'POST',
            'callback'            => [$this, 'save_settings'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
        register_rest_route($this->get_namespace(), '/settings/remove', [
            'methods'             => 'POST',
            'callback'            => [$this, 'remove_value'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
        register_rest_route( $this->get_namespace(), '/logs', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_logs'],
            'args'                => [
                'file' => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
                'count'  => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field' , 'default' => 0],
            ],
            'permission_callback' => function() {
                return '__return_true';
            },
        ]);
        register_rest_route( $this->get_namespace(), '/logs/clear', [
            'methods'             => 'POST',
            'callback'            => function() {
                \Qck\FeedEngine\Core\Diagnostics\Logging\Logger::clear();
                return new \WP_REST_Response( [ 
                    'success' => true ,
                    'message' => __( $this->display_admin_notices(true, 'Logs Cleared'), Manifest::PREFIX ),
                ] );
            },
            'permission_callback' => [$this, 'check_permission'],
        ]);
    }

    public function check_permission(): bool  {
        return current_user_can('manage_options');
        // return true;
    }

    public function save_settings($request) {
        // \Qck\FeedEngine\Core\Debug::logDump($request, __METHOD__);
        \Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' save_settings' );
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

    public function remove_value($request) {
        // \Qck\FeedEngine\Core\Debug::logDump($request, __METHOD__);
        $params = $request->get_params();
        $updated = false;

        try {
            // 1. Validation Logic
            if ( empty($params) ) {
                throw new \Exception( __( 'No data provided to save.', Manifest::PREFIX ) );
            }

            $updated = $this->remove_form_data( $params ) ;
        

            // 3. Success Response
            return new \WP_REST_Response([
                'success' => true,
                'message' => __( $this->display_admin_notices($updated, 'Settings Saved.<br>$params<pre>' . print_r($params,true) . '</pre>' ), Manifest::PREFIX )
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

        
    public function display_admin_notices($updated, $msg) {

        // settings_errors();

            if ( $updated === true  ) {
                return $this->render_admin_notice(
                    esc_html( __( 'Success: ' . $msg, Manifest::PREFIX ) ),
                    AdminNotice::SUCCESS
                );
            } else {
                
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

        
        foreach ( $rows_to_update as $row_name => $data ) {
            $filter_hook = Manifest::PREFIX . "/settings/options/sanitize/{$row_name}";
            if ( has_filter( $filter_hook ) ) {
                \Qck\FeedEngine\Core\Debug::logDump( 'Doing Filter sanitize', __METHOD__ . ' Sanitization' , 750);
                
                $sanitized_options = apply_filters($filter_hook , $data );

                
            }
            else {
                \Qck\FeedEngine\Core\Debug::logDump( 'Hook not found. ' . $filter_hook, __METHOD__ . ' Sanitization' , 250);
                $sanitized_options =  $data ;
            }
            update_option( $row_name, $sanitized_options );
        }

        return ! empty( $rows_to_update );
    }

    
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

    public function remove_form_data( array $rmdata ) {
        $rows_to_update = [];

        foreach ( $rmdata as $parent_key => $target_key ) {
            // Target key is the value: "wpxcb_test-tab_test-ui[group2][range][1]"
            if ( preg_match( '/^([^\[]+)(.+)$/', $target_key, $matches ) ) {
                $option_row  = $matches[1]; 
                $path_string = $matches[2]; // "[group2][range][1]"

                // Convert brackets into a clean path array: ['group2', 'range', '1']
                preg_match_all( '/\[([^\]]+)\]/', $path_string, $path_matches );
                $full_path = $path_matches[1]; 

                if ( empty( $full_path ) ) continue;

                // Group by option row so we can process multiple deletes smoothly
                if ( ! isset( $rows_to_update[$option_row] ) ) {
                    // NOTE: If this is the Meta side, swap get_option with your polymorphic retriever!
                    $rows_to_update[$option_row] = get_option( $option_row, [] );
                }

                // Isolate the final key to destroy (the index '1') from its parent path
                $key = array_pop( $full_path );
                
                $this->deep_unset_logic( $rows_to_update[$option_row], $full_path, $key );
            }
        }

        // Save the newly trimmed arrays back to the database
        foreach ( $rows_to_update as $row_name => $data ) {
            // NOTE: If this is Meta, swap update_option with your polymorphic saver!
            update_option( $row_name, $data );
        }

        return ! empty( $rows_to_update );
    }

    private function deep_unset_logic( array &$data, array $path, string $key ) {
        $temp = &$data;
        
        // Drill down to the parent container holding the target item
        foreach ( $path as $step ) {
            if ( ! isset( $temp[$step] ) || ! is_array( $temp[$step] ) ) {
                return; // The path doesn't exist, nothing to delete
            }
            $temp = &$temp[$step];
        }
        
        // Destroy the target element
        if ( isset( $temp[$key] ) ) {
            unset( $temp[$key] );
            
            // 🚨 CRUCIAL RE-INDEXING STEP FOR REPEATERS
            // If we just deleted index 1 out of [0, 1, 2], the array becomes [0, 2].
            // To prevent layout loops or counting bugs on reload, we normalize sequential rows:
            if ( is_array( $temp ) && $this->is_sequential_array( $temp ) ) {
                $temp = array_values( $temp );
            }
        }
    }

    /**
     * Helper to check if an array's keys are purely sequential numbers.
     */
    private function is_sequential_array( array $arr ): bool {
        if ( empty( $arr ) ) return true;
        return array_keys( $arr ) === range( 0, count( $arr ) - 1 );
    }

    public function get_logs($file) {
        return new \WP_REST_Response([
            'success' => true,
            'message' => __( $this->display_admin_notices(true, 'Logs Retrieved Successfully'), Manifest::PREFIX ),
            'logs' => \Qck\FeedEngine\Core\Diagnostics\Logging\Logger::get_contents(),
        ], 200);
    }

    public function enqueue_admin_ui_js(){
        $js_handle = Manifest::PREFIX . '_admin_ui';
        wp_enqueue_style(
            $js_handle,
            Manifest::url('src/assets/css/admin-ui.css'), 
            [],
            Manifest::VERSION,
        );

        wp_enqueue_script( 
            $js_handle,
            Manifest::url('src/assets/js/admin-ui.js'), 
            ['jquery'], 
            Manifest::VERSION, 
            true 
        );

        wp_localize_script($js_handle, Manifest::PREFIX . '_vars', [
            'prefix'     => Manifest::PREFIX,
            'rest_url' => esc_url_raw(rest_url(Manifest::PREFIX . '/v1/')),
            'nonce'    => wp_create_nonce('wp_rest'), 
        ]);
    }
}