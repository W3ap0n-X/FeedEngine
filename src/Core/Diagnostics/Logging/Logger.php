<?php 
namespace Qck\FeedEngine\Core\Diagnostics\Logging;

use Qck\FeedEngine\Manifest;

class Logger {
    private static function get_log_path() {
        $upload_dir = wp_upload_dir();
        $path = $upload_dir['basedir'] . '/' . Manifest::PREFIX . '-logs';
        
        if ( ! file_exists( $path ) ) {
            wp_mkdir_p( $path );
            // Add an index.php and .htaccess for security
            file_put_contents( $path . '/index.php', '' );
            file_put_contents( $path . '/.htaccess', 'Deny from all' );
        }
        
        return $path . '/debug.log';
    }

    public static function log( $message, $title = 'LOG' ) {
        $timestamp = current_time( 'mysql' );
        $formatted = sprintf( "[%s] [%s]: %s\n", $timestamp, $title, self::format_message( $message ) );
        
        error_log( $formatted, 3, self::get_log_path() );
    }

    public static function clear() {
        if ( file_exists( self::get_log_path() ) ) {
            unlink( self::get_log_path() );
        }
    }

    public static function get_contents() {
        $path = self::get_log_path();
        return file_exists( $path ) ? file_get_contents( $path ) : 'No log entries found.';
    }

    private static function format_message( $message ) {
        if ( is_array( $message ) || is_object( $message ) ) {
            return print_r( $message, true );
        }
        return $message;
    }
}


/*

protected function render_log_viewer() {
    $logs = \Qck\FeedEngine\Core\Util\Logger::get_contents();
    ?>
    <div class="qckfe-log-viewer">
        <h3>System Logs</h3>
        <textarea readonly style="width:100%; height:300px; font-family:monospace; background:#f0f0f0;"><?php echo esc_textarea( $logs ); ?></textarea>
        <div style="margin-top: 10px;">
            <button type="button" id="qckfe-clear-logs" class="button button-link-delete">Clear Log File</button>
        </div>
    </div>

    <script>
    jQuery('#qckfe-clear-logs').on('click', function() {
        if ( ! confirm('Are you sure you want to wipe the logs?') ) return;
        
        // Use your existing REST Dispatcher!
        wp.apiFetch({
            path: '<?php echo Manifest::PREFIX; ?>/v1/logs/clear',
            method: 'POST'
        }).then(() => {
            location.reload(); // Refresh to show the empty state
        });
    });
    </script>
    <?php
}


register_rest_route( $this->get_namespace(), '/logs/clear', [
    'methods'             => 'POST',
    'callback'            => function() {
        \Qck\FeedEngine\Core\Util\Logger::clear();
        return new \WP_REST_Response( [ 'success' => true ] );
    },
    'permission_callback' => [$this, 'check_permission'],
]);
*/