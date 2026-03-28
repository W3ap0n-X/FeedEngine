<?php

if ( ! defined( 'WPINC' ) ) { die; }

namespace Qck\FeedEngine\Core\Shortcodes;
use Qck\FeedEngine\Manifest;

interface Shortcode {
    public function get_tag(): string;
    public function render( array $atts, ?string $content = null ): string;
}