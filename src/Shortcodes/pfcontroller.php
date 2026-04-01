<?php

add_shortcode('retail_hub_master_feed', function($atts) {
    // 1. Setup Attributes
    $a = shortcode_atts([
        'categories' => 'brands,blogs', // Comma-separated slugs
        'posts_per_cat' => 3,
        'columns' => 3
    ], $atts);

    $cat_slugs = explode(',', $a['categories']);
    $output = '<div class="master-hub-wrapper">';

    foreach ($cat_slugs as $slug) {
        $term = get_term_by('slug', trim($slug), 'category');
        if (!$term) continue;

        // Header for each feed section
        $output .= '<section class="feed-section">';
        $output .= '<div class="feed-header"><h2>Top in ' . esc_html($term->name) . '</h2><a href="'.get_term_link($term).'">View All →</a></div>';
        $output .= '<div class="category-grid" style="grid-template-columns: repeat('.esc_attr($a['columns']).', 1fr);">';

        // 2. The Query
        $query = new WP_Query([
            'category_name' => $term->slug,
            'posts_per_page' => (int)$a['posts_per_cat'],
            'no_found_rows' => true, // Performance boost: skip pagination counting
        ]);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                
                // Detection for your specific color-coding
                $is_brand = has_category('brands', get_the_ID());
                $accent_color = $is_brand ? 'brand-orange' : 'blog-blue';
                $label = $is_brand ? 'Brand' : 'Blog';

                $output .= '
                <a href="'.get_permalink().'" class="hub-card '.$accent_color.'">
                    <div class="card-thumb" style="background-image: url('.get_the_post_thumbnail_url(get_the_ID(), 'medium_large').');">
                        <span class="hub-badge">'.$label.'</span>
                    </div>
                    <div class="card-content">
                        <h3>'.get_the_title().'</h3>
                        <span class="card-link">Read More →</span>
                    </div>
                </a>';
            }
        }
        wp_reset_postdata();

        $output .= '</div></section>';
    }

    $output .= '</div>';
    return $output;
});

function retail_hub_master_feed_styles() {
    echo '
<style>
.master-hub-wrapper { display: flex; flex-direction: column; gap: 60px; }
.feed-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
.feed-header h2 { margin: 0; font-size: 24px; color: #1F2633; }
.feed-header a { font-weight: 600; text-decoration: none; color: #0056D2; font-size: 14px; }

.hub-card { 
    background: #fff; border-radius: 12px; overflow: hidden; text-decoration: none;
    border: 1px solid #eee; transition: all 0.3s ease; display: flex; flex-direction: column;
}
.hub-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }

.card-thumb { height: 200px; background-size: cover; background-position: center; position: relative; }
.hub-badge { position: absolute; top: 15px; left: 15px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #fff; }

/* The Orange vs Blue Logic */
.brand-orange .hub-badge { background: #FF7F50; } /* Your Gold/Orange */
.blog-blue .hub-badge { background: #0056D2; } /* Your Brand Blue */
.brand-orange .card-link { color: #FF7F50; }
.blog-blue .card-link { color: #0056D2; }

.card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
.card-content h3 { font-size: 18px; margin: 0 0 15px 0; color: #1F2633; line-height: 1.4; }
.card-link { margin-top: auto; font-weight: 700; font-size: 13px; }
</style>
    ';
}

add_action( 'wp_head', 'retail_hub_master_feed_styles', 99 );