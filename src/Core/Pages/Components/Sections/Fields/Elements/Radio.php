<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

use Qck\FeedEngine\Core\Options\Options;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Radio extends Element implements SettingsInterface {

    /**
     * @var array Radio values.
     */
    private $options = array();

    /**
     * Render the element.
     */
    public function render() {
        $name = esc_attr( $this->name );
        $group_label = esc_html( $this->label );
        $html = '<div>' . $group_label;

        foreach ( $this->options as $current_value => $label ) {
            $value = esc_attr( $current_value );
            
            $checked = checked( $this->value, $current_value , false);
            $html .= <<<HTML
                <fieldset>
                    <label>
                        <input
                            type="radio"
                            name="{$name}"
                            id="{$name}"
                            value="{$value}"
                            {$checked}
                        />
                        {$label}
                    </label>
                </fieldset>
            HTML;
        }
        $html .= '</div>';
        return $html;
            
    }

    /**
     * Radio_Field constructor.
     *
     * @param string  $section_id       Section ID.
     * @param Options $options_instance An instance of `Options`.
     * @param array   $properties       Element properties.
     */
    public function __construct( $section_id, $properties = array() ) {
        parent::__construct( $section_id, $properties );

        if ( isset( $properties['options'] ) ) {
            $this->options = $properties['options'];
        }

        if ( isset( $properties['value'] ) ) {
            $this->value = $properties['value'];
        }
    }

    /**
     * Sanitize the given option value.
     *
     * @param string $option_value
     *
     * @return string
     */
    public function sanitize( $option_value ) {
        return sanitize_text_field( $option_value );
    }

}