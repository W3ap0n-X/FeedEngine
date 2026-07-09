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
        $feed['card'] = $args['card_settings']['card-type'];
        $feed['grid'] = $args['card_settings']['grid-type'];
        $feed['title'] = $args['feedSettings']['title'];
        // \Qck\FeedEngine\Core\Debug::logDump( $feed, __METHOD__ . ' $feed');
        // \Qck\FeedEngine\Core\Debug::logDump( $args['card_settings'], __METHOD__ . ' $feed');
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
        // \Qck\FeedEngine\Core\Debug::logDump( $features, __METHOD__ . ' $features');
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

        $styles .= '--' . Manifest::PREFIX . '-color-card-border: ' .( $card_settings['colors']['card']['border'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-card-bg: ' . ($card_settings['colors']['card']['background'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-card-shadow: ' . ($card_settings['colors']['card']['background'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-card-overlay: ' . ($card_settings['colors']['card']['overlay'] ?? 'transparent') . ';';

        $styles .= '--' . Manifest::PREFIX . '-color-heading: ' . ($card_settings['colors']['text']['heading'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-text: ' . ($card_settings['colors']['text']['default'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-muted: ' . ($card_settings['colors']['text']['muted'] ?? 'inherit') . ';';

        $styles .= '--' . Manifest::PREFIX . '-color-link: ' . ($card_settings['colors']['link']['text'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-link-bg: ' . ($card_settings['colors']['link']['background'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-link-border: ' . ($card_settings['colors']['link']['border'] ?? 'transparent') . ';';

        $styles .= '--' . Manifest::PREFIX . '-color-grid-bg: ' . ($card_settings['colors']['grid']['background'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-grid-border: ' . ($card_settings['colors']['grid']['border'] ?? 'transparent') . ';';

         $styles .= '--' . Manifest::PREFIX . '-color-container-bg: ' . ($card_settings['colors']['container']['background'] ?? 'transparent') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-container-border: ' . ($card_settings['colors']['container']['border'] ?? 'transparent') . ';';
        

        $styles .= '--' . Manifest::PREFIX . '-color-badge-source: ' . ($card_settings['colors']['badges']['source'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-group: ' . ($card_settings['colors']['badges']['group'] ?? 'var(--qckfe-color-feature)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-type: ' . ($card_settings['colors']['badges']['post-type'] ?? 'var(--qckfe-color-accent)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-category: ' . ($card_settings['colors']['badges']['category'] ?? 'var(--qckfe-color-feature)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-tag: ' . ($card_settings['colors']['badges']['tag'] ?? 'var(--qckfe-color-feature)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-source-text: ' . ($card_settings['colors']['badges']['source-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-group-text: ' . ($card_settings['colors']['badges']['group-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-type-text: ' . ($card_settings['colors']['badges']['source-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-category-text: ' . ($card_settings['colors']['badges']['category-text'] ?? 'var(--qckfe-color-muted)') . ';';
        $styles .= '--' . Manifest::PREFIX . '-color-badge-tag-text: ' . ($card_settings['colors']['badges']['tag-text'] ?? 'var(--qckfe-color-muted)') . ';';


        $styles .= '--' . Manifest::PREFIX . '-grid-max-width: ' . ($card_settings['sizing']['grid']['max-width'] ?? '100%') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-padding-x: ' . ($card_settings['sizing']['grid']['padding-x'] ?? '1rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-padding-y: ' . ($card_settings['sizing']['grid']['padding-y'] ?? '1.5rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-margin-x: ' . ($card_settings['sizing']['grid']['margin-x'] ?? '0') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-margin-y: ' . ($card_settings['sizing']['grid']['margin-y'] ?? '0') . ';';

        $styles .= '--' . Manifest::PREFIX . '-grid-gap: ' . ($card_settings['sizing']['grid']['gap'] ?? '1rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-border-width: ' . ($card_settings['sizing']['grid']['border-width'] ?? '0') . ';';
        $styles .= '--' . Manifest::PREFIX . '-grid-border-radius: ' . ($card_settings['sizing']['grid']['border-radius'] ?? '1rem') . ';';

        $styles .= '--' . Manifest::PREFIX . '-container-max-width: ' . ($card_settings['sizing']['container']['max-width'] ?? '100%') . ';';
        $styles .= '--' . Manifest::PREFIX . '-container-padding-x: ' . ($card_settings['sizing']['container']['padding-x'] ?? '1rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-container-padding-y: ' . ($card_settings['sizing']['container']['padding-y'] ?? '1.5rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-container-margin-x: ' . ($card_settings['sizing']['container']['margin-x'] ?? '0') . ';';
        $styles .= '--' . Manifest::PREFIX . '-container-margin-y: ' . ($card_settings['sizing']['container']['margin-y'] ?? '0') . ';';

        $styles .= '--' . Manifest::PREFIX . '-container-border-width: ' . ($card_settings['sizing']['container']['border-width'] ?? '0') . ';';
        $styles .= '--' . Manifest::PREFIX . '-container-border-radius: ' . ($card_settings['sizing']['container']['border-radius'] ?? '1rem') . ';';
        

        $styles .= '--' . Manifest::PREFIX . '-card-max-width: ' . ($card_settings['sizing']['card']['max-width'] ?? '1fr') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-min-width: ' . ($card_settings['sizing']['card']['min-width'] ?? '240px') . ';';
        
        $styles .= '--' . Manifest::PREFIX . '-card-padding-x: ' . ($card_settings['sizing']['card']['padding-x'] ?? '1rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-padding-y: ' . ($card_settings['sizing']['card']['padding-y'] ?? '1.5rem') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-border-width: ' . ($card_settings['sizing']['card']['border-width' ] ?? '1px') . ';';
        $styles .= '--' . Manifest::PREFIX . '-card-radius: ' . ($card_settings['sizing']['card']['border-radius' ] ?? '12px') . ';';

        
        if($card_settings['features']['overlay']){ $styles .= '--' . Manifest::PREFIX . '-card-overlay-color: ' . ($card_settings['colors']['card']['overlay' ] ?? 'rgba(0,0,0,0.8)') . ';'; }
        else {  $styles .= '--' . Manifest::PREFIX . '-card-overlay-color: ' . 'transparent;' ; }

        $styles .= '--' . Manifest::PREFIX . '-card-img-height: ' . ($card_settings['sizing']['card']['img-height'] ?? '200px') . ';';
        if($card_settings['features']['overlay']){ $styles .= '--' . Manifest::PREFIX . '-card-overlay: ' . ($card_settings['sizing']['card']['overlay' ] ?? '20%') . ';'; }
        else {  $styles .= '--' . Manifest::PREFIX . '-card-overlay: ' . 'transparent;' ; }

        if($card_settings['features']['box-shadow']){ 
            $styles .= '--' . Manifest::PREFIX . '-card-shadow: ' . ($card_settings['colors']['card']['box-shadow' ] ?? 'rgba(0,0,0,0.12)') . ';';
            $styles .= '--' . Manifest::PREFIX . '-card-shadow-hover: ' . ($card_settings['colors']['card']['hover-shadow' ] ?? 'rgba(0,0,0,0.2)') . ';';
            $styles .= '--' . Manifest::PREFIX . '-card-shadow-size: ' . ($card_settings['sizing']['card']['box-shadow' ] ?? '10px 8px 24px') . ';';
            $styles .= '--' . Manifest::PREFIX . '-card-shadow-hover-size: ' . ($card_settings['sizing']['card']['hover-shadow' ] ?? '0 8px 24px') . ';'; 
        }
        else {  
            $styles .= '--' . Manifest::PREFIX . '-card-shadow: ' . 'transparent;' ; 
            $styles .= '--' . Manifest::PREFIX . '-card-shadow-hover: ' . 'transparent;' ; 
        }



        $styles .= '--' . Manifest::PREFIX . '-badge-padding: ' . ($card_settings['sizing']['card']['padding-badge'] ?? '0.4em 0.8em') . ';';
        $styles .= '--' . Manifest::PREFIX . '-badge-radius: ' . ($card_settings['sizing']['card']['badge-radius' ] ?? '4px') . ';';
        
        
        $styles .= '--' . Manifest::PREFIX . '-link-padding: ' . ($card_settings['sizing']['card']['padding-link'] ?? '0.4em 0.8em') . ';';
        $styles .= '--' . Manifest::PREFIX . '-link-radius: ' . ($card_settings['sizing']['card']['link-radius' ] ?? '12px') . ';';
        
        $styles .= '--' . Manifest::PREFIX . '-link-border-width: ' . ($card_settings['sizing']['card']['link-border-width' ] ?? 'var(--qckfe-card-border-width)') . ';';


        $styles .= '--' . Manifest::PREFIX . '-font-title: ' . ($card_settings['sizing']['text']['title' ]['font'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-heading: ' . ($card_settings['sizing']['text']['heading' ]['font'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-link: ' . ($card_settings['sizing']['text']['link']['font']  ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-text: ' . ($card_settings['sizing']['text']['text']['font']  ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-muted: ' . ($card_settings['sizing']['text']['muted' ]['font']  ?? 'inherit') . ';';

        $styles .= '--' . Manifest::PREFIX . '-font-size-title: ' . ($card_settings['sizing']['text']['title' ]['size'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-heading: ' . ($card_settings['sizing']['text']['heading' ]['size'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-link: ' . ($card_settings['sizing']['text']['link']['size'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-text: ' . ($card_settings['sizing']['text']['text']['size'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-size-muted: ' . ($card_settings['sizing']['text']['muted']['size'] ?? 'inherit') . ';';

        $styles .= '--' . Manifest::PREFIX . '-font-weight-title: ' . ($card_settings['sizing']['text']['title']['weight' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-heading: ' . ($card_settings['sizing']['text']['heading']['weight' ] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-link: ' . ($card_settings['sizing']['text']['link']['weight'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-text: ' . ($card_settings['sizing']['text']['text']['weight'] ?? 'inherit') . ';';
        $styles .= '--' . Manifest::PREFIX . '-font-weight-muted: ' . ($card_settings['sizing']['text']['muted']['weight' ] ?? 'inherit') . ';';

        $styles .= '--' . Manifest::PREFIX . '-font-alignment-title: ' . ($card_settings['sizing']['text']['title']['alignment' ] ?? 'inherit') . ';';
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