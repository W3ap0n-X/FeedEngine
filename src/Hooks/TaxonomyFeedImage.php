<?php
/*
namespace Qck\FeedEngine\Hooks;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\HookInterface;

class TaxonomyFeedImage implements HookInterface {

    public function is_filter(): bool { return true ; }
    public function get_hook(): string { return 'get_term_metadata'; }
    public function get_priority(): int { return 10; }
    public function get_args_count(): int { return 4; }

    public function get_callback(): callable {
        return function($value, $object_id, $meta_key, $single) {
            if ( '_qckfe_feed-attributes' === $meta_key ) {
        // Return your custom value instead of database value
        return 'your_custom_override_value'; 
    }
    
    // Return null to let WordPress pull from database normally
    return $value;
            
        };
    }
}

/*
*/