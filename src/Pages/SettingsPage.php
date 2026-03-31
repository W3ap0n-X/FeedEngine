<?php
namespace Qck\FeedEngine\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Pages\TopPage;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;



class SettingsPage extends TopPage implements Actions {

    

    public function __construct(  $hooks) {
        parent::__construct(  $hooks );
    }



    /**
     * Return the menu title.
     *
     * @return string
     */
    protected function get_menu_title() {
        return __( Manifest::NAME, Manifest::SLUG );
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    protected function get_page_title() {
        return __( Manifest::NAME . ' Settings', Manifest::SLUG );
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
     * Return page slug.
     *
     * @return string
     */
    public function get_slug() {
        return Manifest::PREFIX . '_settings';
    }

    /**
     * Register sections.
     */
    public function register_sections() {
        $this->add_section( new \Qck\FeedEngine\Options\GeneralOptions() );
        $this->add_section( new \Qck\FeedEngine\Options\BentoOptions() );

        

        foreach ($this->sections as  $section) {
            SettingBuilder::build_ui_from_section($this->get_slug(), $section);
        }
    }

}
