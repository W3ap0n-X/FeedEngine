<?php
namespace Qck\FeedEngine\Core\CPT;

abstract class BasePostType {

    protected $post_type;
    private function __construct($post_type){
        $this->post_type = $post_type;
    }

    abstract public function get_slug(): string;
    abstract public function get_label(): string;
    abstract public function labels(): array;
    abstract public function public(): bool;
    abstract public function description(): string;
    abstract public function supports(): array;


    public function hierarchical(): bool {
        return false;
    }

    public function menu_icon(): string {
        return 'dashicons-admin-post';
    }

    public function has_archive(): string|bool {
        return false; 
    }

    public function template_lock(): string|false {
        return false; 
    }
    
    /**
     * The Toggle: Set to false to "Usurp" with your own Endpoints
     */
    public function show_in_rest(): bool {
        return false; 
    }

    public function late_route_registration(): bool {
        return false; 
    }

    public function query_var(): bool {
        return false; 
    }

    public function map_meta_cap(): bool {
        return true; 
    }

    public function delete_with_user(): bool {
        return false; 
    }


    public function can_export(): bool {
        return true; 
    }

    public function menu_position(): ?int {
        return null; 
    }

    public function register_meta_box_cb(): ?callable {
        return null; 
    }

    public function taxonomies(): array {
        return array(); 
    }

    public function rewrite(): array {
        return array(); 
    }

    public function capabilities(): array {
        return array(); 
    }

    public function template(): array {
        return array(); 
    }

    public function get_args(): array {
        return [
            'label'               => $this->get_label(), // Fixed: Use $this
            'labels'              => $this->labels(),
            'description'         => $this->description(),
            'public'              => $this->public(),
            'show_in_rest'        => $this->show_in_rest(),
            'supports'            => $this->supports(),
            'menu_icon'           => $this->menu_icon(),
            'has_archive'         => $this->has_archive(),
            'hierarchical'        => $this->hierarchical(),
            'show_ui'             => $this->show_ui(),
            'show_in_menu'        => $this->show_in_menu(),
            'show_in_nav_menus'   => $this->show_in_nav_menus(),
            'show_in_admin_bar'   => $this->show_in_admin_bar(),
            'exclude_from_search' => $this->exclude_from_search(),
            'publicly_queryable'  => $this->publicly_queryable(),
            'rest_base'           => $this->rest_base(),
            'rest_namespace'      => $this->rest_namespace(),
            'query_var'           => $this->query_var(),
            'can_export'          => $this->can_export(),
            'menu_position'       => $this->menu_position(),
            'taxonomies'          => $this->taxonomies(),
            'rewrite'             => $this->rewrite(),
            'capability_type'     => $this->capability_type(),
            'capabilities'        => $this->capabilities(),
            'map_meta_cap'        => $this->map_meta_cap(), // Set this to true in your child classes!
            'delete_with_user'    => $this->delete_with_user(),
            'template'            => $this->template(),
            'template_lock'       => $this->template_lock(),
            'register_meta_box_cb'=> $this->register_meta_box_cb(),
            'rest_controller_class' => $this->rest_controller_class(),
        ];
    }

    public function rest_controller_class() : string|null {
        return null;
    }

    public function autosave_rest_controller_class() : string|null {
        return null;
    }

    public function revisions_rest_controller_class() : string|null {
        return null;
    }

    public function show_ui() : bool {
        return $this->public();
    }

    public function show_in_nav_menus() : bool {
        return $this->show_ui();
    }

    public function show_in_menu() : bool {
        return $this->show_ui();
    }

    public function show_in_admin_bar() : bool {
        return $this->show_in_nav_menus();
    }

    public function exclude_from_search() : bool {
        return !($this->public());
    }

    public function publicly_queryable() : bool {
        return $this->public();
    }

    public function embeddable() : bool {
        return $this->public();
    }

    public function rest_base() : bool {
        return $this->post_type;
    }

    public function rest_namespace() : string {
        return 'wp/v2';
    }

    public function capability_type() : string|array {
        return 'post';
    }

    public function register() {
        register_post_type( $this->get_slug(), $this->get_args() );
    }
}