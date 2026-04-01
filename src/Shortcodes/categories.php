<?php 
add_shortcode('rh_category_grid', function() {
    $categories = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'number'     => 8, 
    ]);

    if (empty($categories) || is_wp_error($categories)) return '';

    $output = '<div class="category-grid">';

    foreach ($categories as $cat) {
        $link  = get_term_link($cat);
        $name  = esc_html($cat->name);
        $count = $cat->count;
        $icon_color = '#F0F4FF'; 

        $output .= "
        <a href='{$link}' class='cat-card'>
            <div class='cat-icon' style='background: {$icon_color};'>📦</div>
            <div class='cat-info'>
                <span class='cat-name'>{$name}</span>
                <span class='cat-count'>" . number_format($count) . " products</span>
            </div>
            <div class='cat-arrow'>→</div>
        </a>";
    }

    $output .= '</div>';
    return $output;
});