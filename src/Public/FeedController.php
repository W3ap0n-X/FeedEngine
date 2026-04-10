<?php
namespace Qck\FeedEngine\Public;

use WP_Query;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Engine\Adapters\ShopifyAdapter;
use Qck\FeedEngine\Engine\Adapters\PostAdapter;
use Qck\FeedEngine\Engine\FeedQuery;


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



    private function get_query_category_List($raw_cats){
        $categories = [];

        
        $args = array(
            'taxonomy' => 'category',
            'hide_empty' => false,
    
        );

        foreach (get_terms($args) as $category) {
            if( !empty($raw_cats[ $category->slug ]) ) {
                $categories[] =  $category->slug ;
            }
            // $categories[] = ['id' => $category->term_id,'include' => $raw_cats[$category->slug]];
        }
        return $categories;

    }

    private function get_query_tags_List($raw_tags){
        $tags = [];

        
        $args = array(
            'taxonomy' => 'post_tag',
            'hide_empty' => false,
    
        );

        foreach (get_terms($args) as $tag) {
            if( !empty($raw_tags[ $tag->slug ] )) {
                $tags[] =  $tag->slug ;
            }
            // $categories[] = ['id' => $category->term_id,'include' => $raw_cats[$category->slug]];
        }
        return $tags;

    }

    private function get_query_types_List($raw_types){
        $types = [];

        
        $args = array(
            'public'   => true,
    
        );

        foreach (get_post_types($args) as $type) {
            if( $raw_types[ $type ] ) {
                $types[] =  $type ;
            }
            // $categories[] = ['id' => $category->term_id,'include' => $raw_cats[$category->slug]];
        }
        if(empty($types)) { $types = 'any'; }
        return $types;

    }

    public function run_adapter_test_logic($args) {
        $count = $args['feedSettings']['items_per_page'];
        
        $types = $this->get_query_types_List($args['post_types']);
        $categories = $this->get_query_category_List($args['categories']);
        $tags = $this->get_query_tags_List($args['tags']);
        $feed = [];

        $count -= count($args['feedSettings']['manual_ids']);

        $feed_query = FeedQuery::from($types)
            ->only( $args['feedSettings']['manual_ids'] ?? [])
            ->where_taxonomy('category', $categories)
            ->where_taxonomy('post_tag', $tags)
            ->order_by( $args['feedSettings']['orderby'] ?? '' )
            ->limit($count)
            ->execute();
        foreach ($feed_query as $group => $posts) {
            foreach ( $posts as $post ) {
                $postItem = PostAdapter::map($post); 
                if ( empty( $postItem->image_url ) ) {
                    switch ($args['feedSettings']['image_placeholder_select'] ) {
                        case 'feed_image':
                            if(!empty($args['feed_info']['image'])){
                                $postItem->image_url = wp_get_attachment_image_url($args['feed_info']['image']);
                            }
                            break;

                        case 'custom':
                            if(!empty($args['feedSettings']['image_placeholder'])){
                                $postItem->image_url = wp_get_attachment_image_url($args['feedSettings']['image_placeholder']);
                            }
                            break;
                        
                        default:
                            # code...
                            break;
                    }

                }
                
                $feed[$group][] = $postItem;
            }
        }
        
        // \Qck\FeedEngine\Core\Debug::logDump( $feed, __METHOD__ . ' $feed');

        set_transient( "qckfe_cache_" . $args['feed_info']['id'], $feed , 1 * HOUR_IN_SECONDS );
        return $feed;



        // $mapped_item = \Qck\FeedEngine\Engine\Adapters\ShopifyAdapter::create_item_from_shopify_data($dummy_shopify_json);

        // // Now you have a clean FeedItem object to inspect
        // \Qck\FeedEngine\Core\Debug::logDump( $mapped_item, __METHOD__);
        // error_log(print_r($mapped_item, true));
        // return $mapped_item;

    }

    public function build_front($post_id) {
        $attributes = [
            'feedSettings' => 'settings',
            'post_types' => 'post_types',
            'categories' => 'categories',
            'tags' => 'tags',
        ];
        $featured_image = get_post_thumbnail_id( $post_id );
        $feed_meta = ['feed_info' => ['id' => $post_id,'image'=> $featured_image ] ];
        foreach ($attributes as $key => $value) {
            $feed_meta[$key] = get_post_meta( $post_id, '_qckfe_feed_' . $value, true );
        }
        return $this->run_adapter_test_logic($feed_meta);
    }


    public function shopify_test_item() {
        return [
            [
                'id'      => 'shop_999',
                'title'   => 'Limited Edition Bento Box',
                'handle'  => 'limited-edition-bento',
                'images'  => [
                    ['src' => 'https://cdn.shopify.com/test-image.jpg']
                ],
                'variants' => [
                    ['price' => '45.00']
                ]
            ]
            
        ];
    }
}