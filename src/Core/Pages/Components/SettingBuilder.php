<?php

namespace Qck\FeedEngine\Core\Pages\Components;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;

class SettingBuilder {
    public static function build_ui_from_section($page, $section_object ) {
        

        foreach ($section_object->options->define_fields() as $entry) {
            // THE FACTORY LOGIC: Map Type to Element
            $element = match($entry->type) {
                'boolean' => Element::CHECKBOX_ELEMENT,
                'checkbox' => Element::CHECKBOX_ELEMENT,
                'string'  => Element::TEXT_ELEMENT,
                'number'  => Element::NUMBER_ELEMENT,
                'select'  => Element::RADIO_ELEMENT,
                'custom'  => Element::CUSTOM_ELEMENT,
                default   => Element::TEXT_ELEMENT,
            };
            $html_name = $section_object->options->get_name() . $entry->get_path();
            // Automatically set the value and the "Rhyming" name
            // $element->set_value($current_values[$entry->key] ?? $entry->default);
            // $element->set_name($section_object->get_option_name() . '[' . $entry->key . ']');
            
            $section_object->add_field([
                'id' => $entry->key, 
                'label' => $entry->label, 
                'description' => $entry->description
                ])->add_element($element, [
                    'label' => $entry->label, 
                    'description' => $entry->description, 
                    'name' => $html_name,
                    'value' => $section_object->options->get_value_for_entry($entry),
                ]);
        }

        return $section_object;
    }
}