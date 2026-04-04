<?php
namespace Qck\FeedEngine\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Hooks\Actions;
use Qck\FeedEngine\Core\Pages\TopPage;
use Qck\FeedEngine\Core\Pages\Components\SettingBuilder;



class SettingsPage extends TopPage implements Actions {

    

    public function __construct(  $hooks) {
        parent::__construct(  $hooks );
    }



    
    protected function get_menu_title() {
        return __( Manifest::NAME, Manifest::SLUG );
    }

    
    protected function get_page_title() {
        return __( Manifest::NAME . ' Settings', Manifest::SLUG );
    }

    
    // protected function get_icon_url() {
    //     return 'dashicons-shield-alt';
    // }

    
    public function get_slug() {
        return Manifest::PREFIX . '_settings';
    }

    
    public function register_sections() {
        $this->add_section( new \Qck\FeedEngine\Options\GeneralOptions() );
        // $this->add_section( new \Qck\FeedEngine\Options\BentoOptions() );

        

        foreach ($this->sections as  $section) {
            SettingBuilder::build_ui_from_section($this->get_slug(), $section);
        }
    }

}
