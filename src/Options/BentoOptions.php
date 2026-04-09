<?php
namespace Qck\FeedEngine\Options;

use Qck\FeedEngine\Core\Options\OptionSection;
use Qck\FeedEngine\Core\Options\OptionEntry;

class BentoOptions extends OptionSection {

    public function get_name(): string {
        return 'bento_settings'; // DB Row: qckfe_bento_settings
    }

    public function get_title(): string {
        return 'Bento Grid Configuration';
    }

    public function get_description(): string {
        return 'Section Description';
    }

    public function get_schema(): array {
        return [
            'enabled' => new OptionEntry(
                key: 'enabled',
                label: 'Enable Bento Layout',
                type: 'checkbox',
                default: true
            ),
            'columns' => new OptionEntry(
                key: 'desktop',
                label: 'Desktop Columns',
                type: 'number',
                default: 3,
                path: ['layout'] // qckfe_bento_settings[layout][desktop]
            ),
            'gap' => new OptionEntry(
                key: 'gap',
                label: 'Grid Gap (px)',
                type: 'number',
                default: 20,
                path: ['layout', 'spacing'] // qckfe_bento_settings[layout][spacing][gap]
            )
            ,
            'img' => new OptionEntry(
                key: 'img',
                label: 'Test Image',
                type: 'image',
                path: ['test', 'image'] // qckfe_bento_settings[layout][spacing][gap]
            ),
            'img2' => new OptionEntry(
                key: 'img2',
                label: 'Test Image 2',
                type: 'image',
                path: ['test', 'image'] // qckfe_bento_settings[layout][spacing][gap]
            )
        ];
    }
}