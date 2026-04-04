<?php
namespace Qck\FeedEngine\Core;

class Activator {

    
    public static function activate() {
		\Qck\FeedEngine\Core\Debug::logDump('activating', __METHOD__);
		// update_option( 'rewrite_rules', '' );
    }

	
	public static function deactivate() {
		\Qck\FeedEngine\Core\Debug::logDump('deactivating', __METHOD__);
		// flush_rewrite_rules();
        // unregister_post_type( 'glave-post' );
	}

}