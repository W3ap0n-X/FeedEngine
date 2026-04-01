<?php
namespace Qck\FeedEngine\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Pages\SubPage;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;



class LogViewer extends SubPage implements Actions {

    

    public function __construct(  $parent_slug, $hooks) {
        parent::__construct(  $parent_slug, $hooks );
    }



    /**
     * Return the menu title.
     *
     * @return string
     */
    protected function get_menu_title() {
        return __( "Log Viewer", Manifest::SLUG );
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    protected function get_page_title() {
        return __( Manifest::NAME . ' Logs', Manifest::SLUG );
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
        return Manifest::PREFIX . '_logs';
    }

    /**
     * Register sections.
     */
    public function register_sections() {
        // $this->add_section( new \Qck\FeedEngine\Options\GeneralOptions() );
        $this->add_section( new \Qck\FeedEngine\Options\BentoOptions() );

        

        foreach ($this->sections as  $section) {
            SettingBuilder::build_ui_from_section($this->get_slug(), $section);
        }
    }

}
