<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

use Qck\FeedEngine\Core\Options\Options;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class DropDownList extends Element implements SettingsInterface {
    protected $helptext = 'Select An Option';



    
    private $options = array();

    
    public function render() {
        $name = esc_attr( $this->name );
        $group_label =  $this->get_label() ;
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $options = '';
        $class = $this->get_css_class();
        $disabled = $this->get_disabled();
        $style = $this->style;

        // \Qck\FeedEngine\Core\Debug::logDump( $this->options, __METHOD__ . ' this->options');

        foreach ( $this->options as $current_value => $label ) {
            if(array_is_list($this->options)){
                $value = $this->list_keys ? esc_attr( $current_value ) : esc_attr( $label );
            } else {
                $value = esc_attr( $current_value );
            }
            

            ob_start();
            checked( $this->value, $value , true);

            $checked = ob_get_clean();
            
            $selected = $checked ? 'selected' : '';


            $options .= <<<HTML
                <option  
                    name="{$name}"
                    id="{$name}"
                    value="{$value}"
                    {$selected}

                    >
                    {$label}
                </option>
            HTML;
        }


        
        $html = <<<HTML
            <fieldset class="{$class}">
                {$group_label}
                <label>
                    
                    <select 
                        name="{$name}"
                        id="{$name}"
                        style="{$style}"
                        {$disabled}
                    >
                    {$options}
                    </select>
                    {$helptext}
                </label>
                {$description}
            </fieldset>
        HTML;

        
        return $html;
            
    }

    
    public function __construct( $section_id, $properties = array() ) {
        parent::__construct( $section_id, $properties );
        // \Qck\FeedEngine\Core\Debug::logDump( $properties, __METHOD__ . ' $properties');
        // \Qck\FeedEngine\Core\Debug::logDump( $properties['options'], __METHOD__ . ' $properties[\'options\']');

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