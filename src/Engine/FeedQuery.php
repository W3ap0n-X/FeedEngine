<?php 
namespace Qck\FeedEngine\Engine;

class FeedQuery {
/**
     * The Master Fetcher
     * Wraps get_posts with our plugin's defaults.
     */
    public static function fetch(array $custom_args = []): array {
        $defaults = [
            'post_type'      => 'post',
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'suppress_filters' => false, // Allow WPML/Polylang to hook in
        ];

        $args = wp_parse_args($custom_args, $defaults);
        
        return get_posts($args);
    }

    public static function get_by_category(string $slug, int $count = 10): array {
        return self::fetch([
            'category_name'  => $slug,
            'posts_per_page' => $count
        ]);
    }

    public static function get_by_type(string $type, int $count = 10): array {
        return self::fetch([
            'post_type'      => $type,
            'posts_per_page' => $count
        ]);
    }
}