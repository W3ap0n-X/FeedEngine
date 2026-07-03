<?php

namespace Qck\FeedEngine\Core\Options;

class DebugOptions extends \Qck\FeedEngine\Core\Options\OptionSection {
    
    public function get_name(): string {
        return 'debug'; 
    }

    public function get_title(): string {
        return 'Debug Settings';
    }

    public function get_description(): string {
        return '';
    }

    public function get_schema(): array {
        return [
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'enable',
                label: 'Debug Mode',
                type: 'checkbox',
                default: false
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'log_level',
                label: 'Log Level',
                type: 'number',
                placeholder: 'placeholder',
                description: 'Higher Numbers mean more verbose logging. (Not Yet Implemented)',
                default: 700,
            ),

        ];
    }
}