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
require_once plugin_dir_path(__FILE__) . 'autoload.php';

// Initialize admin menus

final class HOBC_Management{

    private static $instance;

    public function __construct(){

        // plugin loaded
        add_action( 'plugins_loaded', [ $this, 'plugin_loaded' ] );
        
        // add settings link below the plugin list
        add_filter( 'plugin_action_links', [ $this, 'add_action_links' ], 10, 2 );

        // update permalink to postname
        register_activation_hook(__FILE__, [ $this, 'change_permalink_rewirte_rules' ] );

        register_activation_hook(__FILE__, [ $this, 'enable_user_register' ] );
    }

    /**
     * initiate the plugin file when it's loaded.
     * 
     * @return void
     */
    public function plugin_loaded(){
        new HOBC_Admin();
    }

    /**
     * enable_user_register()
     * This method enable Membership option so that user can register
     * 
     * @return void
     */
    public function enable_user_register(){
        update_option('users_can_register', 1);
    }

    /**
     * change_permalink_rewirte_rules this method update the permalink to postname.
     * 
     * @return void
     */
    public function change_permalink_rewirte_rules(){
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();

        add_role(
            'player',
            __('Player', 'hobc-management'),
            [
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
            ]
        );
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