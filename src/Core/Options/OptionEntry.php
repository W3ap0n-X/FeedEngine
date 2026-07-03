<?php


namespace Qck\FeedEngine\Core\Options;

class OptionEntry {

    public $key;
    public $label;
    public $labels;
    public $type;
    public $default;
    public $description;
    public $helptext;
    public $placeholder;

    public $label_element;
    public $list_keys;
    public $path;
    public $options;
    public $disabled;

    public $html;
    public $class;

    public function __construct( $key, $label, $type = 'text', $default = null, $path = [], $options = ['none'] , $description = '', $helptext = '', $placeholder = '', $html = '', $class = '', $labels = null, $list_keys = false , $disabled = false , $label_element = null ) {
        $this->key     = $key;
        $this->label   = $label;
        $this->labels   = $labels;
        $this->label_element       = $label_element;
        $this->description = $description;
        $this->list_keys  = $list_keys;
        $this->disabled  = $disabled;
        
        
        
        $this->path    = $path;
        $this->html    = $html;
        $this->class    = $class;

        

        $this->type    = $type;
        $this->options = $options;
        $this->placeholder = $placeholder;
        $this->helptext = $helptext;
        $this->default = $default;
    }

    public function get_ui_name(){
        $path  = (! empty( $this->path ) ) ? $this->path : [];
        if (is_string($path) && !str_contains($path, '.')) {
            $path = is_array($path) ? $path : explode('.', $path);
        }
        $output = '';
        foreach ($path as $key) {
            $output .=  $key . '][';
        }
        $output .= "" . $this->key . '';
        return $output;
        
    }

    public function get_path($withKey = true): string {
        // If there is no path, just return section[key]
        if (empty($this->path)) {
            if($withKey){
                return sprintf('[%s]', $this->key);
            }
            return '';
        }

        // Build the middle pieces: [sub][group]
        $mid_path = implode('][', $this->path);

        // Result: section_id[sub][group][key]
        if($withKey){
            return sprintf('[%s][%s]', $mid_path, $this->key);
        }
        
        return sprintf('[%s]', $mid_path);
    }
}