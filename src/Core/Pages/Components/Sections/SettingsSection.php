<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\SettingsField;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SettingsSection extends Section {

    

    public function __construct( $section_id, $page, $options_instance, $properties = array() ) {
        
        parent::__construct( $section_id, $page, $options_instance, $properties );
        \Qck\FeedEngine\Core\Debug::logDump($this->hook, __METHOD__ . ' ## ' . $this::class . ' || ' . $options_instance::class, 850);

        add_action('admin_init', function() use ($section_id, $page) {
            add_settings_section(
                $section_id,
                $this->title,
                array( $this, 'print_description' ),
                $page,
                ['section_class' => $this->class]
            );
        });
        


    }
    

    public function get_css_class() {
        return Manifest::PREFIX . '-section' . ' ' . Manifest::PREFIX . '-settings-section' . ' ' . ( empty($this->class) ? '' : " " . esc_attr( $this->class ) )  ;
    }


}