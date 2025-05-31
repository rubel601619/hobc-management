<?php
// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Optionally delete plugin options from the database
delete_option('hobc_settings'); // If you save any settings in options

// Optionally remove usermeta or other plugin-specific metadata
// global $wpdb;
// $wpdb->query("DELETE FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE 'hobc_%'");

// Optionally delete custom post types and their data
// $players = get_posts([
//     'post_type' => 'hobc_player',
//     'numberposts' => -1,
//     'post_status' => 'any'
// ]);

// foreach ($players as $player) {
//     wp_delete_post($player->ID, true);
// }

// You can also remove custom tables if your plugin creates any
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}hobc_players");

