<?php
namespace Qck\FeedEngine\Core\Pages\Components\Sections;

use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SettingsSection extends Section {

    /**
     * Return all element objects in this section.
     *
     * @return Element[]
     */
    private function get_all_elements_in_section() {

        $elements = array();
        
        foreach ( $this->fields as $field ) {
            $elements = array_merge( $elements, $field->get_elements() );
        }
        // \Qck\FeedEngine\Core\Debug::logDump($elements, __METHOD__);
        return $elements;
    }

    /**
     * Sanitize the options' values.
     *
     * @param array $options
     *
     * @return array
     */
    public function sanitize( $options ) {
        // \Qck\FeedEngine\Core\Debug::logDump($options, __METHOD__);
        $elements = $this->get_all_elements_in_section();

        foreach ( $options as $key => $value ) {
            $element         = $elements[ $key ];
            $sanitized_value = $element->sanitize( $value );
            $validate        = $element->get_validate();
            $pre_write       = $element->get_pre_write();

            if ( is_callable( $validate ) && ! $validate( $sanitized_value ) ) {
                $sanitized_value = $element->get_value();
            }

            if ( is_callable( $pre_write ) ) {
                $sanitized_value = $pre_write( $sanitized_value );
            }

            $options[ $key ] = $sanitized_value;
        }

        return $options;
    }

}