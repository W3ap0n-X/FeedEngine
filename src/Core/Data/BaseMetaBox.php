<?php
namespace Qck\FeedEngine\Core\Data;
// Prevent direct access to files
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Metabox;
use \Qck\FeedEngine\Core\Data\MetaData;
abstract class BaseMetaBox extends MetaData {

    public function get_meta_type(): string {
        return "post";
    }

    public $metabox;

    public $section;
    
    public function get_meta_hook(): array {
        return ['add_meta_boxes'];
    }

    public function get_meta_save_hook(): array {
        return ['save_post'];
    }
    

    public function get_context(): string {
        return 'normal' ;
    }

    public function get_priority(): string {
        return 'default' ;
    }

    public function get_user_capabilities(): array {
        return [];
    }

    public function get_css_class(): string|null {
        return null;
    }

    

    public function register() {
        
        $this->metabox = new Metabox(
            Manifest::PREFIX . '_' . $this->get_name(), 
            $this->get_screen(), 
            [
                'title'    => $this->get_title(),
                'context'  => $this->get_context(),
                'priority' => $this->get_priority(),
                'callback' => [$this, 'render_wrapper'],
                'description' => $this->get_description(),
            ]
        );
        
        $section_id = '_' . Manifest::PREFIX . '_' . $this->get_name();
        $this->metabox->add_section([
            'title' => $this->get_title(),
            'description' => $this->get_description(),
            'type' => $this->get_meta_type(),
            'meta' => true,
            'class' => $this->get_css_class(),
        ], $this);
        
        $this->metabox->boot_sections($_GET['post']??0, $this );
        foreach ($this->get_meta_hook() as $hook) {
            add_action($hook, function() use ($section_id) {
                $this->metabox->register();
                
            } );
        }

        foreach ($this->get_meta_save_hook() as $hook) {
            add_action($hook, [$this, 'save_data']);
        }
    }

    public function render_wrapper($object) {
        echo '<div class="data-wrap" data-prefix="' . Manifest::PREFIX . '">';

        // Output a clean Section Title since it won't have a native Metabox frame
        if ($this->get_meta_type() === 'user' | $this->get_meta_type() === 'term') {
            echo '<h2>' . esc_html($this->get_title()) . '</h2>';
        }

        // Pass the user/post down to your element builder
        $html = $this->metabox->render($object, $this);
        // $html = $this->metabox ? $this->metabox->render($object, $this) : $this->render_user_fields($object);
        $name = '_' . Manifest::PREFIX . '_' . $this->get_name();
        wp_nonce_field($name  . '_action', $name . '_nonce');
        echo $html;
        echo '</div>';
    }

    private function sanitize_before_save($name, $options){
        $filter_hook = Manifest::PREFIX . "/meta/{$this->get_meta_type()}/sanitize/{$name}";
        \Qck\FeedEngine\Core\Debug::logDump( 'Checking for filter hook: ' . $filter_hook, __METHOD__ . ' Sanitization' , 725);
        if ( has_filter( $filter_hook ) ) {
            \Qck\FeedEngine\Core\Debug::logDump( 'Doing Filter sanitize', __METHOD__ . ' Sanitization' , 725);
            $sanitized_options = apply_filters($filter_hook , $options );
        } 
        else {
            \Qck\FeedEngine\Core\Debug::logDump( 'Checking for filter Local access: ' . $filter_hook, __METHOD__ . ' Sanitization' , 250);
            if(!empty($this->metabox->sections  && false)) {
                \Qck\FeedEngine\Core\Debug::logDump( 'Doing Metabox sanitize', __METHOD__ . ' Sanitization' , 725);
                $sanitized_options = $this->metabox->sanitize($options);
            } else if(!empty($this->sections)){
                \Qck\FeedEngine\Core\Debug::logDump( 'Doing sections sanitize', __METHOD__ . ' Sanitization' , 725);
                $sanitized_options = $this->section->sanitize($options);
            }
            else {
                \Qck\FeedEngine\Core\Debug::logDump( 'No can do, Doing Raw entry', __METHOD__ . ' Sanitization', 0 );
                $sanitized_options = $options;
            }
            
        }
        return $sanitized_options;
    }

    public function save_data($obj_id) {
        if (!$this->check_permissions($obj_id)) {
            \Qck\FeedEngine\Core\Debug::logDump( 'Invalid Permissions', __METHOD__ . ' ## ' . $this::class, 0);
        }
        
        $name = '_' . Manifest::PREFIX . '_' . $this->get_name();
        if (!isset($_POST[$name . '_nonce'])) {
            \Qck\FeedEngine\Core\Debug::logDump( 'nonce missing', __METHOD__ . ' ## ' . $this::class, 0);
            return;
        }
        if (!wp_verify_nonce($_POST[$name . '_nonce'], $name . '_action')) {
            \Qck\FeedEngine\Core\Debug::logDump( 'nonce unverified', __METHOD__ . ' ## ' . $this::class, 0);
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        
        if (isset($_POST[$name])) {
            $options = $this->sanitize_before_save($name, $_POST[$name]);
            $this->save_values($obj_id, $options);
        }
    }

    protected function check_permissions($obj_id){
        $caps = $this->get_user_capabilities();
        if(empty($caps)) { return true; }


        foreach ($this->get_user_capabilities() as $capability) {
            if(current_user_can($capability, $obj_id)){
                return true;
            }
        }
        return false;
    }

}