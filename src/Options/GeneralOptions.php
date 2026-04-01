<?php

namespace Qck\FeedEngine\Options;

class GeneralOptions extends \Qck\FeedEngine\Core\Options\OptionSection {
    
    public function get_name(): string {
        return 'general_options'; // Will become qckfe_general_options
    }

    public function get_title(): string {
        return 'General Settings';
    }

    public function get_description(): string {
        return 'Section Description';
    }

    public function define_fields(): array {
        return [
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'debug',
                label: 'Debug Mode',
                type: 'checkbox',
                default: false
            ),
            // Nested Example: qckfe_general_options[api][key]
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: 'key',
                label: 'API Key',
                type: 'text',
                path: ['api'] 
            ),
            new \Qck\FeedEngine\Core\Options\OptionEntry(
                key: '213',
                label: 'New Setting',
                type: 'text',
                path: ['example'] 
            ),
        ];
    }
}