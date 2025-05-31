<?php
/*
Plugin Name: HOBC Management
Plugin URI: https://github.com/rubel601619/hobc-management
Description: Manage and organize badminton players, profiles, rankings, and matches within WordPress.
Version: 1.0.0
Author: Rubel Mahmud (Sujan)
Author URI: https://velixtech.com
License: GPL2
Text Domain: hobc-management
Domain Path: /lang
*/

defined('ABSPATH') || exit;

// Include core files
require_once plugin_dir_path(__FILE__) . 'inc/class-hobc-admin.php';
require_once plugin_dir_path(__FILE__) . 'inc/class-hobc-player.php';
require_once plugin_dir_path(__FILE__) . 'inc/functions.php';

// Initialize admin menus

final class HOBC_Management{

    private static $instance;

    public function __construct(){

        // plugin loaded
        add_action( 'plugins_loaded', [ $this, 'plugin_loaded' ] );
        
        // add settings link below the plugin list
        add_filter( 'plugin_action_links', [ $this, 'add_action_links' ], 10, 2 );
    }

    /**
     * initiate the plugin file when it's loaded.
     * 
     * @return void
     */
    public function plugin_loaded(){
        new HOBC_Admin();
    }

    public function add_action_links( $links, $file ){
        if ( plugin_basename( __FILE__ ) === $file ){
            $anchor_tag = sprintf(
                '<a href="%1$s">%2$s</a>',
                admin_url('admin.php?page=hobc-management'),
                esc_html__( 'Settings', 'hobc-management' )
            );
            array_unshift( $links, $anchor_tag );
        }

        return $links;
    }
    

    /**
     * create singleton instance
     *
     * @return void
     */
    public static function init(){
        if ( is_null( self::$instance ) ){
            self::$instance = new self();
        }
        return self::$instance;
    }
}


/**
 * initiate the main plufin
 * 
 * @return void
 */
if( !function_exists( 'hobc_management' ) ){

    function hobc_management(){
        return HOBC_Management::init();
    }

    hobc_management();
}

