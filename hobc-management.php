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

define('HOBC_PLUGIN_PATH', plugin_dir_path(__FILE__));

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
        register_activation_hook(__FILE__, [ $this, 'plugin_activated' ] );

        // user register
        add_shortcode('hobc_registration_form', [ $this, 'user_register' ] );

        // render players
        add_shortcode('hobc_players', [ $this, 'render_players' ] );

        // enqueue frontend style and scripts
        add_action('wp_enqueue_scripts', [ $this, 'enqueue_fontend_scripts' ], 15 );
    }
    
    /**
     * render_players()
     * 
     * This method render the players list
     * 
     * @param $atts attributes of shortcode
     * 
     * @return Player list
     */
    public function render_players( $atts ){

        $per_page = get_option('player_per_page', 10);

        $atts = shortcode_atts(
            [
                'per_page' => $per_page
            ],
            $atts
        );

        $paged = max(1, get_query_var('paged') ?: get_query_var('page') ?: 1);
        $per_page = intval($atts['per_page']);

        $args = [
            'role'    => 'player',
            'orderby' => 'registered',
            'order'   => 'DESC',
            'number'  => $per_page,
            'offset'  => ($paged - 1) * $per_page,
        ];

        $players = get_users($args);
        $total_players = count(get_users(['role' => 'player', 'fields' => 'ID']));

        if (empty($players)) {
            return '<p>No players found.</p>';
        }

        ob_start();

        echo '<div class="hobc-player-list">';

        foreach ($players as $player) {
            $user_id = $player->ID;
            $name = $player->display_name;
            $category = get_user_meta($user_id, 'hobc_category', true);
            $contact = get_user_meta($user_id, 'hobc_contact', true);
            $club_team = get_user_meta($user_id, 'hobc_club_team', true);
            $terms = get_user_meta($user_id, 'hobc_terms', true);
            $image_id = get_user_meta($user_id, 'hobc_profile_image', true);
            $image_url = $image_id ? wp_get_attachment_url($image_id) : 'https://via.placeholder.com/150';

            include plugin_dir_path(__FILE__) . 'views/player-list.php';
        }

        echo '</div>';

        // Pagination
        $total_pages = ceil($total_players / $per_page);
        $current_url = remove_query_arg('paged');

        include plugin_dir_path(__FILE__) . 'views/player-list-pagination.php';

        return ob_get_clean();
    }

    /**
     * enqueue_fontend_scripts()
     * 
     * This method enqueue the frontend style and scripts.
     * 
     * @return void
     */
    public function enqueue_fontend_scripts(){
         wp_enqueue_style(
            'hobc-frontend-style',
            plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
            array(), // Dependencies
            '1.0.0'
        );
    }

    /**
     * user_register()
     * 
     * handle user registration as a player
     * 
     * @return void
     */
    public function user_register(){
        ob_start();
        // Show errors if set
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hobc_register_user'])) {
            $this->process_registration();
        }

        include plugin_dir_path(__FILE__) . 'views/registration-form.php';

        return ob_get_clean();
    }

    /**
     * process_registration()
     * 
     * this method use for register prosesing.
     * 
     * @return void
     */
    public function process_registration(){
        $name      = sanitize_text_field($_POST['hobc_name']);
        $contact   = sanitize_text_field($_POST['hobc_contact']);
        $category  = sanitize_text_field($_POST['hobc_category']);
        $club_team = sanitize_text_field($_POST['hobc_club_team']);
        $email     = sanitize_email($_POST['hobc_email']);
        $password  = $_POST['hobc_password'];
        $terms     = isset($_POST['hobc_terms']) ? true : false;

        // Validation
        if (!$terms) {
            echo '<div style="color:red;">You must agree to the terms.</div>';
            return;
        }

        if (email_exists($email)) {
            echo '<div style="color:red;">This email is already registered.</div>';
            return;
        }

        // Username from email (you can customize this)
        $username = sanitize_user(current(explode('@', $email)));

        // Create user
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            echo '<div style="color:red;">Error creating account.</div>';
            return;
        }

        // Set role to player
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $name,
            'role' => 'player'
        ]);

        update_user_meta($user_id, 'hobc_contact', $contact);
        update_user_meta($user_id, 'hobc_category', $category);
        update_user_meta($user_id, 'hobc_club_team', $club_team);
        update_user_meta($user_id, 'hobc_terms', $terms);


        $this->upload_profile( $user_id );
        
        echo '<div style="color:green;">Registration successful! You can now log in.</div>';

    }

    /**
     * upload_profile()
     * 
     * this method upload the fprofile image while user registration
     * 
     * @return void
     */
    public function upload_profile( $user_id ){
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        if (!empty($_FILES['hobc_profile_image']['name'])) {

            $file = $_FILES['hobc_profile_image'];

            $upload_overrides = ['test_form' => false];

            $movefile = wp_handle_upload($file, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                // Upload successful
                $filename = $movefile['file'];
                $wp_filetype = wp_check_filetype($filename, null);
                $attachment = [
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name(basename($filename)),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                ];

                $attach_id = wp_insert_attachment($attachment, $filename);

                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);

                wp_update_attachment_metadata($attach_id, $attach_data);

                
                update_user_meta($user_id, 'hobc_profile_image', $attach_id);

            } else {
                echo '<div style="color:red;">Image upload failed: ' . esc_html($movefile['error']) . '</div>';
            }
        }
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
     * change_permalink_rewirte_rules this method update the permalink to postname.
     * 
     * @return void
     */
    public function plugin_activated(){
        
        global $wp_rewrite;

        // enable registration
        update_option('users_can_register', 1);

        // rewrite flush rule
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();

        // add role as a player
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