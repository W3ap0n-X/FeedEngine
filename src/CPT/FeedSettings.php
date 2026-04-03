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
                key: 'layout_pattern',
                label: 'Pattern Ratio',
                type: 'text',
                placeholder: 'e.g., 2,1',
                default: '' ,
            ),
            // Nested Example: qckfe_general_options[api][key]
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'items_per_page',
                label: 'Total Items',
                type: 'number',
                path: ['api'] ,
                default: 6 ,
            ),
            // new \Qck\FeedEngine\Core\Options\OptionEntry(
            //     key: '213',
            //     label: 'New Setting',
            //     type: 'text',
            //     path: ['example'] 
            // ),
            // 'source_type' => [
            //     'type' => 'select',
            //     'label' => 'Data Source',
            //     'options' => [
            //         'wp'      => 'WordPress Posts',
            //         'shopify' => 'Shopify Products',
            //         'mixed'   => 'Mixed (Checkerboard)'
            //     ]
            // ],
            // 'layout_pattern' => [
            //     'type' => 'text',
            //     'label' => 'Pattern Ratio',
            //     'placeholder' => 'e.g., 2,1'
            // ],
            // 'items_per_page' => [
            //     'type' => 'number',
            //     'label' => 'Total Items',
            //     'default' => 12
            // ]
        ];
    }
}