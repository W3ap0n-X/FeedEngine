<?php
namespace Qck\FeedEngine\Public;

use WP_Query;
use Qck\FeedEngine\Manifest;

class FeedController {
    private static $rendered_ids = [];

    /**
     * The Single Source of Truth for fetching and rendering a feed.
     * Used by Shortcodes, REST API, and potentially Gutenberg blocks.
     */
    public function get_feed( array $args ) {
        $a = wp_parse_args( $args, [
            'categories'         => '',
            'posts_per_cat'      => 3,
            'template'           => 'bento-grid',
            'exclude_duplicates' => true,
            'debug'              => false,
        ]);

        // 1. Cache Check (logic remains the same as before)
        // ... (omitted for brevity)

        // 2. Query Orchestration
        $exclude_list = ( $a['exclude_duplicates'] ) ? self::$rendered_ids : [];
        $cat_slugs = array_filter( explode( ',', $a['categories'] ) );
        
        ob_start();
        foreach ( $cat_slugs as $slug ) {
            $query = new WP_Query([
                'category_name'  => trim( $slug ),
                'posts_per_page' => (int) $a['posts_per_cat'],
                'post__not_in'   => $exclude_list,
                'no_found_rows'  => true,
            ]);

            $this->render_view( $a['template'], $query, $a );

            // Track IDs for the next call on this page load
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    self::$rendered_ids[] = get_the_ID();
                    $exclude_list[] = get_the_ID();
                }
            }
            wp_reset_postdata();
        }

        return ob_get_clean();
    }

    private function render_view( $template, $query, $args ) {
        $path = Manifest::path() . "templates/{$template}.php";
        if ( file_exists( $path ) ) {
            include $path;
        }
    }

    public function run_adapter_test_logic() {

        $dummy_shopify_json = [
            'id'      => 'shop_999',
            'title'   => 'Limited Edition Bento Box',
            'handle'  => 'limited-edition-bento',
            'images'  => [
                ['src' => 'https://cdn.shopify.com/test-image.jpg']
            ],
            'variants' => [
                ['price' => '45.00']
            ]
        ];

        $mapped_item = \Qck\FeedEngine\Engine\Adapters\ShopifyAdapter::create_item_from_shopify_data($dummy_shopify_json);

        // Now you have a clean FeedItem object to inspect
        // \Qck\FeedEngine\Core\Debug::logDump( $mapped_item, __METHOD__);
        // error_log(print_r($mapped_item, true));
        return $mapped_item;

    }
}