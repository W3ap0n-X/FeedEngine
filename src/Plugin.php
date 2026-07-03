<?php
namespace Qck\FeedEngine;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Public\FeedController;

class Plugin extends Core\Plugin {
    private static $instance = null;
    public function __construct(){
        
        if ( !is_null( self::$instance ) ) {
            // return self::$instance;
            $trace = wp_debug_backtrace_summary();
            \Qck\FeedEngine\Core\Debug::logDump( $trace, __METHOD__ . ' DUPLICATE BOOT ATTEMPT DETECTED! Called by: ', 0);
            return;
        }
        self::$instance = $this;
        parent::__construct();
        

    }

    protected function add_pages() {
		return [
			new \Qck\FeedEngine\Pages\SettingsPage( $this->hooks ),
            new \Qck\FeedEngine\Pages\ShopifyIntegration( Manifest::PREFIX . '_settings' , $this->hooks ),
			new \Qck\FeedEngine\Pages\LogViewer(  Manifest::PREFIX . '_settings' , $this->hooks ),
		];
	}

    protected function add_plugin_meta() {
    
        $meta = [
			// new \WPX\XCodeBase\CPT\MetaBoxes\TestUserMeta(),
            new \Qck\FeedEngine\CPT\MetaBoxes\FeedTaxonomyImages(),
            // new \WPX\XCodeBase\CPT\MetaBoxes\ClippetLanguageSupport(),
            // new \WPX\XCodeBase\CPT\MetaBoxes\TestMeta(),
            // new \WPX\XCodeBase\CPT\MetaBoxes\AuxDemo(),
		];

		return $meta;
	}

    protected function add_plugin_taxonomy() {
    
        $meta = [
			// new \WPX\XCodeBase\CPT\MetaBoxes\TestUserMeta(),
            // new \WPX\XCodeBase\CPT\Taxonomy\PluginTag(),
            // new \WPX\XCodeBase\CPT\Taxonomy\ClippetLanguage(),
            // new \WPX\XCodeBase\CPT\MetaBoxes\TestMeta(),
            // new \WPX\XCodeBase\CPT\MetaBoxes\AuxDemo(),
		];

		return $meta;
	}


}