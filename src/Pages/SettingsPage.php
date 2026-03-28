<?php
namespace Qck\FeedEngine\Core\Pages;

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



    /**
     * Settings_Page constructor.
     *
     * @param HooksManager $hooks_manager
     * @param Options       $options
     */
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
        return __( 'Glave', 'glave' );
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    protected function get_page_title() {
        return __( 'Glave Settings', 'glave' );
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
        return Plugin::PREFIX . '_settings';
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

/** ***** Original Junk ***** 
 * Below is the original setup from the example using this framework
 */
/*
    private function register_general_options() {
        $general_options_section = $this->register_section(
            'options',
            array( 'title' => __( 'General Options', 'glave' ) )
        );

        $notify_on_lockout = $general_options_section->add_field(
            array( 'label'  => __( 'Glave Engine', 'glave' ) )
        );

        $notify_on_lockout->add_element(
            Element::CHECKBOX_ELEMENT,
            array(
                'label' => __( 'Run Glave Engine', 'glave' ),
                'name'  => 'engine_active'
            )
        );

        // $controllers_list = [];

        // $controller_opt = $this->options->get_section('controllers');
        

        // // echo '<pre>' . print_r($controller_opt, true) . '</pre>';
        // foreach( $controller_opt as $controller_name => $controller_settings) {
        //     $controllers_list[$controller_name] = $general_options_section->add_field(
        //         array( 'label'  => __( $controller_name, 'glave' ) )
        //     );
        //     $controllers_list[$controller_name]->add_element(
        //         Element::CHECKBOX_ELEMENT,
        //         array(
        //             'label' => __( 'Activate ', 'glave' ),
        //             'name'  => 'active'
        //         )
        //     );
        // }
        // $handle_cookie_login_field->add_element(
        //     Element::RADIO_ELEMENT,
        //     array(
        //         'name'   => 'handle_cookie_login',
        //         'values' => array(
        //             'yes' => __( 'Yes', 'prsdm-limit-login-attempts' ),
        //             'no'  => __( 'No', 'prsdm-limit-login-attempts' )
        //         )
        //     )
        // );


        // $lockout_field = $general_options_section->add_field(
        //     array( 'label' => __( 'Lockout', 'prsdm-limit-login-attempts' ) )
        // );

        // $lockout_field->add_element(
        //     Element::NUMBER_ELEMENT,
        //     array(
        //         'label'    => __( 'Allow retries', 'prsdm-limit-login-attempts' ),
        //         'name'     => 'allowed_retries',
        //         'validate' => array( Utils::class, 'is_greater_than_zero' )
        //     )
        // );

        // $lockout_field->add_element(
        //     Element::NUMBER_ELEMENT,
        //     array(
        //         'label'     => __( 'Lockout time (in minutes)', 'prsdm-limit-login-attempts' ),
        //         'name'      => 'normal_lockout_time',
        //         'validate'  => array( Utils::class, 'is_greater_than_zero' ),
        //         'pre_write' => array( Utils::class, 'minutes_to_seconds' ),
        //         'post_read' => array( Utils::class, 'seconds_to_minutes' )
        //     )
        // );

        // $lockout_field->add_element(
        //     Element::NUMBER_ELEMENT,
        //     array(
        //         'label'    => __( 'Max lockouts', 'prsdm-limit-login-attempts' ),
        //         'name'     => 'max_lockouts',
        //         'validate' => array( Utils::class, 'is_greater_than_zero' )
        //     )
        // );

        // $lockout_field->add_element(
        //     Element::NUMBER_ELEMENT,
        //     array(
        //         'label'     => __( 'Increased lockout time (in hours)', 'prsdm-limit-login-attempts' ),
        //         'name'      => 'long_lockout_time',
        //         'validate'  => array( Utils::class, 'is_greater_than_zero' ),
        //         'pre_write' => array( Utils::class, 'hours_to_seconds' ),
        //         'post_read' => array( Utils::class, 'seconds_to_hours' )
        //     )
        // );

        // $lockout_field->add_element( 
        //     Element::NUMBER_ELEMENT,
        //     array(
        //         'label'     => __( 'Hours until retries are reset', 'prsdm-limit-login-attempts' ),
        //         'name'      => 'hours_until_retries_reset',
        //         'validate'  => array( Utils::class, 'is_greater_than_zero' ),
        //         'pre_write' => array( Utils::class, 'hours_to_seconds' ),
        //         'post_read' => array( Utils::class, 'seconds_to_hours' )
        //     )
        // );

        // $site_connection_field = $general_options_section->add_field(
        //     array(
        //         'label'       => __( 'Site connection', 'prsdm-limit-login-attempts' ),
        //         'description' => $this->get_site_connection_description()
        //     )
        // );

        // $site_connection_field->add_element(
        //     Element::RADIO_ELEMENT,
        //     array(
        //         'label' => __( 'Site connection', 'prsdm-limit-login-attempts' ),
        //         'name'  => 'site_connection',
        //         'values'  => array(
        //             'direct'        => __( 'Direct connection', 'prsdm-limit-login-attempts' ),
        //             'reverse_proxy' => __( 'From behind a reverse proxy', 'prsdm-limit-login-attempts' )
        //         )
        //     )
        // );

        // $handle_cookie_login_field = $general_options_section->add_field(
        //     array( 'label' => __( 'Handle cookie login', 'prsdm-limit-login-attempts' ) )
        // );

        // $handle_cookie_login_field->add_element(
        //     Element::RADIO_ELEMENT,
        //     array(
        //         'name'   => 'handle_cookie_login',
        //         'values' => array(
        //             'yes' => __( 'Yes', 'prsdm-limit-login-attempts' ),
        //             'no'  => __( 'No', 'prsdm-limit-login-attempts' )
        //         )
        //     )
        // );

        // $notify_on_lockout = $general_options_section->add_field(
        //     array( 'label'  => __( 'Notify on lockout', 'prsdm-limit-login-attempts' ) )
        // );

        // $notify_on_lockout->add_element(
        //     Element::CHECKBOX_ELEMENT,
        //     array(
        //         'label' => __( 'Log IP', 'prsdm-limit-login-attempts' ),
        //         'name'  => 'notify_on_lockout_log_ip'
        //     )
        // );

        // $notify_on_lockout->add_element(
        //     Element::CHECKBOX_ELEMENT,
        //     array(
        //         'label' => __( 'Email to admin', 'prsdm-limit-login-attempts' ),
        //         'name'  => 'notify_on_lockout_email_to_admin'
        //     )
        // );

        // $notify_on_lockout->add_element(
        //     Element::NUMBER_ELEMENT,
        //     array(
        //         'label'    => __( 'After lockouts', 'prsdm-limit-login-attempts' ),
        //         'name'     => 'notify_after_lockouts',
        //         'validate' => array( Utils::class, 'is_greater_than_zero' )
        //     )
        // );
    }
*/