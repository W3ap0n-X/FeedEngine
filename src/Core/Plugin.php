<?php
namespace Qck\FeedEngine\Core;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Loader;
use Qck\FeedEngine\Core\Hooks\HooksManager;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Options\WP_Options;
use Qck\FeedEngine\Admin\SettingsPage;
use Qck\FeedEngine\Public\FeedController;

class Plugin {

    /**
	 * Glave Options
	 * 
	 * @since    1.0.0
	 * @access   public
     * @var 	 Options An instance of the `Options` class.
     */
    public $options;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      HooksManager    >>> $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
        $this->require_files();
        add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'wp_after_admin_bar_render', array( $this, 'ed_setup' ) );
	}

    public function ed_setup(){
		add_action( 'easydump', array( $this, 'easydump' ) , 10, 2);
		
	}

	public function easydump( $var, $label = null) {
		echo (isset($label) ? '<h4>' . $label . '</h4>' : '') . '<pre>' . print_r($var, true) . '</pre>';
	}

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // public function run() {
    //     new SettingsPage();
    //     new FeedController();
    // }

	private function get_actions() {
		$actions = [];
		return $actions;
	}

	    /**
     * Initialize the plugin once activated plugins have been loaded.
     */
    public function init() {
        $this->options = new WP_Options();
		$this->loader = new HooksManager();
		$settings_page = new SettingsPage( $this->options, $this->hooks_manager );


		$this->loader->register( $settings_page );

        
    }
}