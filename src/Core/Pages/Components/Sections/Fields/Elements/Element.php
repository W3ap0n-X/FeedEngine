<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Interfaces\UI;
use Qck\FeedEngine\Core\Options\Options;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Element implements UI {

    const NUMBER_ELEMENT = 'Number';
    const TEXT_ELEMENT = 'Text';
    const RADIO_ELEMENT = 'Radio';
    const CHECKBOX_ELEMENT = 'Checkbox';
    const CUSTOM_ELEMENT = 'Custom';

    /**
     * @var int Number of elements instantiated.
     */
    private static $number_of_elements = 0;

    /**
     * @var array Element label.
     */
    protected $label;

    /**
     * @var array Element name.
     */
    protected $name;

    /**
     * @var mixed Element value.
     */
    protected $value;

    /**
     * @var string Element option name.
     */
    private $option_name;

    /**
     * @var callable|null Validation function.
     */
    private $validate;

    /**
     * @var callable|null Pre-write function.
     */
    private $pre_write;

    /**
     * Element constructor.
     *
     * @param string  $section_id       Section ID.
     * @param Options $options_instance An instance of `Options`.
     * @param array   $properties       Element properties.
     */
    public function __construct( $section_id, $properties = array() ) {
        self::$number_of_elements++;
        if ( $this instanceof SettingsInterface ) {
            $properties = wp_parse_args(
                $properties,
                array(
                    'label'     => sprintf(
                        /* translators: %s is the unique s/n of the element. */
                        __( 'Element #%s', Manifest::PREFIX ),
                        self::$number_of_elements
                    ),
                    'name'      => 'element_' . self::$number_of_elements,
                    'validate'  => null,
                    'pre_write' => null,
                    'post_read' => null
                )
            );

            $this->label       = $properties['label'];
            $this->option_name = $properties['name'];
            $this->name        = sprintf( '%s_%s', Manifest::PREFIX , $this->option_name );
            $this->validate    = $properties['validate'];
            $this->pre_write   = $properties['pre_write'];
            $this->value       = $properties['value'];
            if ( is_callable( $properties['post_read'] ) ) {
                $this->value = $properties['post_read']( $this->value );
            }
        }
    }

    /**
     * Return the element's option name.
     *
     * @return string
     */
    public function get_option_name() {
        return $this->option_name;
    }

    /**
     * Return the validation function.
     *
     * @return callable|null
     */
    public function get_validate() {
        return $this->validate;
    }

    /**
     * Return the current value of this element.
     *
     * @return mixed
     */
    public function get_value() {
        return $this->value;
    }

    /**
     * Return the `pre_write()` function.
     *
     * @return callable|null
     */
    public function get_pre_write() {
        return $this->pre_write;
    }

}