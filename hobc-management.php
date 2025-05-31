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
add_action('plugins_loaded', function() {
    new HOBC_Admin();
});
