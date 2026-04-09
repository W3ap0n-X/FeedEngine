<?php
namespace Qck\FeedEngine\Engine\Adapters;
use Qck\FeedEngine\Engine\Data\FeedItem;
class PostAdapter {
    public static function map(\WP_Post $post): FeedItem {
        $item = new FeedItem();
        $item->id        = (string) $post->ID;
        $item->title     = get_the_title($post);
        $item->url       = get_permalink($post);
        $item->source      = 'wp';
        $item->type      = $post->post_type;
        
        // Handling the "Bizarre" WP Image Logic
        $item->image_url = get_the_post_thumbnail_url($post, 'large') ?: '';
        
        // Check for price in meta if this is a CPT
        $item->price     = get_post_meta($post->ID, '_price', true) ?: '';
        
        return $item;
    }
}