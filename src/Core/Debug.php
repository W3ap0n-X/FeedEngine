<?php
namespace Qck\FeedEngine\Core;
use Qck\FeedEngine\Manifest;
use Qck\FeedEngine\Core\Diagnostics\Logging\Logger;
/**
 * Activator Class
 * 
 * @since     1.0.0
 */
class Debug {

    private $options;
    private $hooks;
    public function __construct($options, $hooks) {
        $this->options = $options;
        $this->hooks = $hooks;
    }

	public static function easydump( $var, $label = null) {
		return (isset($label) ? '<div><h4>' . $label . '</h4>' : '') . '<pre>' . print_r($var, true) . '</pre></div>';
	}

    public static function logDump($var, $label = null, $log_level = 700) {
        $log_level = $log_level < 0 ? 0 : $log_level ; 
        $log_level = $log_level > 999 ? 999 : $log_level ; 
        $debug_options = get_option(Manifest::PREFIX . '_debug');
        if(! $debug_options) { 
            Logger::log( '('. $log_level .') ' . (isset($label) ? $label  . "\n" : '') . print_r($var, true)); 
            Logger::log( 'BTW :: logs are busted'); 
            return;
        }

        if($debug_options['enable'] && $log_level <= $debug_options['log_level'] ) {
            Logger::log( '('. $log_level .') ' . (isset($label) ? $label  . "\n" : '') . print_r($var, true));
        }

        
    }
}