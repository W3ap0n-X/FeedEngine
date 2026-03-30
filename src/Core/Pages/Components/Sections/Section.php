<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;

use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Field;
use Qck\FeedEngine\Core\Debug;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Section {

    /**
     * @var Field[] Section field objects.
     */
    protected $fields = array();

    /**
     * @var Options An instance of `Options`.
     */
    private $options;

    /**
     * @var string Section title.
     */
    private $title;

    /**
     * @var string Section ID.
     */
    private $id;

    /**
     * @var string Slug-name of the settings page this section belongs to.
     */
    private $page;

    /**
     * @var string Section description.
     */
    private $description;

    /**
     * Section constructor.
     *
     * @param string  $id               Section ID.
     * @param string  $page             Slug-name of the settings page.
     * @param Options $options_instance An instance of `Options`.
     * @param array   $properties       Properties.
     */
    public function __construct( $id, $page, $options_instance, $properties = array() ) {
        
        $dump_me = [$id, $page,$properties];
        \Qck\FeedEngine\Core\Debug::logDump($dump_me, __METHOD__);
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( 'Section', 'glave' ),
                'description' => ''
            )
        );

        $this->options = $options_instance;

        $this->title       = $properties['title'];
        $this->description = $properties['description'];
        $this->page        = $page;
        $this->id          = $id;

        add_settings_section(
            $id,
            $this->title,
            array( $this, 'print_description' ),
            $page
        );
    }

    /**
     * Print the section description.
     */
    public function print_description() {
        echo esc_html( $this->description );
    }

    /**
     * Create and add a new field object to this section.
     *
     * @param array $properties Field properties.
     */
    public function add_field( $properties ) {
        \Qck\FeedEngine\Core\Debug::logDump($properties, __METHOD__);
        $field = new Field( $this->id, $this->page, $this->options, $properties );

        $this->fields[] = $field;

        return $field;
    }

}