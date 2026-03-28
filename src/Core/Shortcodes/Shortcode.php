<?php



namespace Qck\FeedEngine\Core\Shortcodes;
use Qck\FeedEngine\Manifest;
if ( ! defined( 'WPINC' ) ) { die; }
interface Shortcode {
    
    public function get_tag(): string;
    public function render( array $atts, ?string $content = null ): string;
}