<?php
namespace Qck\FeedEngine\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FeedWidget extends Widget_Base {

    public function get_name() {
        return 'rh_feed_grid';
    }

    public function get_title() {
        return __( 'RH Feed Engine', 'rh-feed-engine' );
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Feed Settings', 'rh-feed-engine' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'categories',
            [
                'label'       => __( 'Category Slugs', 'rh-feed-engine' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'brands, blog',
                'description' => 'Comma separated list of slugs.',
            ]
        );

        $this->add_control(
            'template',
            [
                'label'   => __( 'Layout Template', 'rh-feed-engine' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'bento-grid',
                'options' => [
                    'bento-grid'    => __( 'Figma Bento Grid', 'rh-feed-engine' ),
                    'standard-card' => __( 'Bootstrap Grid', 'rh-feed-engine' ),
                    'list'          => __( 'Simple List', 'rh-feed-engine' ),
                ],
            ]
        );

        $this->add_control(
            'posts_per_cat',
            [
                'label'   => __( 'Posts Per Category', 'rh-feed-engine' ),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 12,
                'step'    => 1,
                'default' => 3,
            ]
        );

        $this->add_control(
            'exclude_duplicates',
            [
                'label'        => __( 'Exclude Already Rendered', 'rh-feed-engine' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'rh-feed-engine' ),
                'label_off'    => __( 'No', 'rh-feed-engine' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'debug',
            [
                'label'        => __( 'Debug Mode (Admins Only)', 'rh-feed-engine' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'On', 'rh-feed-engine' ),
                'label_off'    => __( 'Off', 'rh-feed-engine' ),
                'return_value' => 'yes',
                'default'      => 'no',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // We convert Elementor settings back into shortcode-style attributes
        // to keep our Controller logic DRY (Don't Repeat Yourself).
        $shortcode_atts = sprintf(
            '[rh_feed categories="%s" template="%s" posts_per_cat="%s" exclude_duplicates="%s" debug="%s"]',
            esc_attr( $settings['categories'] ),
            esc_attr( $settings['template'] ),
            esc_attr( $settings['posts_per_cat'] ),
            esc_attr( $settings['exclude_duplicates'] ),
            esc_attr( $settings['debug'] )
        );

        echo do_shortcode( $shortcode_atts );
    }
}