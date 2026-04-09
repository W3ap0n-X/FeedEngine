<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\CPT\BaseMetaBox;

class FeedCategories extends BaseMetaBox {
    public function get_name(): string { return 'feed_categories'; }
    public function get_title(): string { return 'Include Categories'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }


    public function get_schema(): array {
        $entries = [];


        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'uncategorized',
            label: 'Uncategorized',
            type: 'checkbox',
            default: true ,
        );


    
        $args = array(
            'taxonomy' => 'category',
            'hide_empty' => false,
            'exclude' => 1,
    
        );
        
        foreach (get_terms($args) as $category) {
            $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: $category->slug,
                label: $category->name,
                type: 'checkbox',
                default: false ,
            );
        }
        return $entries;
    }
}