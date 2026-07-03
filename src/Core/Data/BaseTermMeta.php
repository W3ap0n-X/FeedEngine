<?php
namespace Qck\FeedEngine\Core\Data;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Metabox;
abstract class BaseTermMeta extends BaseMetaBox {

    public function get_meta_type(): string {
        return "term";
    }

    public function get_taxonomies(): array {
        return [];
    }

    public function get_term_types(): array {
        $term_types = $this->get_taxonomies();
        if( empty( $term_types ) ) {
            $taxonmomies = get_taxonomies();
            foreach($taxonmomies as $key => $name){
                $term_types[] = $name;
            }
        }
        return $term_types;
    }
    
    public function get_meta_hook(): array {
        $hooks = [];
        $term_types = $this->get_term_types();

        foreach ($term_types as $taxonomy) {
            $hooks[] = $taxonomy . '_add_form_fields' ;
            $hooks[] = $taxonomy . '_edit_form_fields' ;
        }




        return $hooks;
    }

    public function get_meta_save_hook(): array {
        $hooks = [];
        $term_types = $this->get_term_types();

        foreach ($term_types as $taxonomy) {
            $hooks[] = 'created_' . $taxonomy ;
            $hooks[] = 'edited_' . $taxonomy ;
        }




        return $hooks;
    }

    public function get_user_capabilities(): array {
        return ['edit_user'];
    }
    public function register() {

        
        $this->metabox = new Metabox(
            Manifest::PREFIX . '_' . $this->get_name(), 
            $this->get_screen(), 
            [
                'title' => $this->get_title(),
                'context' => $this->get_context(),
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

        // $term_types = $this->get_term_types();
        // \Qck\FeedEngine\Core\Debug::logDump( $term_types, __METHOD__ . ' ## ' . $this::class . ' > $term_types' , 920);

        // \Qck\FeedEngine\Core\Debug::logDump( ['render' => $this->get_meta_hook() , 'save' => $this->get_meta_save_hook()] , __METHOD__ . ' ## ' . $this::class . ' > Register Hooks' , 950);
        $this->metabox->boot_sections($_GET['tag_ID'] ?? 0, $this );

        foreach ($this->get_meta_hook() as $hook) {
            add_action($hook, function() use ($section_id) {
                $this->metabox->register();
                
            });
            add_action($hook, [$this, 'render_wrapper']);
        }

        foreach ($this->get_meta_save_hook() as $hook) {
            add_action($hook, [$this, 'save_data']);
        }
    }


}