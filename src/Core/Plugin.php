<?php
namespace Qck\FeedEngine\Core;
use Qck\FeedEngine\Manifest;

// use Qck\FeedEngine\Core\Options\WP_Options;
// use Qck\FeedEngine\Core\Options\OptionsManager;
use Qck\FeedEngine\Core\Hooks\HooksManager;
use Qck\FeedEngine\Core\Shortcodes\ShortcodeManager;
use Qck\FeedEngine\Core\API\ApiManager;
use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Diagnostics\SiteHealth;
use Qck\FeedEngine\Core\CPT\PostTypeManager;

use Qck\FeedEngine\Pages\SettingsPage;


abstract class Plugin implements Actions {

    
    public $options;

	// public $settings;

	
	protected $hooks;

	
	protected $shortcodes;

	
	protected $post_types;

	
	protected $rest_routes;

    
	protected $plugin_name;

    
	protected $version;





    private static $instance = null;

    public function __construct() {
		$this->version = Manifest::VERSION;
		$this->plugin_name = Manifest::NAME;
		
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	
    public function init() {
        // $this->options = new WP_Options();
		
		$this->rest_routes = new ApiManager();
		$this->hooks = new HooksManager();
		
		$this->shortcodes = new ShortcodeManager();
        
		$this->hooks->load();
		
		$this->hooks->register( new API\SettingsController() );
		$this->shortcodes->register_all();
		
		$this->register_pages();
		// $this->rest_routes->register_endpoints();

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
	}

	abstract protected function add_pages();

	private function register_pages() {
		foreach ( $this->add_pages() as $page ) {
			$this->hooks->register( $page );
		}
	}

}