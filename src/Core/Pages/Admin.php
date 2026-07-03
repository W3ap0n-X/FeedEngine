<?php
namespace Qck\FeedEngine\Core\Pages;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;
use Qck\FeedEngine\Core\Pages\Components\Utility\AdminNotice;
use Qck\FeedEngine\Core\Pages\Components\Utility\SubmitButton;
use Qck\FeedEngine\Core\Pages\Components\Sections\SettingsSection;
use Qck\FeedEngine\Core\Options\OptionSection;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Admin implements Actions {

    
    protected $sections = array();

    protected $hooks;

    
    public function __construct( $hooks ) {
        \Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' ## ' . $this::class , 900);
        $this->hooks = $hooks;
        $this->boot_sections();
    }

    
    public function get_actions(): array {
        return array(
            'admin_menu'            => array( 'add_page' ),
            // 'init'            => array( 'register_sections' ),
            'admin_notices'         => array( 'display_admin_notices' ),
            'admin_enqueue_scripts' => array( 'maybe_enqueue_stylesheets' ),
        );
    }

    
    public function render() {
        $prefix = esc_attr(Manifest::PREFIX);
        $title = esc_html( $this->get_page_title() );
        ob_start();
        if(count($this->sections) > 0) {
            ?>
                <form id="<?php echo $this->get_slug(); ?>_form" class="<?php echo Manifest::PREFIX; ?>_admin_form" method="post">
                    <?php
                        settings_fields( $this->get_slug() );
                        do_settings_sections( $this->get_slug() );
                        $submit = new SubmitButton( $this->get_slug() );
                    ?>
                </form>
            <?php
        }
        $content = ob_get_clean();
        $top = $this->content_top();
        $bottom = $this->content_bottom();

        $html = <<<HTML
            
            <div class="wrap data-wrap" data-prefix="{$prefix}">
                <h1>{$title}</h1>
                <div id="{$prefix}_notices"></div>
                <div class="{$prefix}-admin-content-top">
                    {$top}
                </div>
                {$content}
                <div class="{$prefix}-admin-content-bottom">
                    {$bottom}
                </div>
            </div>
        HTML;
        echo $html;

        
    }

    
    public function content_top() {
        return '';
    }

    
    public function content_bottom() {
        return '';
    }

    
    protected function render_admin_notice( $message, $type ) {
        $notice = new AdminNotice( $message, $type );
        $notice->render();
    }

    /* Not currently implemented */
    public function display_admin_notices() {
        if(!empty($_GET['page']) && $_GET['page'] == $this->get_slug() ) {
            if ( isset( $_GET['action_result'] ) ) {
                if ( $_GET['action_result'] === 'success' ) {
                    $this->render_admin_notice(
                        esc_html( __( 'Action was performed successfully.', Manifest::SLUG ) ),
                        AdminNotice::SUCCESS
                    );
                } else {
                    $this->render_admin_notice(
                        esc_html( __( 'An error occurred. Couldn\'t perform action.', Manifest::SLUG ) ),
                        AdminNotice::ERROR
                    );
                }
            }
        }
    }

    
    public function maybe_enqueue_stylesheets( $hook_suffix ) {
        if ( str_contains( $hook_suffix, $this->get_page_prefix() .  $this->get_slug() ) ) {
            $this->enqueue_stylesheets();
        } else {
            
            return;
        }
    }

    public function enqueue_stylesheets() {
        wp_enqueue_style(
            Manifest::PREFIX . '_admin_page',
            Manifest::url('src/assets/css/admin.css'), 
            [],
            Manifest::VERSION
        );
        
        $js_handle = Manifest::PREFIX . '_admin_page';

        wp_enqueue_script( 
            $js_handle,
            Manifest::url('src/assets/js/admin.js'), 
            ['jquery'], 
            Manifest::VERSION, 
            true 
        );

        wp_localize_script($js_handle, Manifest::PREFIX . '_vars', [
            'prefix'     => Manifest::PREFIX,
            'rest_url' => esc_url_raw(rest_url(Manifest::PREFIX . '/v1/')),
            'nonce'    => wp_create_nonce('wp_rest'), 
        ]);
    }

    abstract public function add_page();

    
    abstract protected function get_menu_title();

    
    abstract protected function get_page_title();

    
    protected function get_capability() {
        return 'manage_options';
    }

    
    abstract protected function get_slug();

    
    abstract protected function get_page_prefix();

    
    protected function get_icon_url() {
        return 'dashicons-admin-generic';
    }

    
    protected function get_position() {
        return null;
    }

    
    abstract public function register_sections();


    public function boot_sections() {

        $this->register_sections();
        

        foreach ( $this->sections as  $section) {
            SettingBuilder::build_ui_from_section($this->get_slug(), $section);
        }
    }

    protected function add_section( $option_section ) {
        \Qck\FeedEngine\Core\Debug::logDump( $option_section->get_title(), __METHOD__ . ' ## ' . $this::class , 950);
        if ( ! ( $option_section instanceof OptionSection ) ) {
            return;
        }
        $section = new SettingsSection( 
            $option_section->get_db_row(), 
            $this->get_slug(), 
            $option_section, 
            ['title' => $option_section->get_title(), 'description' => $option_section->get_description()] 
        );
        $this->sections[] = $section;

        register_setting(
            $this->get_slug(),
            $option_section->get_db_row(),
            array( 'sanitize_callback' => array( $section, 'sanitize' ) )
        );
        
        return $section;
    }

}