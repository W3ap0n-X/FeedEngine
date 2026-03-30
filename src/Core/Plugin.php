<?php
namespace Qck\FeedEngine\Core;
use Qck\FeedEngine\Manifest;

use Qck\FeedEngine\Core\Options\WP_Options;
use Qck\FeedEngine\Core\Hooks\HooksManager;
use Qck\FeedEngine\Core\Shortcodes\ShortcodeManager;
use Qck\FeedEngine\Core\API\ApiManager;
use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Core\Hooks\Actions;

use Qck\FeedEngine\Pages\SettingsPage;
use Qck\FeedEngine\Public\FeedController;

class Plugin implements Actions {

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
		\Qck\FeedEngine\Core\Debug::logDump('__constructing', __METHOD__);
		$this->version = Manifest::VERSION;
		$this->plugin_name = Manifest::NAME;
		add_action( 'plugins_loaded', array( $this, 'init' ) );
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
		\Qck\FeedEngine\Core\Debug::logDump('initializing', __METHOD__);
        $this->options = new WP_Options();
		$this->rest_routes = new ApiManager();
		$this->hooks = new HooksManager();
		$this->shortcodes = new ShortcodeManager();
        $this->hooks->register($this);
		$this->rest_routes->register_endpoints();
		
		$this->register_api_routes();
		$this->register_pages();
		$this->shortcodes->register_all();

        
    }


	public function get_actions() {
		$actions = [
			 //'plugins_loaded' => array( 'init' ) ,
			 /* New actions go here */
			//  'action' => array( 'method' ) ,

		];
		return $actions;
	}

	private function register_pages() {
		\Qck\FeedEngine\Core\Debug::logDump('register pages', __METHOD__);
		$pages = [
			new \Qck\FeedEngine\Pages\SettingsPage( $this->options, $this->hooks ),
		];

		foreach ( $pages as $page ) {
			$this->hooks->register( $page );
		}
	}

	private function register_api_routes() {
		\Qck\FeedEngine\Core\Debug::logDump('register routes', __METHOD__);
		$routes = [
			new API\SettingsController( $this->options ),
		];

		foreach ( $routes as $routes ) {
			$this->hooks->register( $routes );
		}
	}
}