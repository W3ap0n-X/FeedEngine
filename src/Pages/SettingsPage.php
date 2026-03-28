<?php
namespace Qck\FeedEngine\Core\Pages;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Hooks\HooksManager;
use Qck\FeedEngine\Core\Pages\Components\Page\TopPage;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;
use Qck\FeedEngine\Core\Pages\Components\Standalone\AdminNotice;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SettingsPage extends Top_Page implements Actions {



    public function __construct( $options) {
        parent::__construct( $options );
    }

    /**
     * Return the actions to register.
     *
     * @return array
     */
    public function get_actions() {
        $actions = parent::get_actions();

        return $actions;
    }



    /**
     * Return the menu title.
     *
     * @return string
     */
    protected function get_menu_title() {
        return __( 'Glave', Manifest::SLUG );
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    protected function get_page_title() {
        return __( 'Glave Settings', Manifest::SLUG );
    }

    /**
     * Return the menu icon as a dashicon.
     *
     * @link https://developer.wordpress.org/resource/dashicons/
     *
     * @return string
     */
    protected function get_icon_url() {
        return 'dashicons-shield-alt';
    }

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
        $general_options_section = $this->register_section(
            'general_options',
            array( 'title' => __( 'General Options', 'glave' ) )
        );

        $glave_engine = $general_options_section->add_field(
            array( 'label'  => __( 'Glave Engine', 'glave' ) )
        );

        $glave_engine->add_element(
            Element::CHECKBOX_ELEMENT,
            array(
                'label' => __( 'Run Glave Engine', 'glave' ),
                'name'  => 'engine_active'
            )
        );
    }

    /**
     * Register sections.
     */
    public function register_sections() {
        $this->register_general_options();
    }

}
