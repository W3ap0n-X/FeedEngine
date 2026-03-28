<?php

namespace Qck\FeedEngine\Core\Install;

use Qck\FeedEngine\Manifest;

class Uninstall {

    /**
     * Fired when the "Delete" link is clicked in the WP Admin.
     */
    public static function cleanup() {
        // 1. Security check: Ensure WP is actually the one calling this
        if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
            exit;
        }

        // 2. Access the options (We use a standard delete_option here)
        foreach ( Manifest::DEFAULT_OPTIONS as $section_id => $fields ) {
            delete_option( Manifest::PREFIX . '_' . $section_id );
        }

        // 3. Optional: Clean up any custom database tables or transients
        delete_transient( Manifest::PREFIX . '_feed_cache' );
    }
}