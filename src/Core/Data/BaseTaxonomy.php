<?php 

namespace Qck\FeedEngine\Core\Data;

abstract class BaseTaxonomy {

    abstract public function get_slug(): string;

    abstract public function get_label(): string;

    abstract public function obj_types():  array;

    abstract public function public(): bool;

    abstract public function description(): string;

    abstract public function default_term(): null|string|array;

    public function get_metaboxes() : array {
        return [];
    }
    
    public function register() {
        register_taxonomy($this->get_slug(), $this->obj_types(), $this->get_args());
    }

    public function get_args(): array {
        return [
            'labels'                => $this->labels(),
            'description'           => $this->description(),
            'public'                => $this->public(),
            'publicly_queryable'    => $this->publicly_queryable(),
            'hierarchical'          => $this->hierarchical(),
            'show_ui'               => $this->show_ui(),
            'show_in_menu'          => $this->show_in_menu(),
            'show_in_nav_menus'     => $this->show_in_nav_menus(),
            'show_in_rest'          => $this->show_in_rest(),
            'rest_base'             => $this->rest_base(),
            'rest_namespace'        => $this->rest_namespace(),
            'rest_controller_class' => $this->rest_controller_class(),
            'show_tagcloud'         => $this->show_tagcloud(),
            'show_in_quick_edit'    => $this->show_in_quick_edit(),
            'show_admin_column'     => $this->show_admin_column(),
            'meta_box_cb'           => $this->meta_box_cb(),
            'meta_box_sanitize_cb'  => $this->meta_box_sanitize_cb(),
            'capabilities'          => $this->capabilities(),
            'rewrite'               => $this->rewrite(),
            'query_var'             => $this->query_var(),
            'update_count_callback' => $this->update_count_callback(),
            'default_term'          => $this->default_term(),
            'sort'                  => $this->sort(),
            'args'                  => $this->args(),
            
        ];
    }

    // Sensible defaults that can be overridden if needed
    public function get_singular_label(): string { 
        return rtrim($this->get_label(), 's'); 
    }

    public function labels(): array {
        return $this->default_labels();
    }

    /**
     * Automatic Label Generator
     * No more writing 'Add New Feed', 'Edit Feed', etc. 20 times.
     */
    public function default_labels(): array {
        $plural = $this->get_label();
        $singular = $this->get_singular_label();

        return [
            'name'               => $plural,
            'singular_name'      => $singular,
            'menu_name'          => $plural,
            'all_items'          => "All $plural",
            'edit_item'          => "Edit $singular",
            'view_item'          => "View $singular",
            'update_item'        => "Update $singular",
            'add_new_item'       => "Add New $singular",
            'parent_item'           => "Parent $singular",
            'parent_item_colon'           => "Parent $singular:",
            'search_items'       => "Search $plural",
            'popular_items'       => "Popular $plural",
            'separate_items_with_commas'       => "Separate $plural with commas",
            'add_or_remove_items'       => "Add or Remove $plural",
            'choose_from_most_used'       => "Choose from the most used $plural",
            'not_found'          => "No $plural found",
            'back_to_items'          => "← Back to $plural",
            
            
        ];
    }

    public function show_ui() : bool {
        return $this->public();
    }

    public function show_in_menu() : bool {
        return $this->show_ui();
    }

    public function show_in_rest(): bool {
        return false; 
    }

    public function rest_controller_class() : string|null {
        return null;
    }

    public function rest_base() : ?string {
        return $this->get_slug();
    }

    public function rest_namespace() : string {
        return 'wp/v2';
    }

    

    public function show_in_nav_menus() : bool {
        return $this->show_ui();
    }

    public function publicly_queryable() : bool {
        return $this->public();
    }

    public function hierarchical(): bool {
        return false;
    }

    public function capabilities(): array {
        return array(); 
    }

    public function rewrite(): array|bool {
        return true; 
    }

    public function query_var(): bool|string {
        return false; 
    }

    public function show_tagcloud(): bool {
        return true; 
    }

    public function show_in_quick_edit(): bool {
        return true; 
    }

    public function show_admin_column(): bool {
        return false; 
    }

    public function meta_box_cb(): null|bool|callable {
        return null; 
    }

    public function meta_box_sanitize_cb(): null|callable {
        return null; 
    }

    public function update_count_callback(): string|callable {
        return ''; 
    }

    public function sort(): null|bool {
        return null; 
    }

    public function args(): array {
        return array();
    }
}