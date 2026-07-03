<?php

namespace Qck\FeedEngine\Core\CPT;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;
use Qck\FeedEngine\Core\Pages\Components\Sections\SettingsSection;
use Qck\FeedEngine\Core\Pages\Components\Sections\Section;
use Qck\FeedEngine\Core\Pages\CommentPage;
abstract class BaseCommentType {
    use \Qck\FeedEngine\Core\Hooks\RegistersInternalHooks;
    abstract public function get_slug():string;
    abstract public function get_label():string;
    public function description():string|null{
        return null;
    }

    public function get_capability():string{
        return 'manage_options';
    }

    public function get_menu_title():string {
        return $this->get_label();
    }

    abstract public function get_options() : array ;

    public $sections = [];

    public function get_metaboxes() : array {
        return [];
    }

	// protected $hook_suffix; // We'll store the screen ID here

    protected $admin_page;
		
	
	public function register() {
        // $this->register_sections();
        // $this->do_settings();
        
		$this->admin_page = new CommentPage($this);
        $this->boot_internal_components(['page' => $this->admin_page]);
		add_filter( 'admin_comment_types_dropdown', function( $types ) {
			$types[ $this->get_slug() ] = __( $this->get_label(), 'text-domain' );
			return $types;
		}, 10, 2 );		
        
	}

    
	
	
	
	// Default placeholders for your column experiments
    public function add_custom_columns( $columns ) { return $columns; }
    public function render_custom_column_data( $column_name, $comment_id ) { }
	
	
	
}

