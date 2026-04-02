<?php 

namespace Qck\FeedEngine\CPT;

use Qck\FeedEngine\Core\CPT\BasePostType;

class Feed extends BasePostType {

    public function get_slug(): string {
        return 'qckfe-feed';
    }

    public function public(): bool {
        return true;
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
        return ['title']; 
    }

    /**
     * Let's use that custom Capability logic we talked about!
     * This makes it so 'Feeds' are handled separately from 'Posts'.
     */
    // public function capability_type(): string|array {
    //     return 'qckfe_feed';
    // }

    public function get_metaboxes() : array {
        return [
            new \Qck\FeedEngine\CPT\MetaBoxes\FeedSettings(),
        ];
    }
}