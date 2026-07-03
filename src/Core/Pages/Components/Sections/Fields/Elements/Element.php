<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Interfaces\UI;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Element implements UI {
    const NUMBER_ELEMENT = 'Number';
    const TEXT_ELEMENT = 'Text';
    const TEXTAREA_ELEMENT = 'TextArea';
    const RADIO_ELEMENT = 'Radio';
    const DROPDOWNLIST_ELEMENT = 'DropDownList';
    const CHECKBOX_ELEMENT = 'Checkbox';
    const IMAGE_ELEMENT = 'Image';
    const CUSTOM_ELEMENT = 'Custom';
    protected static $number_of_elements = 0;

    protected $id;
    protected $section_id;
    protected $label;
    protected $label_element;
    protected $labels;

    /* 
    * For the options array on some controls like dropdown lists  and radios. Not to be confused with instance of and options object.
    * if a flat list is provided setting this to true will use the numeric index as the data value ie `[ 0 => 'One' ]` as opposed to `['One']`
    * does not affect options provided as associative arrays.
    */
    protected $list_keys;
    protected $name;

    protected $description;
    public $value;

    protected $default;
    protected $option_name;
    // ! Not Yet Implemented
    private $validate;
    // ! Not Yet Implemented
    private $pre_write;
    /* 
    * Optional context element for controls 
    * i.e. adding 'Select an option...' to a dropdown list or putting a faded out example or field constraints.
    */
    protected $helptext;
    protected $class;
    protected $style;
    protected $disabled;


    public function parse_defaults($properties){
        $properties = wp_parse_args(
            $properties,
            array(
                'label'     => sprintf(
                    __( 'Element #%s', Manifest::PREFIX ),
                    self::$number_of_elements
                ),
                'name'      => $this->section_id . '_element_' . self::$number_of_elements,
                'id'          => $this->section_id . '_element_' . self::$number_of_elements,
                'validate'  => null,
                'pre_write' => null,
                'post_read' => null,
                'prefix' => '',
                'meta' => false,
                'description' => '',
                'default' => null,
                'class' => null,
                'style' => null,
                'labels' => false,
                'disabled' => false,
                'label_element' => 'span',
                'list_keys' => false,
            )
        );
        return $properties;
    }
    
    public function __construct( $section_id, $properties = array()) {
        self::$number_of_elements++;
        $this->section_id = $section_id;
        if ( $this instanceof SettingsInterface ) {
            $properties = $this->parse_defaults($properties);
            $this->label       = $properties['label'];
            $this->label_element       = $properties['label_element'] ?? 'span';
            $this->labels       = $properties['labels'];
            $this->id = $properties['id'];
            $this->option_name = $properties['path'] . $properties['name'];
            $this->name        = sprintf( '%s%s_%s', ($properties['meta']? '_':''), Manifest::PREFIX , $this->option_name );
            $this->validate    = $properties['validate'];
            $this->pre_write   = $properties['pre_write'];
            $this->value       = $properties['value'];
            $this->default       = $properties['default'];
            $this->description = $properties['description'];
            $this->disabled = $properties['disabled'];
            $this->class = $properties['class'];
            $this->style = $properties['style'];
            $this->list_keys = $properties['list_keys'];
            $this->helptext = !empty($properties['helptext']) ? $properties['helptext'] : $this->helptext;
            if ( is_callable( $properties['post_read'] ) ) {
                $this->value = $properties['post_read']( $this->value );
            }
        }
    }

    public function get_option_key() {
        return $this->id;
    }

    public function get_option_name() {
        return $this->option_name;
    }

    // ! Not Yet Implemented
    public function get_validate() {
        return $this->validate;
    }

    public function get_value() {
        return $this->value;
    }

    // ! Not Yet Implemented
    public function get_pre_write() {
        return $this->pre_write;
    }

    public function get_description() {
        $ename = strtolower( basename(str_replace('\\', '/',get_called_class())) );
        return $this->description ? '<p class="' . Manifest::PREFIX . '-element-description ' . Manifest::PREFIX . '-' . $ename . '-description">' .  $this->description . '</p>' : '';
    }

    public function get_helptext() {
        $ename = strtolower( basename(str_replace('\\', '/',get_called_class())) );
        return $this->helptext ? '<span class="' . Manifest::PREFIX . '-helptext ' . Manifest::PREFIX . '-' . $ename . '-helptext">' . esc_html( $this->helptext) . '</span>' : '' ;
    }

    public function get_label() {
        $ename = strtolower( basename(str_replace('\\', '/',get_called_class())) );
        return $this->labels ?'<'. $this->label_element .' class="' . Manifest::PREFIX . '-label ' . Manifest::PREFIX . '-' . $ename . '-label">' . esc_html( $this->label) . '</'. $this->label_element .'>' : '' ;
    }

    public function get_css_class() {
        
        $ename = strtolower( basename(str_replace('\\', '/',get_called_class())) );
        return Manifest::PREFIX . '-element' . ' ' . Manifest::PREFIX . '-' . $ename . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }

    public function get_disabled() {
        return $this->disabled ? 'disabled' : '' ;
    }

}