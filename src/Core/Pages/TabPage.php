<?php

namespace Qck\FeedEngine\Core\Pages;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Options\OptionSection;

use Qck\FeedEngine\Core\Pages\Components\Utility\SubmitButton;
use Qck\FeedEngine\Core\Pages\Components\Sections\SettingsSection;
abstract class TabPage extends Admin {

    protected $parent_slug;
    protected $tab_slug;

    public function __construct($parent_slug, $tab_slug, $hooks) {
        // \Qck\FeedEngine\Core\Debug::logDump( '', __METHOD__ . ' ## ' . $this::class );
        $this->parent_slug = $parent_slug;
        $this->tab_slug = $tab_slug;
        parent::__construct($hooks);
    }

    /**
     * For a Tab, we skip add_menu_page/add_submenu_page entirely.
     * The Parent Page handles the entry point.
     */
    public function add_page() {
        // Leave empty or use for tab-specific logic
        return; 
    }

    abstract public function get_menu_title() ;

    public function get_page_title() {
        // Leave empty or use for tab-specific logic
        return; 
    }

    /**
     * We override the prefix logic so it matches the parent's context
     */
    protected function get_page_prefix() {
        return 'tab_';
    }

    public function get_slug() {
        return $this->parent_slug . '_' . $this->tab_slug;
        
    }

    public function get_tab_slug() {
        return $this->tab_slug;
    }

    public function get_parent_slug() {
        return $this->parent_slug;
    }

    protected function add_section( $option_section ) {
        if ( ! ( $option_section instanceof OptionSection ) ) {
            return;
        }
        $section = new SettingsSection( 
            $option_section->get_db_row(), 
            $this->get_slug(), 
            $option_section, 
            ['title' => $option_section->get_title(), 'description' => $option_section->get_description()] 
        );
        $this->sections[] = $section;

        register_setting(
            $this->get_slug(),
            $option_section->get_db_row(),
            array( 'sanitize_callback' => array( $section, 'sanitize' ) )
        );
        
        return $section;
    }

}