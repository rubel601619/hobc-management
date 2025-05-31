<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class HOBC_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Register the plugin admin menu
     */
    public function register_admin_menu() {
        add_menu_page(
            __('HOBC Management', 'hobc-management'), // Page title
            __('HOBC', 'hobc-management'),            // Menu title
            'manage_options',                         // Capability
            'hobc-management',                        // Menu slug
            [$this, 'render_dashboard'],              // Callback function
            'dashicons-groups',                       // Icon
            26                                        // Position
        );
    }

    /**
     * Enqueue styles and scripts for admin pages
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin's page
        if ($hook !== 'toplevel_page_hobc-management') {
            return;
        }

        wp_enqueue_style(
            'hobc-admin-style',
            plugin_dir_url(__FILE__) . '../css/admin-style.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'hobc-admin-script',
            plugin_dir_url(__FILE__) . '../js/admin-script.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }

    /**
     * Render the main dashboard page
     */
    public function render_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('HOBC Management Dashboard', 'hobc-management') . '</h1>';
        echo '<p>' . esc_html__('Manage badminton players and teams.', 'hobc-management') . '</p>';
        // Additional content or tabs can be rendered here
        echo '</div>';
    }
}
