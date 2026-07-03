<?php
namespace Qck\FeedEngine\Core\Pages\Components;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\MetaSection;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\MetaField;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;



class Metabox {
    public $sections = array();
    
    private $title;
    private $id;
    public function get_id(){
        return $this->id;
    }

    private $page;    
    private $description;
    public $callback;
    public $context;
    public $priority;

    public function __construct( $section_id, $page, $properties = array() ) {
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $section_id, Manifest::PREFIX ),
                'description' => ''
            )
        );

        $this->title       = $properties['title'];
        $this->description = $properties['description'];
        $this->page        = $page;
        $this->id          = $section_id;
        $this->priority          = $properties['priority'];
        $this->context          = $properties['context'];
        $this->callback          = $properties['callback'];
        
    }
    public function register(){
        add_meta_box(
            $this->id,
            $this->title,
            $this->callback,
            $this->page,
            $this->context ,
            $this->priority,
        );
    }

    public function parse_defaults($properties){
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $this->id, Manifest::PREFIX ),
                'description' => '',
                'meta' => true,
                'type' => null,
            )
        );
        return $properties;
    }
    
    public function add_section( $properties , $settings) {
        $properties = $this->parse_defaults($properties);

        $section = new MetaSection( $this->id, $this->page, $settings, $properties );
        $this->sections[] = $section;
        
        return $section;
    }

    public function boot_sections($obj_id, $settings){
        foreach ($this->sections as $key => $section) {
            SettingBuilder::build_ui_from_metabox($this->get_obj_id($obj_id), $section, $settings);
        }
    }

    public function boot_sections_taxonomy($taxonomy, $obj_id, $settings){
        foreach ($this->sections as $key => $section) {
            SettingBuilder::build_ui_from_metabox($this->get_obj_id($obj_id), $section, $settings);
        }
    }

    public function render($post, $settings) {
        $html =  '';
        $html .= '<div class="' . Manifest::PREFIX . '-metabox-wrapper">';
        foreach ($this->sections as $section) {
            
            $html .= $section->render();
        }
        $html .= '</div>';
        return $html;
    }


    private function get_obj_id($obj){
        if (is_numeric($obj)) {
        return (int) $obj;
        }

        if ($obj instanceof \WP_Comment) {
            return (int) $obj->comment_ID; // The culprit!
        }

        if ($obj instanceof \WP_Post || $obj instanceof \WP_User) {
            return (int) $obj->ID;
        }

        if ($obj instanceof \WP_Term ) {
            return (int) $obj->ID;
        }

        // if ($obj instanceof \WP_Taxonomy ) {
        //     return (int) $obj->ID;
        // }

        // Fallback block if an array with an ID key is passed
        if (is_array($obj) && isset($obj['ID'])) {
            return (int) $obj['ID'];
        }

        return 0;

    }

    public function sanitize( $options ) {
        $_options = $options;
        $sections = $this->sections;

        foreach ( $options as $key => $value ) {
            $section         = $sections[ $key ];
            $sanitized_value = $section->sanitize( $value );
            $options[ $key ] = $sanitized_value;
        }

        return $options;
    }
}