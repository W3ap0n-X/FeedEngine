<?php
namespace Qck\FeedEngine\Core\Data;
// Prevent direct access to files
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Metabox;
abstract class BaseCommentMeta extends BaseMetaBox {
    public function get_meta_type(): string { return 'comment'; }

    // Define what type of comments this "Box" handles
    abstract public function get_comment_type(): array;

    public function get_screen(): string|array {
        return 'comment';
    }

    public function get_meta_hook(): array {
        return ['add_meta_boxes_comment'];
    }

    public function get_meta_save_hook(): array {
        return ['edit_comment'];
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
        ], $this);
        $this->metabox->boot_sections($_GET['c']?? 0, $this );

        foreach ($this->get_meta_hook() as $hook) {
            add_action($hook, function( $object) use ($section_id) {
                if(!empty($object->comment_type) && ( empty($this->get_comment_type()) || in_array($object->comment_type, $this->get_comment_type()) )){
                    $this->metabox->register();
                    
                }
            });
        }

        foreach ($this->get_meta_save_hook() as $hook) {
            add_action($hook, [$this, 'save_data']);
        }
    }


    public function render_wrapper($comment) {
        \Qck\FeedEngine\Core\Debug::logDump( $comment->comment_type, __METHOD__ . ' $comment->comment_type', 800);
        $types = $this->get_comment_type();
        if(empty($types)) {return parent::render_wrapper($comment);}
        else {
            foreach ($types as  $type) {
                if( $comment->comment_type === $type ) {return parent::render_wrapper($comment);}
            }
        }
        return;
    }

}