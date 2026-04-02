<?php
namespace Qck\FeedEngine\Core\Pages\Components;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\Options;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\MetaField;
use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Metabox {

    /**
     * @var Field[] Section field objects.
     */
    public $fields = array();

    /**
     * @var Options An instance of `Options`.
     */
    public $options;

    /**
     * @var string Section title.
     */
    private $title;

    /**
     * @var string Section ID.
     */
    private $id;

    public function get_id(){
        return $this->id;
    }

    /**
     * @var string Slug-name of the settings page this section belongs to.
     */
    private $page;

    /**
     * @var string Section description.
     */
    private $description;


    public $callback;
    public $context;
    public $priority;

    /**
     * Section constructor.
     *
     * @param string  $id               Section ID.
     * @param string  $page             Slug-name of the settings page.
     * @param Options $options_instance An instance of `Options`.
     * @param array   $properties       Properties.
     */
    public function __construct( $section_id, $page, $properties = array() ) {
        
        // $dump_me = ['section_id'=>$section_id, 'page'=>$page,'properties'=>$properties, 'options'=>$options_instance];
        // \Qck\FeedEngine\Core\Debug::logDump($dump_me, __METHOD__);
        $properties = wp_parse_args(
            $properties,
            array(
                'title'       => __( $section_id, Manifest::PREFIX ),
                'description' => ''
            )
        );

        // $this->options = $options_instance;

        $this->title       = $properties['title'];
        $this->description = $properties['description'];
        $this->page        = $page;
        $this->id          = $section_id;
        $this->priority          = $properties['priority'];
        $this->context          = $properties['context'];
        $this->callback          = $properties['callback'];

        // \Qck\FeedEngine\Core\Debug::logDump($this, __METHOD__ . "FIELD CHECK");
        // add_settings_section(
        //     $section_id,
        //     $this->title,
        //     array( $this, 'print_description' ),
        //     $page
        // );
        
    }

    public function register(){
        add_meta_box(
            $this->id,
            $this->title,
            $this->callback,
            $this->page,
            $this->context ,
            $this->priority,
        );

    }

    /**
     * Create and add a new field object to this section.
     *
     * @param array $properties Field properties.
     */
    public function add_field( $properties ) {
        $field = new MetaField( $this->id, $this->page, $properties );

        $this->fields[] = $field;

        return $field;
    }

    public function render_wrapper($post) {
        // \Qck\FeedEngine\Core\Debug::logDump( $post, __METHOD__);
        // 1. Security Nonce
        wp_nonce_field($this->get_name() . '_action', $this->get_name() . '_nonce');

        // 2. Fetch existing values
        $values = get_post_meta($post->ID, '_qckfe_settings', true) ?: [];

        // 3. Reuse your SettingsBuilder!
        // $builder = new SettingBuilder($this->get_schema(), $values);
        echo '<div class="qckfe-metabox-wrapper">';
        echo '<h1>test</h1>';
        SettingBuilder::build_ui_from_metabox($post->ID, $this->metabox, $this->get_schema());
        echo '<h2>' . $post->ID . '</h2>';
        echo '<h2>' . count($values) . '</h2>';
        echo '<h2>' . count($this->metabox->fields) . '</h2>';
        // echo $content;
        // $metabox->render();
        // \Qck\FeedEngine\Core\Debug::logDump( $content, __METHOD__);
        // $builder->render();
        echo '</div>';
    }


}