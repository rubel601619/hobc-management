<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class HOBC_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('init', [$this, 'register_events']);
        add_action('admin_init', [ $this, 'hobc_register_settings' ] ) ;

        add_action('admin_notices', [ $this, 'render_admin_notice' ] );
    }

    public function render_admin_notice(){
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            echo '<div class="notice notice-success is-dismissible">
                    <p><strong>Settings saved successfully.</strong></p>
                </div>';
        }
    }

    public function hobc_register_settings(){
        register_setting('hobc_settings_group', 'player_per_page');
        register_setting('hobc_settings_group', 'hobc_enable_pagination');
    }
    

    /**
     * register custom post type for menu events
     * 
     * @return void
     */
    public function register_events() {
        $labels = [
            'name' => __('Events', 'hobc-management'),
            'singular_name' => __('Event', 'hobc-management'),
            'add_new' => __('Add New Event', 'hobc-management'),
            'add_new_item' => __('Add New Event', 'hobc-management'),
            'edit_item' => __('Edit Event', 'hobc-management'),
            'new_item' => __('New Event', 'hobc-management'),
            'view_item' => __('View Event', 'hobc-management'),
            'search_items' => __('Search Events', 'hobc-management'),
            'not_found' => __('No events found', 'hobc-management'),
            'not_found_in_trash' => __('No events found in Trash', 'hobc-management'),
            'menu_name' => __('Events', 'hobc-management'),
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_position' => null,
            'show_in_menu' => false,
            'supports' => ['title', 'editor', 'thumbnail'],
        ];

        register_post_type('hobc_event', $args);
    }

    /**
     * register_admin_menu()
     * This methods register all the admin menu and submenu
     * 
     * @return void
     */
    public function register_admin_menu() {
        add_menu_page(
            __('HOBC Management', 'hobc-management'),
            __('HOBC', 'hobc-management'),
            'manage_options',
            'hobc-management',
            [$this, 'render_dashboard'],
            'dashicons-groups',
            26 
        );

        // add submenu for general settings
        add_submenu_page(
            'hobc-management',
            __('General', 'hobc-management'),
            __('General', 'hobc-management'),
            'manage_options',
            'hobc-management',
            [$this, 'render_dashboard']
        );

        // add submenu for events
        add_submenu_page(
            'hobc-management',
            __('Events', 'hobc-management'),
            __('Events', 'hobc-management'),
            'manage_options',
            'edit.php?post_type=hobc_event'
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
            HOBC_PLUGIN_URL. 'assets/css/admin-style.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'hobc-admin-script',
            HOBC_PLUGIN_URL . 'assets/js/admin-script.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }

    /**
     * Render the main dashboard page
     */
    public function render_dashboard() { ?>

        <div class="wrap">
            <h1><?php echo esc_html__('HOBC Dashboard', 'hobc-management') ?></h1>

            <?php
                include HOBC_PLUGIN_PATH . 'views/admin/render-general-settings.php';
            ?>
            
        </div>

        <?php
    }
}
