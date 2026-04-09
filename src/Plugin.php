<?php
namespace Qck\FeedEngine;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Public\FeedController;

class Plugin extends Core\Plugin {
    private static $instance = null;
	public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function add_pages() {
		return [
			new \Qck\FeedEngine\Pages\SettingsPage( $this->hooks ),
			new \Qck\FeedEngine\Pages\LogViewer(  Manifest::PREFIX . '_settings' , $this->hooks ),
		];
	}
}