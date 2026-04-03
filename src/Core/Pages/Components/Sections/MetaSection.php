<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\MetaField;
use Qck\FeedEngine\Core\Debug;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MetaSection extends Section {

        public function __construct( $section_id, $page, $options_instance, $properties = array() ) {
        
        // $dump_me = ['section_id'=>$section_id, 'page'=>$page,'properties'=>$properties, 'options'=>$options_instance];
        // \Qck\FeedEngine\Core\Debug::logDump($dump_me, __METHOD__);
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $section_id, Manifest::PREFIX ),
                'description' => ''
            )
        );

        $this->options = $options_instance;

        $this->title       = $properties['title'];
        $this->description = $properties['description'];
        $this->page        = $page;
        $this->id          = $section_id;
    }

    /**
     * Create and add a new field object to this section.
     *
     * @param array $properties Field properties.
     */
    public function add_field( $properties ) {
        $field = new MetaField( $this->id, $this->page, $properties );

        $this->fields[] = $field;

        return $field;
    }

    public function render() {
        $html = '';
        foreach ($this->fields as $field) {
            $html .= $field->render();
        }
        // \Qck\FeedEngine\Core\Debug::logDump( $html, __METHOD__);
        return $html;
    }
}