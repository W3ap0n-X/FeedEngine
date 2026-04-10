<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\CPT\BaseMetaBox;

class FeedPostTags extends BaseMetaBox {
    public function get_name(): string { return 'feed_tags'; }
    public function get_title(): string { return 'Include Tags'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }

    public function get_context(): string {
        return 'side' ;
    }

    public function get_priority(): string {
        return 'low' ;
    }


    public function get_schema(): array {
        $entries = [];


    
        $args = array(
            'taxonomy' => 'post_tag',
            'hide_empty' => false,
    
        );
        
        foreach (get_terms($args) as $tag) {
            $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: $tag->slug,
                label: $tag->name,
                type: 'checkbox',
                default: false ,
            );
        }
        return $entries;
    }
}