<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields;
// Prevent direct access to files
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MetaField extends Field {

    
    // protected static $number_of_fields = 0;

    
    // protected $elements = array();

    
    // private $options;

    
    // protected $section_id;

    
    // protected $description;

    /**
     * Render the UI element.
     *
     * @return string
     */
    public function render() {
        $prefix = Manifest::PREFIX;
        $name = esc_attr( $this->name );
        $label =  $this->get_label() ;
        $description = $this->get_description() ;
        $class = $this->get_css_class();


        $content = '';
        foreach ( $this->elements as $key => $element ) {
            $content .= $element->render();
        }

        $html = <<<HTML
            <fieldset class="{$class}" name="{$name}">
                {$label}
                    <fieldset class="{$prefix}-field-content" name="{$name}">
                        
                        {$content}
                    </fieldset>
                {$description}
            </fieldset>
        HTML;
        return $html;
    }



    
    public function get_elements() {
        return $this->elements;
    }
    public function get_css_class() {
        return Manifest::PREFIX . '-field' . ' ' . Manifest::PREFIX . '-meta_field' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }

}