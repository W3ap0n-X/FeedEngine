<?php
namespace Qck\FeedEngine\Pages;

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Pages\TopPage;
use Qck\FeedEngine\Core\Options\WP_Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;
use Qck\FeedEngine\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SettingsPage extends TopPage implements Actions {

    

    public function __construct( $options , $hooks) {
        parent::__construct( $options , $hooks );
    }

    // /**
    //  * Return the actions to register.
    //  *
    //  * @return array
    //  */
    // public function get_actions() {
    //     $actions = parent::get_actions();

    //     return $actions;
    // }



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
        // $current_data = $this->options->get('general_options');
        // \Qck\FeedEngine\Core\Debug::logDump($current_data, 'UI Data Check');
        $general_options_section = $this->register_section(
            'general_options',
            array( 'title' => __( 'General Options', Manifest::PREFIX ) )
        );

        $section_debug = $general_options_section->add_field(
            array( 'label'  => __( 'Debug', Manifest::PREFIX ) )
        );

        $section_debug->add_element(
            Element::CHECKBOX_ELEMENT,
            array(
                'label' => __( 'Debug Mode', Manifest::PREFIX ),
                'name'  => 'debug'
            )
        );
        // \Qck\FeedEngine\Core\Debug::logDump($general_options_section, __METHOD__);
    }

    /**
     * Register sections.
     */
    public function register_sections() {
        $this->register_general_options();
    }

}
