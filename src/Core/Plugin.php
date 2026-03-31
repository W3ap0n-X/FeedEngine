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

use Qck\FeedEngine\Pages\SettingsPage;


abstract class Plugin implements Actions {

    /**
	 * Options
	 * 
	 * @since    1.0.0
	 * @access   public
     * @var 	 WP_Options An instance of the `Options` class.
     */
    public $options;

	// public $settings;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      HooksManager    >>> $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $hooks;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ShortcodeManager    >>> $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $shortcodes;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ApiManager    >>> $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $rest_routes;

    /**
	 * plugin_name
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    >>> $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

    /**
	 * Glave Version.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    >>> $version    The current version of the plugin.
	 */
	protected $version;





    private static $instance = null;

    public function __construct() {
		$this->version = Manifest::VERSION;
		$this->plugin_name = Manifest::NAME;
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	/**
     * Initialize the plugin once activated plugins have been loaded.
     */
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
			 /* New actions go here */
			//  'action' => array( 'method' ) ,

		];
		return $actions;
	}

	public function register_endpoints() {
		$this->rest_routes->register_endpoints();
	}

	abstract protected function add_pages();

	private function register_pages() {
		foreach ( $this->add_pages() as $page ) {
			$this->hooks->register( $page );
		}
	}

}