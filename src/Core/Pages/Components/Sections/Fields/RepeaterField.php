<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Checkbox;



class RepeaterField extends Field {
    protected $temp_elements = array();




    public function __construct( $section_id, $page, $properties = array() ) {
        parent::__construct( $section_id, $page, $properties );

        $this->value = $properties['value'];
    }


    /**
     * Render the UI element.
     *
     * @return string
     */

    public function render() {
        
        $hiddem = 'hidden';
        $class = $this->get_css_class() ;
        $label = $this->get_label() ;
        $description = $this->get_description() ;
        $name = esc_attr( $this->name );
        $prefix = Manifest::PREFIX ;
        
        $template = '';
        foreach ( $this->temp_elements as $key => $element ) {
            $template .= $element->render();
        }
        $template_wrapper = <<<HTML
            <div class="{$prefix}-repeat_field-template" {$hiddem}>
                <fieldset class="{$prefix}-repeat_field-item" name="{$name}[__INDEX__]" data-new='1'>
                    <span class="{$prefix}-repeat_field-index">__INDEX__</span>
                    {$template}
                    <button type="button" class="button {$prefix}-repeat_field-remove">Remove</button>
                    <button type="button" class="button {$prefix}-repeat_field-copy">Copy</button>
                </fieldset>
            </div>

        HTML;

        $content = '';


        $content .= '<fieldset class="' . Manifest::PREFIX . '-repeat_field-content ' . $class . '" name="' . esc_attr( $this->name ) . '">';

        foreach ( $this->elements as $key => $elements ) {
            $content .= '<fieldset class="' . Manifest::PREFIX . '-repeat_field-item" name="' . esc_attr( $this->name ) . '[' . $key . ']">';
            $content .= '<span class="' . $prefix . '-repeat_field-index">' . $key . '</span>';
            foreach ( $elements as $_key => $element ) {
                $content .= $element->render();
            }
            $content .= '<button type="button" class="button ' . Manifest::PREFIX . '-repeat_field-remove">Remove</button>';
            $content .= '<button type="button" class="button ' . Manifest::PREFIX . '-repeat_field-copy">Copy</button>';
            $content .= '</fieldset>';
        }
        
        $content .= '</fieldset>';


        $html = <<<HTML
            <div class="{$class}">
                {$label}
                {$template_wrapper}
                {$content}
                <button type="button" class="button {$prefix}-repeat_field-add">Add</button>
                {$description}
            </div>

        HTML;
        return $html;
    }



    
    public function get_elements() {
        return $this->elements;
    }

    public function add_field( $field_type, $properties ) {
        
        $field_type = __NAMESPACE__ . '\\' . $field_type;
        if ( ! class_exists( $field_type )  ) {
            return;
        }
        $temp_properties = $properties;
        $temp_properties['path'] .= '[__INDEX__]';
        $field = new $field_type( $this->section_id, $this->page, $temp_properties );
        if ( ! ( $field instanceof Field ) ) {
            \Qck\FeedEngine\Core\Debug::logDump( $field, __METHOD__ . ' No Match');
            return;
        }
        $this->temp_elements[ $field->get_option_key() ] = $field;

        if(empty($this->value)) {
            $item_properties = $properties;
            $item_properties['path'] .= '[0]';
            $element = new $field_type( $this->section_id, $this->page, $item_properties );
            $this->elements[0][ $element->get_option_key() ] = $element;

        } else {
            foreach ($this->value as $index => $value ) {
                $item_properties = $properties;
                $key = $item_properties['id'];
                // \Qck\FeedEngine\Core\Debug::logDump( $index, __METHOD__ . ' $index');
                // \Qck\FeedEngine\Core\Debug::logDump( $value, __METHOD__ . ' $value');
                // \Qck\FeedEngine\Core\Debug::logDump( $key, __METHOD__ . ' $key');
                $item_properties['path'] .= '[' . $index .']';
                $item_properties['value'] = !empty($value[$key]) ? $value[$key] : $item_properties['default'];
                // \Qck\FeedEngine\Core\Debug::logDump( $item_properties, __METHOD__ . ' $item_properties');
                $element = new $field_type( $this->section_id, $this->page, $item_properties );
                $this->elements[$index][ $element->get_option_key() ] = $element;

            }
        }

        
        return $field;
    }

    public function add_element( $element_type, $properties ) {
        $element_type = __NAMESPACE__ . '\\Elements\\' . $element_type;
        
        if ( ! class_exists( $element_type ) ) {
            return;
        }
        $temp_properties = $properties;
        $temp_properties['value'] = $properties['default'];
        $temp_properties['path'] .= '[__INDEX__]';
        $temp_properties['disabled'] = true;
        $temp_element = new $element_type( $this->section_id, $temp_properties );
        if ( ! ( $temp_element instanceof Element) ) {
            \Qck\FeedEngine\Core\Debug::logDump( $temp_element, __METHOD__ . ' No Match');
            return;
        }

        $this->temp_elements[ $temp_element->get_option_key() ] = $temp_element;

        if(empty($this->value)) {
            $item_properties = $properties;
            $item_properties['path'] .= '[0]';
            $element = new $element_type( $this->section_id, $item_properties );
            $this->elements[0][ $element->get_option_key() ] = $element;

        } else {
            foreach ($this->value as $index => $value ) {
                $item_properties = $properties;
                $key = $item_properties['id'];
                $item_properties['path'] .= '[' . $index .']';
                if (  ( $temp_element instanceof Checkbox) ) {
                    $item_properties['value'] = array_key_exists($key, $value) ? $value[$key] : $item_properties['default'];
                } else {
                    $item_properties['value'] = !empty($value[$key]) ? $value[$key] : $item_properties['default'];
                }
                
                // \Qck\FeedEngine\Core\Debug::logDump( $item_properties, __METHOD__ . ' $item_properties');
                $element = new $element_type( $this->section_id, $item_properties );
                $this->elements[$index][ $element->get_option_key() ] = $element;

            }
        }
        return $this;
    }

    public function get_css_class() {
        return Manifest::PREFIX . '-field' . ' ' . Manifest::PREFIX . '-repeat_field' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }

    public function sanitize( $options ) {
        $_options = $options;
        foreach ($options as $index => $option) {
            # code...
        
            $elements = $this->elements;

            foreach ( $option as $key => $value ) {
                $element         = $elements[$index][ $key ];
                if(!empty($element)){
                    $sanitized_value = $element->sanitize( $value );

                    $option[ $key ] = $sanitized_value;
                } else {
                    \Qck\FeedEngine\Core\Debug::logDump( 'Element not found. ' . $key, __METHOD__ . ' Sanitization' );
                }
                
            }

            $options[ $index ] = $option;
        }
        return $options;
    }

}