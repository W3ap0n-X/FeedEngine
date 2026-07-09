<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;

class Field {

    const DEFAULT_FIELD = 'Field';
    const REPEATER_FIELD = 'RepeaterField';
    const SETTINGS_FIELD = 'SettingsField';
    
    protected static $number_of_fields = 0;

    protected $id;
    protected $label;
    protected $elements = array();

    protected $labels;
    protected $label_element;

    protected $option_name;

    protected $name;

    protected $page;
    protected $class;


    
    protected $section_id;

    
    protected $description;

    protected $value;

    

    public function render() {
        $prefix = Manifest::PREFIX;
        $name = esc_attr( $this->name );
        $label =  $this->get_label() ;
        $description = $this->get_description() ;
        $class = $this->get_css_class();
        
        /* Render each element in field  */
        $content = '';
        foreach ( $this->elements as $key => $element ) {
            $content .= $element->render();
        }

        $html = <<<HTML
            <fieldset class="{$class}" name="{$name}">
                {$label}
                    <fieldset class="{$prefix}-field-content" name="{$name}">
                        
                        {$content}
                    </fieldset>
                {$description}
            </fieldset>
        HTML;
        
        return $html;
    }

    public function parse_defaults($properties){
        $properties = wp_parse_args(
            $properties,
            array(
                'label'     => sprintf(
                    
                    __( 'Field #%s', Manifest::PREFIX ),
                    self::$number_of_fields
                ),
                'name'      => $this->section_id . '_field_' . self::$number_of_fields,
                'id'          => $this->section_id . '_field_' . self::$number_of_fields,
                'validate'  => null,
                'pre_write' => null,
                'post_read' => null,
                'description' => '',
                'labels' => false,
                'meta' => false,
                'class' => null,
                'style' => null,
                'prefix' => '',
                'label_element' => 'legend',
                'list_keys' => false,
                'default' => null,

            )
        );
        return $properties;
    }

    
    public function __construct( $section_id, $page, $properties = array() ) {
        $this->section_id  = $section_id;

        self::$number_of_fields++;

        $properties = $this->parse_defaults($properties);

        
        $this->page  = $page;
        $this->id = $properties['id'];
        $this->description = $properties['description'];
        $this->label       = $properties['label'];
        $this->class       = $properties['class'];
        $this->labels       = $properties['labels'];
        $this->option_name = $properties['path'] . $properties['name'];
        $this->label_element       = $properties['label_element'] ?? 'legend';
        $this->name        = sprintf( '%s%s_%s', ($properties['meta']? '_':''), Manifest::PREFIX , $this->option_name );

    }

    public function sanitize( $options ) {
        $_options = $options;
        $elements = $this->elements;
        // \Qck\FeedEngine\Core\Debug::logDump($options, __METHOD__ . ' ## ' . $this::class . '  ' . $this->option_name);
        if(is_array($options)){
            foreach ( $options as $key => $value ) {
                $element         = $elements[ $key ];
                if(!empty($element)){
                    $sanitized_value = $element->sanitize( $value );
                    // $validate        = $element->get_validate();
                    // $pre_write       = $element->get_pre_write();

                    // if ( is_callable( $validate ) && ! $validate( $sanitized_value ) ) {
                    //     \Qck\FeedEngine\Core\Debug::logDump($sanitized_value, __METHOD__ . ' Unable to validate $sanitized_value');
                    //     $sanitized_value = $element->get_value();
                    // }

                    // if ( is_callable( $pre_write ) ) {
                    //     $sanitized_value = $pre_write( $sanitized_value );
                    // }


                    $options[ $key ] = $sanitized_value;
                } else {
                    \Qck\FeedEngine\Core\Debug::logDump( 'Element not found. ' . $key, __METHOD__ . ' Sanitization' , 10);
                }
            }
        }
        else {
            $element         = $elements[ $this->id ];
            if(!empty($element)){
                $sanitized_value = $element->sanitize( $options );
                // $validate        = $element->get_validate();
                // $pre_write       = $element->get_pre_write();

                // if ( is_callable( $validate ) && ! $validate( $sanitized_value ) ) {
                //     \Qck\FeedEngine\Core\Debug::logDump($sanitized_value, __METHOD__ . ' Unable to validate $sanitized_value');
                //     $sanitized_value = $element->get_value();
                // }

                // if ( is_callable( $pre_write ) ) {
                //     $sanitized_value = $pre_write( $sanitized_value );
                // }


                $options = $sanitized_value;
            } else {
                \Qck\FeedEngine\Core\Debug::logDump( 'Element not found. ' . $this->id, __METHOD__ . ' Sanitization' , 10);
            }
        }
        
        \Qck\FeedEngine\Core\Debug::logDump(['original' => $_options,'sanitized' => $options], __METHOD__ . ' $_options && $options', 775);

        return $options;
    }

    public function add_field( $field_type, $properties ) {
        
        $field_type = __NAMESPACE__ . '\\' . $field_type;

        if ( ! class_exists( $field_type )  ) {
            return;
        }

        $field = new $field_type( $this->section_id, $this->page, $properties );


        
        if ( ! ( $field instanceof Field ) ) {
            \Qck\FeedEngine\Core\Debug::logDump( $field, __METHOD__ . ' No Match', 5);
            return;
        }
        $this->elements[ $field->get_option_key() ] = $field;

        
        return $field;
    }

    
    public function add_element( $element_type, $properties ) {
        $element_type = __NAMESPACE__ . '\\Elements\\' . $element_type;
        if ( ! class_exists( $element_type ) ) {
            return;
        }
        $element = new $element_type( $this->section_id, $properties );
        if ( ! ( $element instanceof Element) ) {
            \Qck\FeedEngine\Core\Debug::logDump( $element, __METHOD__ . ' No Match', 5);
            return;
        }
        $this->elements[ $element->get_option_key() ] = $element; 

        
        return $this;
    }

    public function get_option_key() {
        return $this->id;
    }

    public function get_option_name() {
        return $this->option_name;
    }
    
    public function get_elements() {
        return $this->elements;
    }

    public function get_description() {
        return $this->description ? '<p class="' . Manifest::PREFIX . '-field-description">' .  $this->description . '</p>' : '';
    }

    
    public function get_label() {
        return $this->labels ?'<'. $this->label_element .' class="' . Manifest::PREFIX . '-field-label">' . esc_html( $this->label) . '</'. $this->label_element .'>' : '' ;
    }

    public function get_css_class() {
        return Manifest::PREFIX . '-field' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }

}