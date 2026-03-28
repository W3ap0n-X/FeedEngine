<?php
/**
 * Plugin Name: Retail Hub Feed Engine
 * Description: High-performance, cached MVC-style feed generator for Retail Hub.
 * Version: 1.0.0
 * Author: Your Name
 */


namespace Qck\FeedEngine;

if ( ! defined( 'WPINC' ) ) { die; }



// Load the Autoloader
require_once plugin_dir_path( __FILE__ ) . 'src/Autoloader.php';
Autoloader::register();

/* ***** Activation Hooks *****

*/
register_activation_hook( __FILE__, [Core\Activator::class, 'activate'] );
register_deactivation_hook( __FILE__, [Core\Activator::class, 'deactivate'] );
register_uninstall_hook( __FILE__, [ \Core\Install\Uninstall::class, 'cleanup' ] );

// Ignition
add_action( 'plugins_loaded', function() {
    Core\Plugin::instance()->run();
});