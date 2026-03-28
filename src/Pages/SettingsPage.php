<?php
namespace Qck\FeedEngine\Pages;

use Qck\FeedEngine\Manifest;

use Qck\FeedEngine\Core\Pages\Components\Page\TopPage;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SettingsPage extends Top_Page implements Actions {

    

    public function __construct( $options , $hooks) {
        
        parent::__construct( $options , $hooks );
    }

    /**
     * Return the actions to register.
     *
     * @return array
     */
    public function get_actions() {
        $actions = array(

        );

        return $actions;
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
     * Register the General Options section.
     */
    private function register_general_options() {
        
    }

    /**
     * Register sections.
     */
    public function register_sections() {
        $this->register_general_options();
    }

}
