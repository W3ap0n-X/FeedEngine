<?php
namespace Qck\FeedEngine\Core;
/**
 * Activator Class
 * 
 * @since     1.0.0
 */
class Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
		\Qck\FeedEngine\Core\Debug::logDump('activating', __METHOD__);
		// update_option( 'rewrite_rules', '' );
    }

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		\Qck\FeedEngine\Core\Debug::logDump('deactivating', __METHOD__);
		// flush_rewrite_rules();
        // unregister_post_type( 'glave-post' );
	}

}