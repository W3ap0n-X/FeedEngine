<?php
namespace Qck\FeedEngine;
if ( ! defined( 'WPINC' ) ) { die; }






/**
 * Plugin Meta Registry
 */
final class Manifest {
    // Static meta
    public const VERSION = '0.0.1.4';
    public const PREFIX  = 'qckfe';
    public const NAME    = 'FeedEngine';
    public const SLUG    = 'qck-feed-engine';
    public const MENU_SLUG    = 'qck_feed_engine';

    // Dynamic paths (Handled via methods to stay reliable)
    public static function path(string $path = ''): string {
        // We go up one level because this file is in /src/
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }

    public static function url(string $path = ''): string {
        return plugins_url(ltrim($path, '/'), self::path(self::SLUG . '.php'));
    }

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