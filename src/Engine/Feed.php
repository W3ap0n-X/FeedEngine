<?php 
namespace Qck\FeedEngine\Engine;

class Feed {
    /**
     * The main entry point for the grid logic.
     */
    public function get_checkerboard_feed(array $categories, int $per_source = 6) {
        $streams = [];

        foreach ($categories as $cat_slug) {
            $streams[] = FeedQuery::get_by_category($cat_slug, $per_source);
        }

        // Weave them: 1st from Cat A, 1st from Cat B, 1st from Cat C...
        return CollectionWeaver::weave($streams);
    }
}