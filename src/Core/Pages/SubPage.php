<?php
namespace Qck\FeedEngine\Core\Pages;

abstract class SubPage extends Admin {

	protected $parent_slug;

	public function __construct($parent_slug, $options){
		$this->parent_slug = $parent_slug;
		parent::__construct($options);;
	}

	/**
     * Add this page as a top-level menu page.
     * >>> Yeah... about that
     */
    public function add_page() {
        add_submenu_page(
            $this->parent_slug,   		// parent slug
            $this->get_page_title(),    // page_title
            $this->get_menu_title(),    // menu_title
            $this->get_capability(),    // capability
            $this->get_slug(),          // menu_slug
            array( $this, 'render' ),   // callback
            $this->get_position()       // position
        );
    }

    protected function get_page_prefix() {
        return 'glave_page_';
    }

}