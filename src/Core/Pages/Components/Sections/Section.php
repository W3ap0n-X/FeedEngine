<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Field;
use Qck\FeedEngine\Core\Debug;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Section {

    
    protected $fields = array();

    
    public $options;

    
    private $title;

    
    private $id;

    public function get_id(){
        return $this->id;
    }

    
    private $page;

    
    private $description;

    
    public function __construct( $section_id, $page, $options_instance, $properties = array() ) {
        
        // $dump_me = ['section_id'=>$section_id, 'page'=>$page,'properties'=>$properties, 'options'=>$options_instance];
        // \Qck\FeedEngine\Core\Debug::logDump($dump_me, __METHOD__);
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $section_id, Manifest::PREFIX ),
                'description' => ''
            )
        );

        $this->options = $options_instance;

        $this->title       = $properties['title'];
        $this->description = $properties['description'];
        $this->page        = $page;
        $this->id          = $section_id;
        // \Qck\FeedEngine\Core\Debug::logDump($this, __METHOD__ . "FIELD CHECK");
        add_settings_section(
            $section_id,
            $this->title,
            array( $this, 'print_description' ),
            $page
        );
    }

    
    public function print_description() {
        echo esc_html( $this->description );
    }

    
    public function add_field( $properties ) {
        $field = new Field( $this->id, $this->page, $properties );

        $this->fields[] = $field;

        return $field;
    }



}