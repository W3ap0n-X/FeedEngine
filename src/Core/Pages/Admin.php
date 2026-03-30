<?php
namespace Qck\FeedEngine\Core\Pages;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;

use Qck\FeedEngine\Core\Pages\Components\Utility\AdminNotice;
use Qck\FeedEngine\Core\Pages\Components\Utility\SubmitButton;
use Qck\FeedEngine\Core\Pages\Components\Sections\SettingsSection;
use Qck\FeedEngine\Core\Pages\Components\Sections\Section;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;
use Qck\FeedEngine\Core\Options\Options;
// use Qck\FeedEngine\Plugin;
use Qck\FeedEngine\Core\Debug;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Admin implements Actions {

    
    /**
     * @var Section[] Page section objects.
     */
    protected $sections = array();

    /**
     * @var Options An instance of `Options`.
     */
    protected $options;

    /**
     * Admin_Page constructor.
     *
     * @param Options $options An instance of `Options`.
     */
    public function __construct($options , $hooks ) {
        // \Qck\FeedEngine\Core\Debug::logDump($options, __METHOD__);
        $this->options = $options;
        $this->hooks = $hooks;
    }

    /**
     * Return the actions to register.
     *
     * @return array
     */
    public function get_actions(): array {
        return array(
            'admin_menu'            => array( 'add_page' ),
            'admin_init'            => array( 'register_sections' ),
            'admin_notices'         => array( 'display_admin_notices' ),
            'admin_enqueue_scripts' => array( 'maybe_enqueue_stylesheets' ),
        );
    }

    /**
     * Render this admin page.
     */
    public function render() {
        ?>
        
        <div class="wrap" data-prefix="<?php echo Manifest::PREFIX; ?>">
            <h1><?php echo esc_html( $this->get_page_title() ); ?></h1>

            <div id="<?php echo Manifest::PREFIX; ?>_notices"></div>

            <div class="<?php echo Manifest::PREFIX; ?>-admin-content-top">
                <?php echo $this->content_top(); ?>
            </div>

            <form id="<?php echo $this->get_slug(); ?>_form" class="<?php echo Manifest::PREFIX; ?>_admin_form" method="post">
                <?php
                settings_fields( $this->get_slug() );
                do_settings_sections( $this->get_slug() );
                $submit = new SubmitButton( $this->get_slug() );
                ?>
            </form>


            <div class="<?php echo Manifest::PREFIX; ?>-admin-content-bottom">
                <?php echo $this->content_bottom(); ?>
            </div>
        </div>

        <?php
    }

    /**
     * Render Custom HTML for plugin wp-admin page above options sections
     */
    public function content_top() {
        return '';
    }

    /**
     * Render Custom HTML for plugin wp-admin page below options sections
     */
    public function content_bottom() {
        return '';
    }

    /**
     * Display an admin notice with the given message and type.
     *
     * @param string $message Message to display.
     * @param string $type    Notice type ('success', 'error', or 'warning').
     */
    protected function render_admin_notice( $message, $type ) {
        $notice = new Admin_Notice( $message, $type );
        $notice->render();
    }

    /**
     * Display admin notices.
     */
    public function display_admin_notices() {

        if($_GET['page'] == $this->get_slug() ) {
            // settings_errors();
            if ( isset( $_GET['action_result'] ) ) {
                if ( $_GET['action_result'] === 'success' ) {
                    $this->render_admin_notice(
                        esc_html( __( 'Action was performed successfully.', Manifest::SLUG ) ),
                        AdminNotice::SUCCESS
                    );
                } else {
                    /** @noinspection SpellCheckingInspection */
                    $this->render_admin_notice(
                        esc_html( __( 'An error occurred. Couldn\'t perform action.', Manifest::SLUG ) ),
                        AdminNotice::ERROR
                    );
                }
            }
        }
    }

    /**
     * Enqueue stylesheets for all admin pages.
     *
     * @param string $hook_suffix The current admin page.
     */
    public function maybe_enqueue_stylesheets( $hook_suffix ) {
        if ( $hook_suffix == $this->get_page_prefix() . $this->get_slug()  ) {
            $this->enqueue_stylesheets();
        } else {
            return;
        }
    }

    public function enqueue_stylesheets() {
        // 1. CSS
        wp_enqueue_style(
            Manifest::PREFIX . '_admin_page',
            Manifest::url('src/assets/css/admin.css'), // Using the url() method we built
            [],
            Manifest::VERSION
        );

        // 2. JS - Let's use a consistent handle variable
        $js_handle = Manifest::PREFIX . '_admin_page';

        wp_enqueue_script( 
            $js_handle,
            Manifest::url('src/assets/js/admin.js'), 
            ['jquery'], // Added jquery as a dependency since your script uses it
            Manifest::VERSION, 
            true // Move to footer for better performance
        );

        // 3. Localize - Using the SAME handle
        wp_localize_script($js_handle, Manifest::PREFIX . '_vars', [
            '__wp_plugin_prefix'     => Manifest::PREFIX,
            'rest_url' => esc_url_raw(rest_url(Manifest::SLUG . '/v1/')),
            'nonce'    => wp_create_nonce('wp_rest'), 
        ]);
    }

    abstract public function add_page();

    /**
     * Return the menu title.
     *
     * @return string
     */
    abstract protected function get_menu_title();

    /**
     * Return the page title.
     *
     * @return string
     */
    abstract protected function get_page_title();

    /**
     * Return the capability required for this menu to be displayed to the user.
     *
     * @return string
     */
    protected function get_capability() {
        return 'manage_options';
    }

    /**
     * Return page slug.
     *
     * @return string
     */
    abstract protected function get_slug();

    /**
     * Return page prefix.
     *
     * @return string
     */
    abstract protected function get_page_prefix();

    /**
     * Return the URL to the icon to be used for this menu.
     * * @link https://developer.wordpress.org/resource/dashicons/
     *
     * @return string
     */
    protected function get_icon_url() {
        return 'dashicons-admin-generic';
    }

    /**
     * Return the position in the menu order this item should appear.
     *
     * @return int|null
     */
    protected function get_position() {
        return null;
    }

    /**
     * Register sections.
     *
     * Used to add new sections to an admin page.
     *
     * @return void
     */
    abstract public function register_sections();

    /**
     * Create and register a new settings section object.
     *
     * @param string $section_id Section ID.
     * @param array  $properties Section properties.
     *
     * @return SettingsSection
     */
    protected function register_section( $section_id, $properties = array() ) {
        $dump_me = ['id'=>$section_id, 'properties'=>$properties];
        \Qck\FeedEngine\Core\Debug::logDump($dump_me, __METHOD__);
        $section = new SettingsSection( $section_id, $this->get_slug(), $this->options, $properties );

        $this->sections[] = $section;

        register_setting(
            $this->get_slug(),
            Manifest::PREFIX . '_' . $section_id,
            // 'qckfe_general_options',
            array( 'sanitize_callback' => array( $section, 'sanitize' ) )
        );

        return $section;
    }

    /**
     * Create and register a new section object.
     *
     * @param string $section_id Section ID.
     * @param array  $properties Section properties.
     *
     * @return Section
     */
    protected function register_presentation_section( $section_id, $properties = array() ) {
        $section = new Section( $section_id, $this->get_slug(), $this->options, $properties );
        $this->sections[] = $section;

        return $section;
    }

}