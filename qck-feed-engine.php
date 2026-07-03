<?php
/**
 * Plugin Name: FeedEngine
 * Description: Create curated lists of posts and objects and display them fast and efficiently.
 * Version: 0.0.5
 * Author: Qck
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
register_uninstall_hook( __FILE__, [ Core\Install\Uninstall::class, 'cleanup' ] );

// Ignition
require plugin_dir_path( __FILE__ ) . 'src/Plugin.php';

new Plugin();