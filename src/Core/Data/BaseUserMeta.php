<?php
namespace Qck\FeedEngine\Core\Data;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Pages\Components\Metabox;
abstract class BaseUserMeta extends BaseMetaBox {

    public function get_meta_type(): string {
        return "user";
    }
    
    public function get_meta_hook(): array {
        return ['show_user_profile','edit_user_profile'];
    }

    public function get_meta_save_hook(): array {
        return ['personal_options_update','edit_user_profile_update'];
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
        $this->metabox->boot_sections($_GET['user_id'] ?? get_current_user_id(), $this );

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