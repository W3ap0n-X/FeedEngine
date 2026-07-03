<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

use Qck\FeedEngine\Core\Pages\Components\Interfaces\HTML;
use Qck\FeedEngine\Manifest;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom extends Element {

    
    private $html = null;

    
    public function render() {
        $content = ! empty( $this->html ) ? $this->html : '';
        $label =  $this->get_label() ;
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $class = $this->get_css_class();
        $style = $this->style;
        
        $html = <<<HTML
        
            
            <fieldset class="{$class}" style="{$style}">
                {$label}
                <label>
                {$content}
                {$helptext}
                </label>
                {$description}

            </fieldset>
        HTML;
        return $html;
    }

    public function set_html($html) {
        // \Qck\FeedEngine\Core\Debug::logDump( $this->value, __METHOD__ . ' $this->value');
        // \Qck\FeedEngine\Core\Debug::logDump( $this->name, __METHOD__ . ' $this->name');
        // \Qck\FeedEngine\Core\Debug::logDump( $this->get_value(), __METHOD__ . ' $this->get_value()');
        if ( $html instanceof HTML ) {
            $this->html = $html->get_html( $this->value, $this->name, Manifest::PREFIX );
        } else {
            // \Qck\FeedEngine\Core\Debug::logDump( $html, __METHOD__ . ' HTML is not valid');
            $this->html = $html;
        }

    }

    
    public function __construct( $section_id, $properties = array() ) {
        // \Qck\FeedEngine\Core\Debug::logDump( [$section_id, $properties], __METHOD__ . ' [$section_id, $properties]');
        parent::__construct( $section_id, $properties );
        $properties = wp_parse_args(
            $properties,
            array(
                'label'     => sprintf(
                    
                    __( 'Element #%s', Manifest::PREFIX ),
                    self::$number_of_elements
                ),
                'name'      => $section_id . '_element_' . self::$number_of_elements,
                'id'          => $section_id . '_element_' . self::$number_of_elements,
                'validate'  => null,
                'pre_write' => null,
                'post_read' => null,
                'prefix' => '',
                'meta' => false,
                'description' => '',
                'default' => null,
                'labels' => false,
                'disabled' => false,
                'label_element' => 'span',
                'list_keys' => false,
                'class' => null,
                'style' => null,

            )
        );
        
        $this->label       = $properties['label'];
        $this->list_keys       = $properties['list_keys'];
        $this->labels       = $properties['labels'];
        $this->id = $properties['id'];
        $this->option_name = $properties['path'] . $properties['name'];
        // $this->name        = sprintf( '%s%s_%s', $properties['prefix'], Manifest::PREFIX , $this->option_name );
        $this->name        = sprintf( '%s%s_%s', ($properties['meta']? '_':''), Manifest::PREFIX , $this->option_name );
        // $this->validate    = $properties['validate'];
        // $this->pre_write   = $properties['pre_write'];
        $this->label_element       = $properties['label_element'] ?? 'span';
        $this->value       = $properties['value'];
        $this->default       = $properties['default'];
        $this->description = $properties['description'];
        $this->disabled = $properties['disabled'];
        $this->class = $properties['class'];
        $this->style = $properties['style'];
        $this->helptext = !empty($properties['helptext']) ? $properties['helptext'] : $this->helptext;
        if ( is_callable( $properties['post_read'] ) ) {
            $this->value = $properties['post_read']( $this->value );
        }
        // \Qck\FeedEngine\Core\Debug::logDump( $this, __METHOD__ . ' $this');
        // \Qck\FeedEngine\Core\Debug::logDump( $this->get_name(), __METHOD__ . ' $this->get_name()');
        $this->set_html( $properties['html'] );
    }

    public function get_name() {
        return $this->name;
    }

    public function sanitize( $option_value ) {
        // No real strategy for custom controls yet
        return $option_value;
    }

}