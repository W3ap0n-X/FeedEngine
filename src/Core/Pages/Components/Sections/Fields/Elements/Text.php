<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Text extends Element implements SettingsInterface {

    /**
     * Render the element.
     */
    public function render() {
        ?>

        <fieldset>
            <label>
                <input
                    type="text"
                    name="<?php echo esc_attr( $this->name ); ?>"
                    id="<?php echo esc_attr( $this->name ); ?>"
                    value="<?php echo esc_attr( $this->value ); ?>"
                />

                <?php echo esc_html( $this->label ); ?>
            </label>
        </fieldset>

        <?php
    }

    /**
     * Sanitize the given option value.
     *
     * @param string $option_value
     *
     * @return bool
     */
    public function sanitize( $option_value ) {
        return sanitize_text_field( $option_value );
    }

}