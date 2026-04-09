<?php 
namespace Qck\FeedEngine\Engine;

class FeedQuery {
    /**
     * The Master Fetcher
     * Wraps get_posts with our plugin's defaults.
     */
    private array $params = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 10,
        'tax_query'      => ['relation' => 'AND'],
        'meta_query'     => ['relation' => 'AND'],
    ];

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

    /**
     * Start the chain with the Post Type
     */
    public static function from(string|array $types): self {
        $instance = new self();
        $instance->params['post_type'] = $types;
        return $instance;
    }

    /**
     * Handle Taxonomies with a simple interface
     */
    public function inTax(string $tax, $terms, string $operator = 'IN'): self {
        $this->params['tax_query'][] = [
            'taxonomy' => $tax,
            'field'    => is_numeric(is_array($terms) ? $terms[0] : $terms) ? 'term_id' : 'slug',
            'terms'    => (array) $terms,
            'operator' => $operator
        ];
        return $this;
    }

    /**
     * The "Manual Override" logic
     */
    public function only(array $ids): self {
        $this->params['post__in'] = $ids;
        $this->params['orderby'] = 'post__in'; // Keep the order of IDs provided
        return $this;
    }

    /**
     * Final execution: Get just the IDs for the Catalog
     */
    public function pluckIds(): array {
        $this->params['fields'] = 'ids';
        $query = new \WP_Query($this->params);
        return $query->posts;
    }
}