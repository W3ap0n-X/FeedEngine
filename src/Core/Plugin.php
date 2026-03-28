<?php
namespace Qck\FeedEngine;
use Qck\FeedEngine\Manifest;

use Qck\FeedEngine\Core\Options\WP_Options;
use Qck\FeedEngine\Core\Hooks\HooksManager;
use Qck\FeedEngine\Core\Shortcodes\ShortcodeManager;
use Qck\FeedEngine\Core\API\ApiManager;

use Qck\FeedEngine\Pages\SettingsPage;
use Qck\FeedEngine\Public\FeedController;

class Plugin {

    /**
	 * Options
	 * 
	 * @since    1.0.0
	 * @access   public
     * @var 	 WP_Options An instance of the `Options` class.
     */
    public $options;

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
        $this->options = new WP_Options();
		$this->rest_routes = new ApiManager();
		$this->hooks = new HooksManager();
		$this->shortcodes = new ShortcodeManager();

		$this->rest_routes->register_endpoints();
		$this->hooks->register($this);
		register_api_routes();
		$this->register_pages();
		$this->shortcodes->register_all();

        
    }


	private function get_actions() {
		$actions = [
			 'plugins_loaded' => array( 'init' ) ,
			 /* New actions go here */
			//  'action' => array( 'method' ) ,

		];
		return $actions;
	}

	private function register_pages() {
		$pages = [
			new Pages\SettingsPage( $this->options, $this->hooks ),
		];

		foreach ( $pages as $page ) {
			$this->hooks->register( $page );
		}
	}

	private function register_api_routes() {
		$routes = [
			new Core\API\SettingsController( $this->options ),
		];

		foreach ( $routes as $routes ) {
			$this->hooks->register( $routes );
		}
	}
}