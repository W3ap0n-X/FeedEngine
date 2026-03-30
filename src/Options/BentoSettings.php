<?php

namespace Qck\FeedEngine\Options;

class BentoSettings {
    public function get_key(): string { return 'bento_config'; }
    
    public function get_defaults(): array {
        return [
            'columns' => 3,
            'gap'     => '20px',
            'featured' => true
        ];
    }

    public function validate( $input ) {
        // Your custom logic here
        return $input;
    }
}