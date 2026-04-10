<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\CPT\BaseMetaBox;

class FeedSettings extends BaseMetaBox {
    public function get_name(): string { return 'feed_settings'; }
    public function get_title(): string { return 'Feed Configuration'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }

    

    public function get_schema(): array {
        return [
            
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'items_per_page',
                label: 'Total Items',
                type: 'number',
                default: 6 ,
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'orderby',
                label: 'Item order',
                type: 'select',
                default: 'date' ,
                options: [ 
                    'date' => 'Date', 
                    'title' => 'Title' ,
                    'rand' => 'Random',
                    'ID' => 'ID',
                    'menu_order' => 'Menu Order???',
                    'modified' => 'Modified',
                    'post__in' => 'Manual',
                ],
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'image_placeholder_select',
                label: 'Image Placeholder Type',
                type: 'select',
                default: 'none' ,
                options: [ 
                    'none' => 'None', 
                    'feed_image' => 'Use Feed Featured Image' ,
                    'custom' => 'Custom'
                ],
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'image_placeholder',
                label: 'Custom Placeholder',
                type: 'image',
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'manual_ids',
                label: 'Custom Placeholder',
                type: 'custom',
                html: new \Qck\FeedEngine\Pages\Components\PostSearch
            ),

        ];
    }
}