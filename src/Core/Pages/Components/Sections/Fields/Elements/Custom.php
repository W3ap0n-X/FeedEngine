<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

use Qck\FeedEngine\Core\Pages\Components\Interfaces\HTML;
use Qck\FeedEngine\Core\Options\Options;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom extends Element {

    
    private $html = null;

    
    public function render() {
        $content = ! empty( $this->html ) ? $this->html : '';
        $html = <<<HTML
            <div class="custom-element">
                {$content}

            </div>
        HTML;
        return $html;
    }

    
    public function __construct( $section_id, $options_instance, $properties = array() ) {
        parent::__construct( $section_id, $options_instance, $properties );

        $this->html = $properties['html'];

        if ( $properties['html'] instanceof HTML ) {
            $this->html = $properties['html']->get_html();
        }
    }

}