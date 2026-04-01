<?php 
add_shortcode('retail_trending_grid', function($atts) {
    $a = shortcode_atts([
        'featured_id' => '', // Post ID to pin to the big slot
        'cat_slug'    => '', // Optional: Filter the whole grid by a specific category (e.g. 'tech')
    ], $atts);

    $output = '<div class="trending-bento-grid">';

    // 1. Get the Big Slot (Pinned or Latest)
    $args_main = [
        'posts_per_page' => 1,
        'category_name'  => $a['cat_slug']
    ];
    if (!empty($a['featured_id'])) {
        $args_main['p'] = $a['featured_id'];
    }
    
    $featured_query = new WP_Query($args_main);
    $main_post = $featured_query->posts[0] ?? null;

    // 2. Get the 4 side-kicks
    $args_sub = [
        'posts_per_page' => 4,
        'category_name'  => $a['cat_slug'],
        'post__not_in'   => $main_post ? [$main_post->ID] : []
    ];
    $dynamic_query = new WP_Query($args_sub);

    $all_posts = $main_post ? array_merge([$main_post], $dynamic_query->posts) : $dynamic_query->posts;

    foreach ($all_posts as $index => $post) {
        $link  = get_permalink($post->ID);
        $title = get_the_title($post->ID);
        $img   = get_the_post_thumbnail_url($post->ID, 'large');
        
        // Logical "Category" Detection
        // We check if the post has the 'brands' category assigned
        $is_brand = has_category('brands', $post->ID);
        $label    = $is_brand ? 'Brands' : 'Blogs';
        $accent   = $is_brand ? 'accent-orange' : 'accent-blue';
        
        $slot_class = ($index === 0) ? 'main-feature' : 'sub-feature';

        $output .= "
        <a href='{$link}' class='trending-card {$slot_class} {$accent}'>
            <div class='card-img' style='background-image: url({$img});'></div>
            <div class='card-overlay'>
                <span class='badge'>{$label}</span>
                <h3>{$title}</h3>
                <span class='view-link'>View Review →</span>
            </div>
        </a>";
    }

    $output .= '</div>';
    wp_reset_postdata();
    return $output;
});