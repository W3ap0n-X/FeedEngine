<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

use Qck\FeedEngine\Core\Pages\Components\Interfaces\HTML;
use Qck\FeedEngine\Core\Options\Options;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom extends Element {

    /**
     * @var string HTML to display.
     */
    private $html = null;

    /**
     * Render the element.
     */
    public function render() {
        ?>

        <div class="custom-element">

            <?php
            if ( ! empty( $this->html ) ) {
                echo $this->html;
            }
            ?>

        </div>

        <?php
    }

    /**
     * Custom_Element constructor.
     *
     * @param string  $section_id       Section ID.
     * @param Options $options_instance An instance of `Options`.
     * @param array   $properties       Element properties.
     */
    public function __construct( $section_id, $options_instance, $properties = array() ) {
        parent::__construct( $section_id, $options_instance, $properties );

        $this->html = $properties['html'];

        if ( $properties['html'] instanceof HTML ) {
            $this->html = $properties['html']->get_html();
        }
    }

}