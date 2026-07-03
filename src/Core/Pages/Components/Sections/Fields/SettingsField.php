<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SettingsField extends Field {

    public function render() {
        $html = parent::render();
        echo $html;
    }

    
    public function __construct( $section_id, $page, $properties = array() ) {
        parent::__construct( $section_id, $page, $properties );

        add_action('admin_init', function() use ($section_id, $page) {
            add_settings_field(
                $this->id,
                $this->label,
                array( $this, 'render' ),
                $page,
                $section_id
            );
        });
            
    }

    
    public function get_css_class() {
        return Manifest::PREFIX . '-field' . ' ' . Manifest::PREFIX . '-settings_field' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }

}