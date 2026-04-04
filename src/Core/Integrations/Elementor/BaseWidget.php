<?php 
namespace Qck\FeedEngine\Core\Integrations\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Qck\FeedEngine\Core\Options\OptionEntry;
use Qck\FeedEngine\Core\Options\OptionSection;

abstract class BaseWidget extends Widget_Base {

    
    abstract protected function get_schema(): OptionSection;

    public function get_categories() {
        return [ 'qck-feed-engine' ]; // Group your widgets together
    }

    protected function register_controls() {
        $schema = $this->get_schema();

        $this->start_controls_section(
            'section_content',
            [
                'label' => $schema->get_title(),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        foreach ( $schema->define_fields() as $entry ) {
            $this->add_control(
                $entry->key,
                $this->map_entry_to_control( $entry )
            );
        }

        $this->end_controls_section();
    }

    
    private function map_entry_to_control( OptionEntry $entry ): array {
        $control_type = match ( $entry->type ) {
            'number'   => Controls_Manager::NUMBER,
            'checkbox' => Controls_Manager::SWITCHER,
            'select'   => Controls_Manager::SELECT,
            'color'    => Controls_Manager::COLOR,
            default    => Controls_Manager::TEXT,
        };

        return [
            'label'   => $entry->label,
            'type'    => $control_type,
            'default' => $entry->default,
            'description' => $entry->description ?? '',
        ];
    }

    
    protected function render() {
        $settings = $this->get_settings_for_display();
        $template = $this->get_template_name();

        // Path to your template folder
        $template_path = \Qck\FeedEngine\Manifest::get_template_path( $template );

        if ( file_exists( $template_path ) ) {
            include $template_path;
        } else {
            printf( '<pre>Template not found: %s</pre>', esc_html( $template ) );
        }
    }

    abstract protected function get_template_name(): string;
}


/*
namespace Qck\FeedEngine\Integrations\Elementor;

use Qck\FeedEngine\Core\Integrations\Elementor\BaseWidget;
use Qck\FeedEngine\Options\BentoOptions;

class BentoWidget extends BaseWidget {

    public function get_name() { return 'qck_bento_grid'; }
    public function get_title() { return 'Bento Feed Grid'; }
    public function get_icon() { return 'eicon-gallery-grid'; }

    protected function get_schema(): \Qck\FeedEngine\Core\Options\OptionSection {
        return new BentoOptions(); // Reusing the exact same logic from the settings page!
    }

    protected function get_template_name(): string {
        return 'bento-grid-view.php';
    }
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $widgets_manager->register( new \Qck\FeedEngine\Integrations\Elementor\BentoWidget() );
} );

*/