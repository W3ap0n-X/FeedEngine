<?php
namespace Qck\FeedEngine\Core\Pages\Components;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Pages\TabPage;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;
use Qck\FeedEngine\Core\Diagnostics\Logging\Logger;
use Qck\FeedEngine\Core\Options\CustomCommentTypeOptions;



class CustomCommentTypeSettings extends TabPage implements Actions {

    public function get_menu_title() {
        return 'Settings';
    }

    public function __construct(  $parent_slug, $tab_slug) {
        $this->parent_slug = $parent_slug;
        $this->tab_slug = $tab_slug;
        
    }

    public function get_actions(): array {
        return array(
            'admin_menu'            => array( 'add_page' ),
            'admin_init'            => array( 'register_sections' ),
            'admin_notices'         => array( 'display_admin_notices' ),
            'admin_enqueue_scripts' => array( 'maybe_enqueue_stylesheets'),
        );
    }

    public function maybe_enqueue_stylesheets($hook_suffix ) {
        parent::maybe_enqueue_stylesheets($hook_suffix );
        wp_enqueue_media();
    }




    /**
     * Return the menu icon as a dashicon.
     *
     * @link https://developer.wordpress.org/resource/dashicons/
     *
     * @return string
     */
    // protected function get_icon_url() {
    //     return 'dashicons-shield-alt';
    // }



    /**
     * Register sections.
     */
    public function register_sections() {
        $this->add_section( new CustomCommentTypeOptions($this->get_parent_slug()) );
        foreach ($this->sections as  $section) {
            SettingBuilder::build_ui_from_section($this->get_slug(), $section);
        }
    }

    /**
     * Render Custom HTML for plugin wp-admin page above options sections
     */
    public function content_top() {
        
        $html = <<<HTML
            <h1>TESTME</h1>
        HTML;
        return $html;
    
    }

}
