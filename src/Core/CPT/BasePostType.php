<?php
namespace Qck\FeedEngine\Core\CPT;

abstract class BasePostType {
    abstract public function get_slug(): string;
    abstract public function get_labels(): array;
    
    /**
     * The Toggle: Set to false to "Usurp" with your own Endpoints
     */
    public function show_in_rest(): bool {
        return true; 
    }

    public function get_args(): array {
        return [
            'labels'       => $this->get_labels(),
            'public'       => true,
            'show_in_rest' => $this->show_in_rest(), // THE TOGGLE
            'supports'     => ['title', 'editor', 'thumbnail'],
            'menu_icon'    => 'dashicons-rss',
            'has_archive'  => true,
        ];
    }

    public function register() {
        register_post_type( $this->get_slug(), $this->get_args() );
    }
}