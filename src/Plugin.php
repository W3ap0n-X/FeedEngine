<?php
namespace Qck\FeedEngine;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Public\FeedController;

class Plugin extends Core\Plugin {
    

    protected function add_pages() {
		return [
			new \Qck\FeedEngine\Pages\SettingsPage( $this->hooks ),
			new \Qck\FeedEngine\Pages\LogViewer(  Manifest::PREFIX . '_settings' , $this->hooks ),
		];
	}
}