<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Number extends Element implements SettingsInterface {

    
    public function render() {
        $label =  $this->get_label() ;
        
        $value = esc_attr( $this->value );
        $name = esc_attr( $this->name );
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $class = $this->get_css_class();
        $style = $this->style;
        $disabled = $this->get_disabled();
        $html = <<<HTML
            <fieldset class="{$class}" >
                {$label}
                <label>
                    <input
                        type="number" 
                        name="{$name}" 
                        id="{$name}" 
                        style="{$style}"

                        
                        value="{$value}" 
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
        return intval( $option_value );
    }

}