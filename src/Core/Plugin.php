<?php
namespace Qck\FeedEngine\Core;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HooksManager;
use Qck\FeedEngine\Core\Shortcodes\ShortcodeManager;
use Qck\FeedEngine\Core\API\ApiManager;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Diagnostics\SiteHealth;
use Qck\FeedEngine\Core\CPT\PostTypeManager;
use Qck\FeedEngine\Core\Data\BaseMetaBox;
use Qck\FeedEngine\Core\Data\BaseTaxonomy;

abstract class Plugin implements Actions {

    
    public $options;

	// public $settings;

	
	public $hooks;

	
	protected $shortcodes;

	
	protected $post_types;

	
	protected $rest_routes;

    
	protected $plugin_name;

    
	protected $version;


	

	public function debug_end(){
        \Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' -------------------- End --------------------' , 720);
    }

    
    public function __construct() {
		\Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' -------------------- START --------------------' , 720);
        add_action( 'shutdown', array( $this, 'debug_end' ) );
		$this->version = Manifest::VERSION;
		$this->plugin_name = Manifest::NAME;
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	

	
    public function init() {
		\Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' init' , 800);
		
		$this->rest_routes = new ApiManager();
		$this->hooks = new HooksManager();
		
		$this->shortcodes = new ShortcodeManager();
        
		$this->hooks->load();
		
		$this->hooks->register( new API\SettingsController() );
		$this->shortcodes->register_all();
		
		$this->register_pages();

        new SiteHealth();
    }


	public function get_actions():array {
		$actions = [
			 //'plugins_loaded' => array( 'init' ) ,
			 
			//  'action' => array( 'method' ) ,

		];
		return $actions;
	}

	public function register_endpoints() {
		$this->rest_routes->register_endpoints();
		
	}

	public function register_post_types() {
		$this->post_types = new PostTypeManager();
		$this->post_types->register_all();
		$this->register_plugin_meta();
		$this->register_plugin_taxonomies();
		
	}

	abstract protected function add_pages();
	abstract protected function add_plugin_meta();
	abstract protected function add_plugin_taxonomy();

	private function register_pages() {
		foreach ( $this->add_pages() as $page ) {
			$this->hooks->register( $page );
		}
	}

	private function register_plugin_meta() {
		foreach ( $this->add_plugin_meta() as $meta ) {
			if( $meta instanceof BaseMetaBox ) {
				$meta->register();
			}
			
		}
	}

	private function register_plugin_taxonomies() {
		foreach ( $this->add_plugin_taxonomy() as $meta ) {
			if( $meta instanceof BaseTaxonomy ) {
				$meta->register();
			}
			
		}
	}

}