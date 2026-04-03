<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Text extends Element implements SettingsInterface {

    /**
     * Render the element.
     */
    public function render() {

        $label = esc_html( $this->label );
        $value = esc_attr( $this->value );
        $name = esc_attr( $this->name );
        $html = <<<HTML
            <fieldset>
                <label>
                    <input
                        type="text"
                        name="{$name}"
                        id="{$name}"
                        value="{$value}"
                    />
                    {$label}
                </label>
            </fieldset>
        HTML;
        return $html;
    }

    /**
     * Sanitize the given option value.
     *
     * @param string $option_value
     *
     * @return bool
     */
    public function sanitize( $option_value ) {
        return sanitize_text_field( $option_value );
    }

}