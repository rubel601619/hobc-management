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

        add_action('admin_init', [ $this, 'delete_player' ] );
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

        // add submenu for players management
        add_submenu_page( 
            'hobc-management',
            'Players',
            'Players',
            'manage_options',
            'manage-players',
            [ $this, 'display_players' ]
        );
    }

    /**
     * display_players()
     * 
     * This methos render the players from admin dashboard.
     */
    public function display_players(){
        $paged    = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = get_option('player_per_page', 10);
        $offset   = ($paged - 1) * $per_page;

        $args = [
            'role'    => 'player',
            'number'  => $per_page,
            'offset'  => $offset,
            'orderby' => 'registered',
            'order'   => 'DESC',
        ];

        $user_query = new WP_User_Query($args);
        $players    = $user_query->get_results();
        $total      = $user_query->get_total();

        echo '<div class="wrap"><h1 class="mb-3">Manage Players</h1>';
        
        if (!empty($_GET['deleted'])) {
            echo '<div class="notice notice-success is-dismissible"><p>Player deleted successfully.</p></div>';
        }

        if ($players) {
            echo '<table class="widefat striped"><thead><tr>
                    <th>Name</th><th>Email Address</th>
                </tr></thead><tbody>';

            foreach ($players as $player) {
                $edit_url   = admin_url('user-edit.php?user_id=' . $player->ID);
                $delete_url = wp_nonce_url(admin_url('admin.php?page=manage-players&action=delete&user_id=' . $player->ID), 'hobc_delete_player');

                include HOBC_PLUGIN_PATH . 'views/admin/players.php';                
            }

            echo '</tbody></table>';

            // Pagination
            $total_pages = ceil($total / $per_page);
            if ($total_pages > 1) {
                $base_url = admin_url('admin.php?page=manage-players');

                echo '<div class="tablenav"><div class="tablenav-pages">';
                if ($paged > 1) {
                    echo '<a class="button" href="' . add_query_arg('paged', $paged - 1, $base_url) . '">&laquo; Prev</a>';
                } else {
                    echo '<span class="button disabled">&laquo; Prev</span>';
                }

                if ($paged < $total_pages) {
                    echo '<a class="button ms-2" href="' . add_query_arg('paged', $paged + 1, $base_url) . '">Next &raquo;</a>';
                } else {
                    echo '<span class="button disabled ms-2">Next &raquo;</span>';
                }

                echo '</div></div>';
            }
        } else {
            echo '<p>No players found.</p>';
        }

        echo '</div>';
    }

    public function delete_player(){
        if (isset($_GET['page'], $_GET['action'], $_GET['user_id']) 
            && $_GET['page'] === 'manage-players'
            && $_GET['action'] === 'delete'
            && current_user_can('delete_users')
            && wp_verify_nonce($_GET['_wpnonce'], 'hobc_delete_player')) {
            
            $user_id = intval($_GET['user_id']);
            wp_delete_user($user_id);
            wp_redirect(admin_url('admin.php?page=manage-players&deleted=1'));
            exit;
        }
    }

    /**
     * Enqueue styles and scripts for admin pages
     */
    public function enqueue_admin_assets($hook) {
        wp_enqueue_style(
            'hobc-admin-style',
            HOBC_PLUGIN_URL. 'assets/css/admin-style.css',
            [],
            '1.0.0'
        );
        // Only load on our plugin's page
        if ($hook !== 'toplevel_page_hobc-management') {
            return;
        }

        

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
