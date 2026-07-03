<?php
/*
namespace Qck\FeedEngine\Shortcodes;
// use Qck\FeedEngine\Core\Debug;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Shortcodes\Shortcode;

class TaxonomyImage implements Shortcode {

    private $prefix = Manifest::PREFIX . '_';

    private $atts = array(
        'term'=> null,

    );

    public function get_tag(): string { return $this->prefix . 'get_term_feed_img'; }

    public function render( $atts, $content = null ) : string{
        $term = get_queried_object(); 
        if ( ! $term || ! isset( $term->term_id ) ) return 'fail';
        // Fetch the metadata (replace 'your_meta_key' with your actual key)
        // $meta_value = get_term_meta( $term->term_id, '_qckfe_feed-attributes', true );

        

        

        // return wp_get_attachment_image( $meta_value['feed_image'], 'large', false, [
        //     'class' => 'elementor-term-image',
        //     'loading' => 'eager' // Use 'eager' if this is your LCP element above the fold
        // ]);

        return '';



    }

    public function get_name(): string {
        return "Feed Info";
    }
    public function get_description(): string {
        return "Testing";
    }
    public function get_example(): string {
        return "Testing";
    }
}

/*
*/