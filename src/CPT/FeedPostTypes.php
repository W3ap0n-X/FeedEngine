<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\CPT\BaseMetaBox;

class FeedPostTypes extends BaseMetaBox {
    public function get_name(): string { return 'feed_post_types'; }
    public function get_title(): string { return 'Include Post Types'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }


    public function get_schema(): array {
        $entries = [];


        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'post',
            label: 'Posts',
            type: 'checkbox',
            default: true ,
        );

        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'page',
            label: 'Pages',
            type: 'checkbox',
            default: false ,
        );

        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'attachment',
            label: 'Media',
            type: 'checkbox',
            default: false ,
        );
    
        $args = array(
            'public'   => true,
            '_builtin' => false,
    
        );
        
        foreach (get_post_types($args, 'objects') as $post_type) {
            $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: $post_type->name,
                label: $post_type->label,
                type: 'checkbox',
                default: false ,
            );
        }
        return $entries;
    }
}