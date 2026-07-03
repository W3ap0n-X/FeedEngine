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
        // \Qck\FeedEngine\Core\Debug::logDump( $raw_types, __METHOD__ . ' $raw_types');
        foreach (get_post_types($args) as $type) {
            // \Qck\FeedEngine\Core\Debug::logDump( $type, __METHOD__ . ' $type');

            if(  $raw_types[ $type ] ?? false) {
                $types[] =  $type ;
            }
            // $categories[] = ['id' => $category->term_id,'include' => $raw_cats[$category->slug]];
        }
        if(empty($types)) { $types = 'any'; }
        return $types;

    }

    public function run_adapter_test_logic($args) {
        $count = $args['feedSettings']['items_per_page'];
        // \Qck\FeedEngine\Core\Debug::logDump( $count, __METHOD__ . ' $count');
        $types = $this->get_query_types_List($args['post_types']);
        $categories = $this->get_query_category_List($args['categories']);
        $tags = $this->get_query_tags_List($args['tags']);
        $feed = [];
        
        $features = $this->get_card_features($args['card_settings']);
        $styles = $this->compile_card_styles($args['card_settings']);
        // \Qck\FeedEngine\Core\Debug::logDump( $styles, __METHOD__ . ' $styles');

        $manual_ids = !empty($args['feedSettings']['manual_ids']) ? $args['feedSettings']['manual_ids'] : [];
        
        $count -= count($manual_ids);
        // \Qck\FeedEngine\Core\Debug::logDump( $count, __METHOD__ . ' $count');

        $feed_query = FeedQuery::from($types)
            ->only( $args['feedSettings']['manual_ids'] ?? [])
            ->where_taxonomy('category', $categories)
            ->where_taxonomy('post_tag', $tags)
            ->order_by( $args['feedSettings']['orderby'] ?? '' )
            ->limit($count)
            ->execute();

            // \Qck\FeedEngine\Core\Debug::logDump( $feed_query, __METHOD__ . ' $feed_query');
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
        $feed['style'] = $styles;
        $feed['features'] = $features;
        // \Qck\FeedEngine\Core\Debug::logDump( $feed, __METHOD__ . ' $feed');
        $options = get_option('qckfe_general_options');
        set_transient( "qckfe_cache_" . $args['feed_info']['id'], $feed , 1 * $options['cache-interval'] ?? HOUR_IN_SECONDS );
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
            'card_settings' => 'card_settings'
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

    public static function _get_card_features($card_settings) {
        $features = [];
        foreach ($card_settings['features'] as $feature => $enable) {
            if(is_array($enable)){
                foreach ($enable as $_feature => $_enable) {
                    if($_enable){
                        $features[$feature][] = $_feature;
                    }
                }
            }
            else if($enable){
                $features[] = $feature;
            }
        }
        \Qck\FeedEngine\Core\Debug::logDump( $features, __METHOD__ . ' $features');
        return $features;
    }

    public function get_card_features($card_settings) {
        
        if($card_settings['use-global']){
            return [];
        }
        
        return self::_get_card_features($card_settings);
    }

    public static  function _compile_card_styles($card_settings) {

        $styles = "";
        $styles .= '--' . Manifest::PREFIX . '-color-accent: ' . ( $card_settings['colors']['accent'] ?? 'inherit' ). ';';
        $styles .= '--' . Manifest::PREFIX . '-color-feature: ' .( $card_settings['colors']['feature'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-border: ' .( $card_settings['colors']['border'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-heading: ' . ($card_settings['colors']['heading'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-text: ' . ($card_settings['colors']['text'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-link: ' . ($card_settings['colors']['link'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-link-bg: ' . ($card_settings['colors']['link-bg'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-link-border: ' . ($card_settings['colors']['link-border'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-muted: ' . ($card_settings['colors']['muted'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-bg: ' . ($card_settings['colors']['background'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-source: ' . ($card_settings['colors']['badges']['source'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-group: ' . ($card_settings['colors']['badges']['group'] ?? 'var(--qckfe-color-feature)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-type: ' . ($card_settings['colors']['badges']['source'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-category: ' . ($card_settings['colors']['badges']['category'] ?? 'var(--qckfe-color-feature)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-tag: ' . ($card_settings['colors']['badges']['tag'] ?? 'var(--qckfe-color-feature)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-source-text: ' . ($card_settings['colors']['badges']['source-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-group-text: ' . ($card_settings['colors']['badges']['group-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-type-text: ' . ($card_settings['colors']['badges']['source-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-category-text: ' . ($card_settings['colors']['badges']['category-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-tag-text: ' . ($card_settings['colors']['badges']['tag-text'] ?? 'var(--qckfe-color-muted)') . ';';

        $styles .= '--' . Manifest::PREFIX . '-grid-padding-x: ' . ($card_settings['sizing']['grid']['padding-x'] ?? '1rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-padding-y: ' . ($card_settings['sizing']['grid']['padding-y'] ?? '1.5rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-gap: ' . ($card_settings['sizing']['container']['gap'] ?? '1rem') . ';';
        
        $styles .= '--' . Manifest::PREFIX . '-card-max-width: ' . ($card_settings['sizing']['card']['max-width'] ?? '1fr') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-min-width: ' . ($card_settings['sizing']['card']['min-width'] ?? '240px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-img-height: ' . ($card_settings['sizing']['card']['img-height'] ?? '200px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-padding-x: ' . ($card_settings['sizing']['card']['padding-x'] ?? '1rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-padding-y: ' . ($card_settings['sizing']['card']['padding-y'] ?? '1.5rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-badge-padding: ' . ($card_settings['sizing']['card']['padding-badge'] ?? '0.4em 0.8em') . ';';
        $styles .= '--' . Manifest::PREFIX . '-link-padding: ' . ($card_settings['sizing']['card']['padding-link'] ?? '0.4em 0.8em') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-radius: ' . ($card_settings['sizing']['card']['border-radius' ] ?? '12px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-badge-radius: ' . ($card_settings['sizing']['card']['badge-radius' ] ?? '4px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-link-radius: ' . ($card_settings['sizing']['card']['link-radius' ] ?? '12px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-border-width: ' . ($card_settings['sizing']['card']['border-width' ] ?? '1px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-link-border-width: ' . ($card_settings['sizing']['card']['link-border-width' ] ?? 'var(--qckfe-card-border-width)') . ';';


        $styles .= '--' . Manifest::PREFIX . '-font-size-heading: ' . ($card_settings['sizing']['card']['heading' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-link: ' . ($card_settings['sizing']['card']['link' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-text: ' . ($card_settings['sizing']['card']['text' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-muted: ' . ($card_settings['sizing']['card']['muted' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-heading: ' . ($card_settings['sizing']['card']['heading-weight' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-link: ' . ($card_settings['sizing']['card']['link-weight' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-text: ' . ($card_settings['sizing']['card']['text-weight' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-muted: ' . ($card_settings['sizing']['card']['muted-weight' ] ?? 'inherit') . ';';
        return $styles;
    }

    public function compile_card_styles($card_settings) {
        $styles = "";
        if($card_settings['use-global']){
            return $styles;
        }



        $styles = self::_compile_card_styles($card_settings);



        return $styles;
    }
}