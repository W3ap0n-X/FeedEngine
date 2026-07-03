<?php 
namespace Qck\FeedEngine\Core\CPT;

use Qck\FeedEngine\Manifest;


class PostTypeManager {
    protected $post_types = [];

    public function register_all() {
        $dir = Manifest::path() . 'src/CPT/';
        if ( ! is_dir( $dir ) ) return;
        $files = glob( $dir . '*.php' );
        foreach ( $files as $file ) {
            
            $class_name = basename( $file, '.php' );
            $full_class = "\\Qck\\FeedEngine\\CPT\\" . $class_name;
            if (class_exists($full_class)) {
                $this->post_types[] = new $full_class();
                
                
            }
        }

        foreach ($this->post_types as $post_type) {
            $post_type->register( );
            foreach ($post_type->get_metaboxes() as $metabox) {
                $metabox->register();
            }
        }
    }
}