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
        'tax_query'      => [ 'relation' => 'AND' ],
        'meta_query'     => [ 'relation' => 'AND' ],
    ];

    public static function fetch( array $custom_args = [] ) : array {

        $defaults = [
            'post_type'        => 'post',
            'posts_per_page'   => 10,
            'post_status'      => 'publish',
            'suppress_filters' => false,
        ];

        $args = wp_parse_args( $custom_args , $defaults );
        
        return get_posts( $args );

    }

    /**
     * Start the chain with the Post Type
     */
    public static function from( $types ) : self {

        $instance = new self();

        $instance->params[ 'post_type' ] = $types;

        return $instance;

    }

    /**
     * Handle Taxonomies with a simple interface
     */
    public function where_taxonomy( string $taxonomy_name , $term_values , string $operator = 'IN' ) : self {
        // cast as an array if item is a string
        $term_list = array_filter( (array) $term_values );
        // \Qck\FeedEngine\Core\Debug::logDump( $term_list, __METHOD__ . ' $term_list');
        if ( empty( $term_list ) ) {
            return $this;
        }
        $first_item = reset( $term_list );
        // Check the first item to determine if we use ID or Slug
        $field_type = is_numeric( $first_item ) ? 'term_id' : 'slug';

        $this->params[ 'tax_query' ][] = [
            'taxonomy'         => $taxonomy_name,
            'field'            => $field_type,
            'terms'            => $term_list,
            'operator'         => $operator,
            'include_children' => true,
        ];

        return $this;

    }

    /**
     * The "Manual Override" logic
     */
    public function only( array $ids ) : self {

        $this->params[ 'post__in' ] = $ids;

        // This ensures the feed respects the hand-picked order from the UI
        $this->params[ 'orderby' ]  = 'post__in';

        return $this;

    }

    public function limit( int $count ) : self {

        $this->params[ 'posts_per_page' ] = $count;

        return $this;

    }

    public function order_by( string $orderby = 'date' , string $order = 'DESC' ) : self {

        // If we have manual IDs set via only(), we usually want to respect that order
        if ( isset( $this->params[ 'post__in' ] ) && $orderby === 'manual' ) {
            $this->params[ 'orderby' ] = 'post__in';
            return $this;
        }

        $this->params[ 'orderby' ] = $orderby;
        $this->params[ 'order' ]   = $order;

        // Handle numeric meta values (like price or views)
        if ( str_contains( $orderby , 'meta_value' ) ) {
            // You would pass the meta_key here if needed
            // $this->params['meta_key'] = 'some_key';
        }

        return $this;

    }

    public function apply_order( string $type = 'date' , string $direction = 'DESC' ) : self {

        switch ( $type ) {
            
            case 'manual':
                // Only works if you've provided IDs via post__in
                $this->params[ 'orderby' ] = 'post__in';
                break;

            case 'price':
                // Force numeric sorting and handle the meta key
                $this->params[ 'orderby' ]  = 'meta_value_num';
                $this->params[ 'meta_key' ] = '_qckfe_price'; 
                break;

            case 'alphabetical':
                $this->params[ 'orderby' ] = 'title';
                $this->params[ 'order' ]   = 'ASC';
                break;

            default:
                // Standard date sort with ID as a tie-breaker
                $this->params[ 'orderby' ] = 'date ID';
                $this->params[ 'order' ]   = $direction;
                break;
        }

        return $this;

    }

    /**
     * Final execution: Get just the IDs for the Catalog
     */
    public function pluck_ids() : array {

        $this->params[ 'fields' ] = 'ids';

        $query = new \WP_Query( $this->params );

        return $query->posts;

    }

    public function execute() : array {

        $manual_items  = [];
        $dynamic_items = [];

        // 1. Trip One: The "Must-Haves" (Manual)
        if ( ! empty( $this->params['post__in'] ) ) {
            $manual_query = new \WP_Query([
                'post_type' => $this->params['post_type'],
                'post__in'  => $this->params['post__in'],
                'orderby'   => 'post__in', // Keep the user's order
            ]);
            $manual_items = $manual_query->posts;
        }

        // 2. Trip Two: The "Fillers" (Automated)
        // We only do this if we haven't hit our limit yet or if auto-fill is on
        $dynamic_args = $this->params;
        
        // CRITICAL: Exclude the manual IDs so we don't have duplicates!
        if ( ! empty( $this->params['post__in'] ) ) {
            unset( $dynamic_args['post__in'] ); // Clear the manual list
            $dynamic_args['post__not_in'] = $this->params['post__in']; 
        }

        $dynamic_query = new \WP_Query( $dynamic_args );
        $dynamic_items = $dynamic_query->posts;

        // 3. Merge and Return
        return ['manual' => $manual_items , 'automatic' => $dynamic_items ];

    }
}