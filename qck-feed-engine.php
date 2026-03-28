<?php
/**
 * Plugin Name: Feed Engine
 * Description: Feeds for days
 * Version: 0.0.1.3
 * Author: Me
 */


namespace Qck\FeedEngine;

if ( ! defined( 'WPINC' ) ) { die; }



// Load the Autoloader
require_once plugin_dir_path( __FILE__ ) . 'src/Manifest.php';
require_once plugin_dir_path( __FILE__ ) . 'src/Autoloader.php';
Autoloader::register();

/* ***** Activation Hooks *****

*/
register_activation_hook( __FILE__, [Core\Activator::class, 'activate'] );
register_deactivation_hook( __FILE__, [Core\Activator::class, 'deactivate'] );
register_uninstall_hook( __FILE__, [ \Core\Install\Uninstall::class, 'cleanup' ] );

// Ignition
require plugin_dir_path( __FILE__ ) . 'src/Core/Plugin.php';

new Plugin();