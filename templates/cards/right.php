<?php
/** @var \Qck\FeedEngine\Engine\Data\FeedItem $item */


    



$source = !empty($features['badges']) && in_array('source', $features['badges'] ) ? '<span class="qckfe-badge source">' .  esc_html( $item->source ) . '</span>' : '';
    $type = !empty($features['badges']) && in_array('post-type', $features['badges'] ) ? '<span class="qckfe-badge type">' .  esc_html( $item->type ) . '</span>' : '';
    $group = !empty($features['badges']) && in_array('group', $features['badges'] ) ? '<span class="qckfe-badge group">' .  esc_html( $group ) . '</span>' : '';
    $tag = !empty($features['badges']) && in_array('tag', $features['badges'] ) ? '<span class="qckfe-badge tag">' .  esc_html( 'tag' ) . '</span>' : '';
    $category = !empty($features['badges']) && in_array('category', $features['badges'] ) ? '<span class="qckfe-badge category">' .  esc_html( 'category' ) . '</span>' : '';

    $url = esc_url( $item->url );

$heading = in_array('heading', $features) ? '<span class="qckfe-card-title">' . esc_html( $item->title ) . '</span>'  : '';
$excerpt = in_array('excerpt', $features) ? '<span class="qckfe-card-excerpt">' . wp_trim_excerpt('' , $item->id ) . '</span>'  : '<a href="' . $url . '" class="qckfe-card-link">Read More</a>';
$image = in_array('image', $features) ? '<div class="qckfe-card-media"><img src="' . esc_url( $item->image_url ) . '" alt=""></div>'  : '';
$overlay = in_array('overlay', $features) ? '<div class="qckfe-card-overlay"></div>'  : '';


$output = <<<HTML
<div class="qckfe-card qckfe-card-right">
    {$image}
    
    <div class="qckfe-card-content">
        <div class="qckfe-card-badges">
            {$source}
            {$group}
            {$type}
            {$category}
            {$tag}
        </div>
        {$heading}
        
        {$excerpt}
    </div>
    {$overlay}
</div>
HTML;

// $output = \Qck\FeedEngine\Core\Debug::easyDump( $item, ' $item') . $output;
// $output = \Qck\FeedEngine\Core\Debug::easyDump( $features, ' $features') . $output;

return $output;