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

		];
		return $actions;
	}

	public static function easydump( $var, $label = null) {
		return (isset($label) ? '<h4>' . $label . '</h4>' : '') . '<pre>' . print_r($var, true) . '</pre>';
	}
}