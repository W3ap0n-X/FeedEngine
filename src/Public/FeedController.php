<?php


namespace Qck\FeedEngine\Public;

class FeedController {

    private static $rendered_ids = [];

    public function render_feed( $atts ) {
        $a = shortcode_atts([
            'categories'         => 'brands,blog',
            'posts_per_cat'      => 3,
            'exclude_duplicates' => 'yes',
            'manual_exclude'     => '',
            'debug'              => 'no', // The new toggle
            'template' => 'bento-grid', // Default template
        ], $atts);

        $exclude_list = [];
        if ( !empty($a['manual_exclude']) ) {
            $exclude_list = array_map('intval', explode(',', $a['manual_exclude']));
        }

        if ( $a['exclude_duplicates'] === 'yes' ) {
            $exclude_list = array_merge($exclude_list, self::$rendered_ids);
        }

        // Dynamic pathing: Check if file exists in plugin or theme override
    $template_file = RH_FEED_PATH . "templates/{$a['template']}.php";
    
    if ( ! file_exists( $template_file ) ) {
        $template_file = RH_FEED_PATH . "templates/bento-grid.php"; // Fallback
    }

        // --- Start Output Buffering ---
        ob_start();
        
        $cat_slugs = explode(',', $a['categories']);
        echo '<div class="master-hub-wrapper">';

        foreach ($cat_slugs as $slug) {
            $query = new WP_Query([
                'category_name'  => trim($slug),
                'posts_per_page' => (int)$a['posts_per_cat'],
                'post__not_in'   => $exclude_list,
                'no_found_rows'  => true,
            ]);

            include RH_FEED_PATH . 'templates/category-section.php';

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $id = get_the_ID();
                    self::$rendered_ids[] = $id;
                    $exclude_list[] = $id;
                }
            }
            wp_reset_postdata();
        }

        echo '</div>';

        // --- Handle Debug Output ---
        if ( $a['debug'] === 'yes' && current_user_can('manage_options') ) {
            echo "\n\n";
        }

        return ob_get_clean();
    }



    public function cacheThing(){
        // Inside render_feed()
        $cache_ver = get_option( 'rh_feed_cache_version', '1' );
        $cache_key = 'rh_hub_v' . $cache_ver . '_' . md5( serialize( $a ) );

        $output = get_transient( $cache_key );
        if ( false !== $output ) return $output;
    }

}