<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\CPT\BaseMetaBox;

class ShopifySettings extends BaseMetaBox {
    public function get_name(): string { return 'feed_shopify'; }
    public function get_title(): string { return 'Shopify Settings'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }

    // public function get_context(): string {
    //     return 'side' ;
    // }

    public function get_priority(): string {
        return 'low' ;
    }


    public function get_schema(): array {
        $entries = [];


        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'include_shopify',
            label: 'Enable Shopify',
            type: 'checkbox',
            default: false ,
        );

        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'image_placeholder_select',
            label: 'Image Placeholder Type',
            type: 'select',
            default: 'none' ,
            options: [ 
                'none' => 'None', 
                'feed_image' => 'Use Feed Featured Image' ,
                'custom' => 'Custom'
            ],
        );

        $entries[] = new \Qck\FeedEngine\Core\Options\OptionEntry(
            key: 'image_placeholder',
            label: 'Custom Placeholder',
            type: 'image',
        );

        
        return $entries;
    }
}