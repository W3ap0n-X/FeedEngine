<?php
/**
 * Bento Grid Template
 * Variables available: $query (WP_Query), $a (Shortcode Attributes)
 */

if ( ! $query->have_posts() ) {
    echo '<p class="rhha-no-posts">No reviews found in this section.</p>';
    return;
}

$count = 0;
?>

<div class="rhha-bento-grid">
    <?php while ( $query->have_posts() ) : $query->the_post(); 
        // Logic for the 'Featured' double-size card
        $is_featured = ( $count === 0 ); 
        $thumb_size  = $is_featured ? 'large' : 'medium_large';
        $category    = get_the_category()[0]->name ?? 'Review';
        $rating      = get_post_meta( get_the_ID(), '_star_rating', true ); // Adjust key as needed
    ?>
        
        <article <?php post_class( 'rhha-card ' . ( $is_featured ? 'is-featured' : '' ) ); ?>>
            <a href="<?php the_permalink(); ?>" class="rhha-card-link">
                
                <div class="rhha-card-image" style="background-image: url('<?php echo get_the_post_thumbnail_url( get_the_ID(), $thumb_size ); ?>');">
                    <span class="rhha-badge"><?php echo esc_html( $category ); ?></span>
                </div>

                <div class="rhha-card-content">
                    <div class="rhha-meta">
                        <?php if ( $rating ) : ?>
                            <span class="rhha-rating">★ <?php echo esc_html( $rating ); ?></span>
                        <?php endif; ?>
                        <span class="rhha-date"><?php echo get_the_date('M Y'); ?></span>
                    </div>
                    
                    <h3 class="rhha-title"><?php the_title(); ?></h3>
                    
                    <?php if ( $is_featured ) : ?>
                        <p class="rhha-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 25 ); ?></p>
                    <?php endif; ?>

                    <span class="rhha-cta">Read Review →</span>
                </div>
            </a>
        </article>

    <?php $count++; endwhile; ?>
</div>

<style>
/* The Bento Magic */
.rhha-bento-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(3, 1fr); /* 3 columns for desktop */
    grid-auto-rows: minmax(200px, auto);
}

.rhha-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #eee;
    transition: transform 0.2s ease;
    display: flex;
    flex-direction: column;
}

/* Make the first item big (Bento Style) */
.rhha-card.is-featured {
    grid-column: span 2;
    grid-row: span 2;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .rhha-bento-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
    .rhha-bento-grid { grid-template-columns: 1fr; }
    .rhha-card.is-featured { grid-column: span 1; grid-row: span 1; }
}

/* Internal Card Styling */
.rhha-card-image { height: 200px; background-size: cover; background-position: center; position: relative; }
.rhha-card.is-featured .rhha-card-image { height: 400px; }
.rhha-card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
.rhha-title { font-size: 1.2rem; margin: 10px 0; color: #333; }
.rhha-card.is-featured .rhha-title { font-size: 1.8rem; }
.rhha-badge { position: absolute; top: 10px; left: 10px; background: #ff5722; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; }
</style>