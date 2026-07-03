<?php
namespace Qck\FeedEngine\Core;

class Activator {

    
    public static function activate() {
		\Qck\FeedEngine\Core\Debug::logDump('activating', __METHOD__);
    }

	
	public static function deactivate() {
		\Qck\FeedEngine\Core\Debug::logDump('deactivating', __METHOD__);
	}

}