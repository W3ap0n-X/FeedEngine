<?php

namespace Qck\FeedEngine\Core\Pages\Components;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Elements\Element;
use Qck\FeedEngine\Core\Pages\Components\Sections\Fields\Field;
use  Qck\FeedEngine\Core\Options\OptionField;
use  Qck\FeedEngine\Core\Options\OptionEntry;

class SettingBuilder {


    private static function get_element_properties($entry) {

    }

    private static function map_type_to_field($type) {
        return match($type) {
            'field' => Field::DEFAULT_FIELD,
            'repeater', 'repeat' => Field::REPEATER_FIELD,
            'settings', 'toplevel' => Field::SETTINGS_FIELD,
            
            default               => Field::DEFAULT_FIELD,
        };
    }

    private static function map_type_to_element($type) {
        return match($type) {
            
            'boolean', 'checkbox' => Element::CHECKBOX_ELEMENT,
            'string','text'  => Element::TEXT_ELEMENT,
            'textarea'  => Element::TEXTAREA_ELEMENT,
            'number'  => Element::NUMBER_ELEMENT,
            'select'  => Element::DROPDOWNLIST_ELEMENT,
            'radio'  => Element::RADIO_ELEMENT,
            'image'  => Element::IMAGE_ELEMENT,
            'custom'  => Element::CUSTOM_ELEMENT,
            default               => Element::TEXT_ELEMENT,
        };
    }
    public static function build_ui_from_section($page, $section_object ) {
        foreach ($section_object->options->get_schema() as $entry) {
            if ( $entry instanceof OptionField) {
                $field = $section_object->add_field(self::map_type_to_field('settings'), [
                    'id' => $entry->key, 
                    'label' => $entry->label, 
                    'description' => $entry->description,
                    'path'  => $section_object->options->get_name() . $entry->get_path(false),
                    'name'  => '['. $entry->key .']',
                    'class' => $entry->class,  
                    'label_element' => $entry->label_element, 
                    'labels' => $entry->labels !== null ? $entry->labels: false,
                ]);
                $field = self::recursive_build($field, $entry, $section_object->options);

            }
            else {
                /* Handle Single Elements not in a parent field */
                $element = self::map_type_to_element($entry->type);
                $section_object->add_field(self::map_type_to_field('settings'), [
                    'id' => $entry->key, 
                    'label' => $entry->label, 
                    'description' => $entry->description,
                    'class' => $entry->class,  
                    'label_element' => $entry->label_element, 
                    'path'  => $section_object->options->get_name() . $entry->get_path(false),
                    'name'  => '['. $entry->key .']',
                ])->add_element($element, [
                    'id' => $entry->key, 
                    'path'  => $section_object->options->get_name() . $entry->get_path(false),
                    'name'  => '['. $entry->key .']',
                    'value' => $section_object->options->get_value_for_entry($entry),
                    'label'       => $entry->label,
                    'labels' => $entry->labels !== null ? $entry->labels: false,
                    'options' => $entry->options, 
                    'list_keys' => $entry->list_keys, 
                    'html' => $entry->html, 
                    'helptext' => $entry->helptext,
                    'class' => $entry->class,  
                    'placeholder' => $entry->placeholder, 
                    'default' => $entry->default, 
                    'label_element' => $entry->label_element, 
                ]);
            }
        }

        return $section_object;
    }

    

    public static function build_ui_from_metabox($obj, $section_object, $settings) {
        foreach ($settings->get_schema() as $key => $entry) {

            if ( $entry instanceof OptionField) {
                $element = self::map_type_to_field($entry->type);
                $field = $section_object->add_field($element, [
                    'id' => $entry->key, 
                    'label' => $entry->label, 
                    'labels' => $entry->labels !== null ? $entry->labels: true,
                    'description' => $entry->description,
                    'meta' => true,
                    'path'  => $settings->get_name() . $entry->get_path(false),
                    'name'  => '['. $entry->key .']',
                    'class' => $entry->class,  
                    'label_element' => $entry->label_element, 
                    'value' =>  isset($obj) ? $settings->get_value_for_field($obj, $entry) : $settings->get_value_for_field($entry),
                ]);
                $field = self::recursive_build($field, $entry, $settings, $obj);
            } else {
                $value = $settings->get_value_for_entry($obj,$entry);
                /* Handle Single Elements not in a parent field */
                $element = self::map_type_to_element($entry->type);
                $section_object->add_field('Field', [
                    'id' => $entry->key, 
                    'label' => $entry->label, 
                    'labels' => true, 
                    'path'  => $settings->get_name() . $entry->get_path(false),
                    'name'  => '['. $entry->key .']',
                    'description' => $entry->description,
                    'label_element' => $entry->label_element, 
                    'meta' => true,
                ])->add_element($element, [
                    'id' => $entry->key, 
                    'path'  => $settings->get_name() . $entry->get_path(false),
                    'name'  => '['. $entry->key .']',
                    'value' => $settings->get_value_for_entry($obj,$entry),
                    'label'       => $entry->label,
                    'labels' => $entry->labels !== null ? $entry->labels: false,
                    'options' => $entry->options, 
                    'list_keys' => $entry->list_keys, 
                    'meta' => true,
                    'html' => $entry->html, 
                    'helptext' => $entry->helptext,
                    'class' => $entry->class,  
                    'placeholder' => $entry->placeholder, 
                    'default' => $entry->default, 
                    'label_element' => $entry->label_element, 
                ]);
            }

        }
        return $section_object;
    }

    

    private static function recursive_build($field , $entry, $settings, $obj = null){
        $dump = [$field , $entry, $settings , $obj];
        foreach($entry->entries as $_entry) {
            $_entry->path = [ ...$entry->path, $entry->key, ...$_entry->path];

            if ( $_entry instanceof OptionField) {
                $element = self::map_type_to_field($_entry->type);
                $sub_field = $field->add_field($element, [
                    'id' => $_entry->key, 
                    'label' => $_entry->label, 
                    'labels' => $_entry->labels !== null ? $_entry->labels: true,
                    'label_element' => $_entry->label_element, 
                    'class' => $_entry->class,  
                    'meta'       => isset($obj) || $entry->meta ? true : false,
                    'description' => $_entry->description,
                    'path'  => $settings->get_name() . $_entry->get_path(false),
                    'name'  => '['. $_entry->key .']',
                    'value' =>  isset($obj) ? $settings->get_value_for_field($obj, $_entry) : $settings->get_value_for_field($_entry),
                ]);
                $sub_field = self::recursive_build($sub_field, $_entry,  $settings , $obj );
                

            }
            else if ($_entry instanceof OptionEntry) {
                $element = self::map_type_to_element($_entry->type);
                $field->add_element($element, [
                    'id' => $_entry->key, 
                    'path'  => $settings->get_name() . $_entry->get_path(false),
                    'name'  => '['. $_entry->key .']',
                    'value' => isset($obj) ? $settings->get_value_for_entry($obj, $_entry) : $settings->get_value_for_entry($_entry),
                    'description' => $_entry->description, 
                    'default' => $_entry->default, 
                    'label'       => $_entry->label,
                    'labels' => $_entry->labels !== null ? $_entry->labels: true,
                    'label_element' => $_entry->label_element, 
                    'meta'       => isset($obj) || $entry->meta ? true : false,
                    'options' => $_entry->options, 
                    'list_keys' => $_entry->list_keys, 
                    'html' => $_entry->html, 
                    'helptext' => $_entry->helptext,
                    'class' => $_entry->class,  
                    'placeholder' => $_entry->placeholder, 
                ]);
            }
                
        }
        return $field;
    }

}