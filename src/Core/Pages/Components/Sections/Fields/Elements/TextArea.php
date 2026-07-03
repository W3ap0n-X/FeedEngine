<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TextArea extends Element implements SettingsInterface {

    
    public function render() {

        $label =  $this->get_label() ;
        $value = esc_attr( $this->value );
        $name = esc_attr( $this->name );
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $class = $this->get_css_class();
        $disabled = $this->get_disabled();
        $style = $this->style;
        // $description = esc_attr( $this->description );
        $html = <<<HTML
            <fieldset class="{$class}">
                {$label}
                <label>
                    
                    <textarea name="{$name}" id="{$name}" style="{$style}" {$disabled} >{$value}</textarea>
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