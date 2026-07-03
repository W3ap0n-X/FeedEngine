<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\MetaField;
use Qck\FeedEngine\Core\Debug;



class MetaSection extends Section {

    public function parse_defaults($properties){
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $this->section_id, Manifest::PREFIX ),
                'description' => '',
                'meta' => true,
                'type' => 'meta',
                'class' => null,
                'style' => null,
            )
        );
        return $properties;
    }

    public function __construct( $section_id, $page, $options_instance, $properties = array() ) {

        parent::__construct($section_id, $page, $options_instance, $properties);
        // \Qck\FeedEngine\Core\Debug::logDump($this->hook, __METHOD__ . ' ## ' . $this::class . ' || ' . $options_instance::class);
        
    }

    public function render() {
        $prefix = Manifest::PREFIX;

        $class = $this->get_css_class();
        $name = $this->get_name();
        $description = $this->get_description() ;

        $content = '';
        foreach ($this->fields as $field) {
            $content .= $field->render();
        }

        $html = <<<HTML
            
            <div class="{$class}" name="{$name}">
                {$description}
                <fieldset class="{$prefix}-section-content">
                    {$content}
                </fieldset>
            </div>
        HTML;
        return $html;
    }

    public function get_css_class() {
        return Manifest::PREFIX . '-section' . ' ' . Manifest::PREFIX . '-meta-section' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }
}