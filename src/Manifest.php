<?php
namespace Qck\FeedEngine;
if ( ! defined( 'WPINC' ) ) { die; }






/**
 * Plugin Meta Registry
 */
final class Manifest {
    // Static meta
    public const VERSION = '0.0.1.3';
    public const PREFIX  = 'qckfe';
    public const NAME    = 'FeedEngine';
    public const SLUG    = 'qck-feed-engine';

    // Dynamic paths (Handled via methods to stay reliable)
    public static function path(string $path = ''): string {
        // We go up one level because this file is in /src/
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    public static function url(string $path = ''): string {
        return plugins_url(ltrim($path, '/'), self::path(self::SLUG . '.php'));
    }

    /**
     * @var array Default options.
     */
    public const DEFAULT_OPTIONS = array(
		// >>> Acts as junk drawer
		'general_options' => array(
            'debug' => true,

		), 
        'test_options' => array(
            'debug' => true,

		), 
	);

    public static function details(){
        $details = array(
            'Name' => self::NAME , 
            'Slug' => self::SLUG , 
            'Version' => self::VERSION , 
            'Prefix' => self::PREFIX , 
        );
        return $details;
    }
}