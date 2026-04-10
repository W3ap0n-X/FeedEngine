<?php 

namespace Qck\FeedEngine\CPT;

use Qck\FeedEngine\Core\CPT\BasePostType;

class Feed extends BasePostType {

    public function get_slug(): string {
        return 'qckfe-feed';
    }

    public function public(): bool {
        return false;
    }

    public function show_ui() : bool {
        return true;
    }

    public function get_label(): string {
        return 'Feeds';
    }

    public function description(): string {
        return 'Feeds for days';
    }

    public function menu_icon(): string {
        return 'dashicons-rss'; // A nice RSS icon for the sidebar
    }

    public function supports(): array {
        // We probably don't need the editor for a Feed config, 
        // just a Title and our custom Meta Boxes later.
        return ['title' , 'thumbnail' , 'revisions']; 
    }

    public function get_metaboxes() : array {
        return [
            new \Qck\FeedEngine\CPT\MetaBoxes\FeedSettings(),
            new \Qck\FeedEngine\CPT\MetaBoxes\ShopifySettings(),
            new \Qck\FeedEngine\CPT\MetaBoxes\FeedPostTypes(),
            new \Qck\FeedEngine\CPT\MetaBoxes\FeedCategories(),
            new \Qck\FeedEngine\CPT\MetaBoxes\FeedPostTags(),
        ];
    }
}