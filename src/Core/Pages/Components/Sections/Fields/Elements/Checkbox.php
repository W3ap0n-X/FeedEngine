<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Checkbox extends Element implements SettingsInterface {
    protected $helptext = 'Enable';

    


    public function render() {
        $name = json_encode(esc_attr( $this->name ));
        ob_start();
        checked( '1', $this->value , true);
        $checked = ob_get_clean();
        $label =  $this->get_label() ;
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $class = $this->get_css_class();
        $style = $this->style;
        $disabled = $this->get_disabled();
        $html = <<<HTML
        
            <fieldset class="{$class}">
                {$label}

                <label>
                    <input 
                        type="hidden" 
                        name={$name} 
                        value="0" 
                        {$disabled}
                    />
                    <input
                        type="checkbox"
                        name={$name} 
                        name={$name} 
                        value="1"
                        style="{$style}"
                        {$checked}
                        {$disabled}
                    />
                    {$helptext}
                </label>
                {$description}
            </fieldset>
        HTML;
        return  $html;
    }

    
    public function sanitize( $option_value ) {
        \Qck\FeedEngine\Core\Debug::logDump( $option_value, __METHOD__ . ' ## ' . $this::class . ' | $option_value');
        // return rest_sanitize_boolean($option_value );
        return ( '1' === (string) $option_value || true === $option_value );
    }

}