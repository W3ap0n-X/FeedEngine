<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Radio extends Element implements SettingsInterface {

    
    private $options = array();


    public function render() {
        $prefix = Manifest::PREFIX;
        $name = esc_attr( $this->name );
        $group_label =  $this->get_label() ;
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $options = '';
        $class = $this->get_css_class();
        $disabled = $this->get_disabled();
        $style = $this->style;
        foreach ( $this->options as $current_value => $label ) {
            if(array_is_list($this->options)){
                $value = $this->list_keys ? esc_attr( $current_value ) : esc_attr( $label );
            } else {
                $value = esc_attr( $current_value );
            }

            ob_start();
            checked( $this->value, $value , true);

            $checked = ob_get_clean();
            
            
            $options .= <<<HTML
                
                    <input  
                        type="radio"
                        name="{$name}"
                        id="{$name}"
                        value="{$value}"
                        style="{$style}"
                        {$checked}
                        {$disabled}
                    />
                    {$label}
                
            HTML;
        }

        $html = <<<HTML
            <fieldset class="{$class}">
                {$group_label}
                <label>
                    
                    <fieldset class="{$prefix}-options-wrapper"> 
                    {$options}
                    </fieldset>
                    
                    {$helptext}
                </label>
                {$description}
            </fieldset>
        HTML;
        return $html;
            
    }

    
    public function __construct( $section_id, $properties = array() ) {
        parent::__construct( $section_id, $properties );

        if ( isset( $properties['options'] ) ) {
            $this->options = $properties['options'];
        }

        if ( isset( $properties['value'] ) ) {
            $this->value = $properties['value'];
        }
    }

    
    public function sanitize( $option_value ) {
        return sanitize_text_field( $option_value );
    }

}