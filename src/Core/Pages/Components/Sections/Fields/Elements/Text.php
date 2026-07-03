<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Text extends Element implements SettingsInterface {

    
    public function render() {

        $label =  $this->get_label() ;
        $value = esc_attr( $this->value );
        $name = esc_attr( $this->name );
        $description = $this->get_description() ;
        $class = $this->get_css_class();
        $style = $this->style;
        $helptext = $this->get_helptext() ;
        $disabled = $this->get_disabled();

        $html = <<<HTML
            <fieldset class="{$class}">
                {$label}
                <label>
                    <input
                        type="text"
                        name="{$name}"
                        id="{$name}"
                        value="{$value}"
                        style="{$style}"
                        {$disabled}
                    />
                    {$helptext}
                </label>
                {$description}
            </fieldset>
        HTML;
        return $html;
    }

    
    public function sanitize( $option_value ) {
        return sanitize_text_field( $option_value );
    }

}