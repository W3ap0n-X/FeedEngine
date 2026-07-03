<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;
use Qck\FeedEngine\Manifest;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Image extends Element implements SettingsInterface {

    


    public function render() {
        $prefix = Manifest::PREFIX;
        $val = $this->value;
        $image_url = $this->value ? wp_get_attachment_image_url($this->value, 'medium') : '';
        $display = $this->value ? 'block' : 'none';
        $name = json_encode(esc_attr( $this->name ));
        $description = $this->get_description() ;
        $helptext = $this->get_helptext() ;
        $class = $this->get_css_class();
        $disabled = $this->get_disabled();
        $style = $this->style;
        
        // ob_start();
        // checked( '1', $this->value , true);
        // $checked = ob_get_clean();
        $label =  $this->get_label() ;
        $html = <<<HTML
            <fieldset class="{$class}">
                {$label}
                <label>
                    
                    <div class="{$prefix}-image-preview-wrapper" style="{$style}">
                        <img class="{$prefix}-image-preview" src="{$image_url}" style="max-width:200px; display:{$display};">
                        <input type="hidden" name={$name}  class="{$prefix}-image-id" value="{$val}" {$disabled} >
                        <button type="button" class="button {$prefix}-select-img upload-image-button">Select Image</button>
                        <button type="button" class="button {$prefix}-remove-img remove-image-button" style="display:{$display};">Remove</button>
                    </div>
                    {$helptext}
                </label>
                {$description}
            </fieldset>
        HTML;
        return $html;
    }

    
    public function sanitize( $option_value ) {
        return ( '1' === (string) $option_value || true === $option_value );
    }

}