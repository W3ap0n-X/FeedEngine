<?php
namespace Qck\FeedEngine\Core\Integrations;

class ElementorBridge {
    public function init() {
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    public function register_widgets($widgets_manager) {
        // You just register the wrapper; the wrapper does the heavy lifting
        // $widgets_manager->register(new \Qck\FeedEngine\Integrations\Elementor\BentoWidget());
    }
}