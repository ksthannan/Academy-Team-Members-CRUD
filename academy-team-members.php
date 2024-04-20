<?php
/*
Plugin Name: Academy Team Members
Plugin URI: #
Description: Academy Team Members is a plugin designed to help you manage lists and all the information for team members.
Version: 1.0
Author: Md. Abdul Hannan
Author URI: #
Text Domain: tmembers
Domain Path: /languages
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define required constants
 */
define( 'ACADEMY_VER', '1.0.0' );
define( 'ACADEMY_URL', plugins_url('', __FILE__) );
define( 'ACADEMY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACADEMY_URL_ASSETS', ACADEMY_URL . '/assets' );

/**
 * Autoload require
 */
require_once __DIR__ . "/vendor/autoload.php";


class Academy_Team_Members {
    /**
     * Properties
     */
    private static $instance = null;

    function __construct() {
        // admin enqueue 
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_frontend_assets'));

        // load features 
        add_action('init', array($this, 'initialize_features'));

        // funnctions 
        new academy\teammembers\Functions();
    }

    /**
     * Instance
     */
    public static function get_instance() {
        if ( self::$instance == null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize features
     */
    public function initialize_features() {
        load_plugin_textdomain( 'tmembers', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Enqueue frontend assets
     */
    public function admin_enqueue_frontend_assets( ) {
        wp_enqueue_style( 'academy-style', ACADEMY_URL_ASSETS . '/css/admin.css', array(), ACADEMY_VER, 'all' );
        wp_enqueue_script( 'academy-script', ACADEMY_URL_ASSETS . '/js/admin.js', array( 'jquery' ), ACADEMY_VER, true );
    }

}

/**
 * Instantiate
 */
Academy_Team_Members::get_instance();