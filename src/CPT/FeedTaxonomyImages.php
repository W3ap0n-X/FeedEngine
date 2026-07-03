<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;
use Qck\FeedEngine\Core\Data\BaseTermMeta;
use Qck\FeedEngine\Manifest;

class FeedTaxonomyImages extends BaseTermMeta {
    public function get_name(): string { return 'feed-attributes'; }
    public function get_title(): string { return 'Feed Engine - Feed Attributes'; }
    public function get_screen(): array { return []; }


    public function get_taxonomies(): array {
        return [];
    }
    

    public function get_schema(): array {
        return [
            
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'feed_image',
                label: 'Featured Feed Image',
                type: 'image',
            ),

        ];
    }
}