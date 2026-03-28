<?php
namespace Qck\FeedEngine\Core;
/**
 * Activator Class
 * 
 * @since     1.0.0
 */
class Debug implements Actions {

    public function __construct($options) {
        
    }

    private function get_actions() {
		$actions = [
			 'wp_after_admin_bar_render' => array( 'ed_setup' ) 

		];
		return $actions;
	}

    public function ed_setup(){
		add_action( 'easydump', array( $this, 'easydump' ) , 10, 2);
		
	}

	public function easydump( $var, $label = null) {
		echo (isset($label) ? '<h4>' . $label . '</h4>' : '') . '<pre>' . print_r($var, true) . '</pre>';
	}
}