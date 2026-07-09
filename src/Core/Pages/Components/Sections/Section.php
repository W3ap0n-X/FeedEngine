<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Field;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Section {

    
    protected $fields = array();

    protected $section_id;
    public $options;

    public $properties;
    public function get_properties(){
        return $this->id;
    }
    protected $title;

    protected $option_name;
    protected $name;
    
    protected $id;

    public function get_id(){
        return $this->id;
    }

    
    protected $page;
    protected $type;
    protected $meta;
    protected $class;

    
    protected $description;

    public function parse_defaults($properties){
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $this->section_id, Manifest::PREFIX ),
                'description' => '',
                'meta' => false,
                'type' => 'options',
                'class' => null,
                'style' => null,
            )
        );
        return $properties;
    }

    protected $hook;

    private function set_sanitize_hook(){
        $this->hook = Manifest::PREFIX . "/" . ($this->meta ? 'meta' : 'settings') . (!empty($this->type) ? "/" . $this->type : '') . "/sanitize/" . ($this->meta ? '_' : '') . "{$this->section_id}";
        add_filter( $this->hook, [$this, 'sanitize'] , 10, 1);
    }
    
    public function __construct( $section_id, $page, $options_instance, $properties = array() ) {
        
        $this->section_id  = $section_id;
        $properties = $this->parse_defaults($properties);

        $this->options = $options_instance;


        $this->type       = $properties['type'];
        $this->meta       = $properties['meta'];
        $this->title       = $properties['title'];
        $this->description = $properties['description'];
        $this->page        = $page;
        $this->class        = $properties['class'];
        $this->id          = $section_id;
        $this->set_sanitize_hook();
    }

    public function sanitize( $options ) {
        $_options = $options;
        $fields = $this->fields;
        // \Qck\FeedEngine\Core\Debug::logDump($options, __METHOD__ . ' ## ' . $this::class . '  ' . $this->id);

        foreach ( $options as $key => $value ) {
            $field         = $fields[ $key ];
            if(!empty($field)){
                $sanitized_value = $field->sanitize( $value );
                $options[ $key ] = $sanitized_value;
            } else {
                \Qck\FeedEngine\Core\Debug::logDump( 'Field not found. ' . $key, __METHOD__ . ' Sanitization' , 0);
            }
            
            
        }
        \Qck\FeedEngine\Core\Debug::logDump(['hook' => $this->hook,'original' => $_options,'sanitized' => $options], __METHOD__ . ' ## ' . $this::class, 775);

        return $options;
    }

    public function print_description() {
        echo  $this->get_description();
    }

    public function add_field( $field_type, $properties ) {
        
        $field_type = __NAMESPACE__ . '\\Fields\\' . $field_type;

        // \Qck\FeedEngine\Core\Debug::logDump( $field_type, __METHOD__ . ' $field_type');

        if ( ! class_exists( $field_type )  ) {
            return;
        }

        $field = new $field_type( $this->section_id, $this->page, $properties );


        
        if ( ! ( $field instanceof Field ) ) {
            \Qck\FeedEngine\Core\Debug::logDump( $field, __METHOD__ . ' No Match');
            return;
        }

        // \Qck\FeedEngine\Core\Debug::logDump( $element, __METHOD__ . ' $element');
        // if ($element instanceof Field) {
        //     $this->elements[ $element->get_option_name() ] = $element;

        // } else {
            
        // }
        $this->fields[ $field->get_option_key() ] = $field;

        
        return $field;
    }

    public function get_description() {
        return $this->description ? '<p class="' . Manifest::PREFIX . '-field-description">' .  $this->description . '</p>' : '';
    }

    
    public function get_name() {
        return esc_attr( $this->section_id ) ;
    }

    public function get_css_class() {
        return Manifest::PREFIX . '-section' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }


}