<?php

namespace Qck\FeedEngine\Core\Options;

class CustomCommentTypeOptions extends \Qck\FeedEngine\Core\Options\OptionSection {

    protected $type_slug;
    public function __construct($comment_type_slug){
        $this->type_slug = $comment_type_slug;
    }
    
    public function get_name(): string {
        return $this->type_slug . '_settings'; 
    }

    public function get_title(): string {
        return 'Type Settings';
    }

    public function get_description(): string {
        return 'Section Description';
    }

    public function get_schema(): array {
        return [
            

        ];
    }
}