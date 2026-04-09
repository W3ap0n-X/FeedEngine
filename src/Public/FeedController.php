<?php
namespace Qck\FeedEngine\Public;

use WP_Query;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Engine\Adapters\ShopifyAdapter;
use Qck\FeedEngine\Engine\Adapters\PostAdapter;


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
                'cache_results' => false
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

    private function get_query_category_defaults($_args = []){
        
    


        // $categories = ['uncategorized' => ['id' => 1,'include' => $a['uncategorized']]];
        $args = array(
            'taxonomy' => 'category',
            'hide_empty' => false,
    
        );
        $all_categories = [];
        foreach (get_terms($args) as $category) {
            $all_categories[$category->slug] = false;
        }
        $a = wp_parse_args( $_args, $all_categories);
        return $a;

    }

    private function get_query_category_List($raw_cats = []){


        
        $args = array(
            'taxonomy' => 'category',
            'hide_empty' => false,
    
        );

        foreach (get_terms($args) as $category) {
            $categories[$category->slug] = ['id' => $category->term_id,'include' => $raw_cats[$category->slug]];
        }
        return $categories;

    }

    private function get_query_post_type_defaults(){
        $post_types = [
            'post' => true,
            'page'=> true,
            'attachment'=> false,
        ];
        $args = array(
            'public'   => true,
            '_builtin' => false,
    
        );
        foreach (get_post_types($args) as $post_type) {
            $post_types[$post_type] = false;
        }
        return $post_types;

    }

    private function get_query_post_types($post_types){
        $types = [] ;
         foreach ($post_types as $post_type => $include) {
            if($include == true) {
                $types[] = $post_type;
            }
        }
        return $types;

    }

    private function get_query_categories($categories){
        $cats = [] ;
         foreach ($categories as $category => $details) {
            // \Qck\FeedEngine\Core\Debug::logDump( $details, __METHOD__ . ' $details');
            if($details['include'] == true) {
                $cats[] = $details['id'];
            }
        }
        \Qck\FeedEngine\Core\Debug::logDump( $cats, __METHOD__ . ' $cats');
        return $cats;

    }

    public function run_adapter_test_logic($args) {
        // ob_start();
        $a = wp_parse_args( $args, [
            'feedSettings' => [
                'categories'         => '',
                'exclude_duplicates' => true,
                'debug'              => false,
                'api' => [
                    'items_per_page'=> 1,
                ]
            ],
            'post_types' => $this->get_query_post_type_defaults(),
            'categories' => $this->get_query_category_defaults($args['categories']),

        ]);

        $a['categories'] = $this->get_query_category_defaults($a['categories']);

        \Qck\FeedEngine\Core\Debug::logDump( $a, __METHOD__ . ' $a');

        \Qck\FeedEngine\Core\Debug::logDump( $this->get_query_category_List($a['categories']), __METHOD__ . ' $this->get_query_categories($a[\'categories\'])');

        // $categories = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
        // \Qck\FeedEngine\Core\Debug::logDump( $categories, __METHOD__ . ' $categories');


        $exclude_list = ( $a['exclude_duplicates'] ) ? self::$rendered_ids : [];
        $query = new WP_Query([
            'posts_per_page' => (int) $a['feedSettings']['api']['items_per_page'],
            'post__not_in'   => $exclude_list,
            'no_found_rows'  => true,
            'post_type'  => $this->get_query_post_types($a['post_types']),
            'category__in' => $this->get_query_categories($this->get_query_category_List($a['categories'])),
        ]);
        $feed = [];
        foreach ( $query->get_posts() as $post ) {
            $feed[] = PostAdapter::map($post); 
        }
        \Qck\FeedEngine\Core\Debug::logDump( $feed, __METHOD__ . ' $feed');
        return $feed;

        // return ob_get_clean();

        // $dummy_shopify_json = [
        //     'id'      => 'shop_999',
        //     'title'   => 'Limited Edition Bento Box',
        //     'handle'  => 'limited-edition-bento',
        //     'images'  => [
        //         ['src' => 'https://cdn.shopify.com/test-image.jpg']
        //     ],
        //     'variants' => [
        //         ['price' => '45.00']
        //     ]
        // ];

        // $mapped_item = \Qck\FeedEngine\Engine\Adapters\ShopifyAdapter::create_item_from_shopify_data($dummy_shopify_json);

        // // Now you have a clean FeedItem object to inspect
        // \Qck\FeedEngine\Core\Debug::logDump( $mapped_item, __METHOD__);
        // error_log(print_r($mapped_item, true));
        // return $mapped_item;

    }
}