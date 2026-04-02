<?php 
namespace Qck\FeedEngine\CPT\MetaBoxes;

use Qck\FeedEngine\Core\CPT\BaseMetaBox;

class FeedSettings extends BaseMetaBox {
    public function get_name(): string { return 'qckfe_feed_settings'; }
    public function get_title(): string { return 'Feed Configuration'; }
    public function get_screen(): array { return ['qckfe-feed' ]; }


    public function get_values($post_id): array {
        return get_post_meta( $post_id );
    }

    public function get_value_for_entry($post_id, \Qck\FeedEngine\Core\Options\OptionEntry $entry) {
        $all_data = $this->get_values(); // Fetches the whole DB row
        \Qck\FeedEngine\Core\Debug::logDump( $all_data, __METHOD__);
        // // If there's no path, just grab the key from the top level
        // if (empty($entry->path)) {
        //     return $all_data[$entry->key] ?? $entry->default;
        // }

        // // Walk the path
        // $current = $all_data;
        // foreach ($entry->path as $step) {
        //     if (isset($current[$step]) && is_array($current[$step])) {
        //         $current = $current[$step];
        //     } else {
        //         return $entry->default; // Path broken, return default
        //     }
        // }

        // return $current[$entry->key] ?? $entry->default;
    }

    public function get_schema(): array {
        return [
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'layout_pattern',
                label: 'Pattern Ratio',
                type: 'text',
                placeholder: 'e.g., 2,1'
            ),
            // Nested Example: qckfe_general_options[api][key]
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'items_per_page',
                label: 'Total Items',
                type: 'number',
                path: ['api'] 
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