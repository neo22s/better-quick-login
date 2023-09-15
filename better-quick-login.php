<?php
/**
 * Plugin Name: Better Quick Login
 * Plugin URI: https://garridodiaz.com/better-quick-login/
 * Description: Passwordless login system for WordPress.
 * Version: 1.0
 * Text Domain: better-quick-login
 * Author: Chema
 * Author URI: https://garridodiaz.com
 * License: GPL2
 */

defined('ABSPATH') or die('Slow down cowboy');

require_once(plugin_dir_path(__FILE__) . 'classes/widget.php');

class BetterQuickLogin
{

    const MAIN_FILE = __FILE__;
    static $message = NULL;

    public function __construct()
    {
        // Initialize the plugin by adding hooks and actions
        add_action( 'init', [$this, 'addPluginTextDomain']);
        add_action( 'init', [$this, 'loginRequest']);
        add_action( 'init', [$this, 'autoLogin']);
        add_action( 'init', [$this, 'registerCustomBlock']);
        add_action( 'init', [$this, 'forceOneSessionPerUser'],99);
        add_action('admin_notices', [$this, 'displayDonationMessage']);
        add_action('admin_head', [$this, 'addDonationMessageJS']);
        add_action('login_header', [$this, 'customLoginForm']);
        add_action('widgets_init', [$this, 'registerLoginWidget']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockAssets']);
        add_action('wp_enqueue_scripts',[$this, 'enqueueStyles']);
        add_action('login_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('admin_init', [$this, 'registerPluginSettings']);
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_shortcode('quicklogin', array($this, 'loginForm'));
        add_filter('plugin_row_meta', [$this, 'addPluginRowMeta'], 10, 2);
        add_filter('the_content', [$this, 'displayMessage']);
        register_activation_hook(self::MAIN_FILE, [$this, 'setDefaultOptions']);
    }

    public function displayMessage($content) { 
        if ( isset(self::$message) ){
            $html =  include(plugin_dir_path(self::MAIN_FILE) . 'templates/message.php');
            self::$message = NULL;
            return $html.$content;
        }

        return $content;
    }

    public function enqueueStyles() {
        wp_enqueue_style('quick-login-style', plugin_dir_url(__FILE__) . 'css/styles.css', array(), '1.0');
    }

    public function registerCustomBlock() {
        register_block_type('quick-login/quick-login-block', [
            'editor_script' => 'quick-login-block',
        ]);
    }

    public function enqueueBlockAssets() {
        // Enqueue your JavaScript file for the custom block
        wp_enqueue_script(
            'quick-login-block', 
            plugin_dir_url(__FILE__) . 'blocks/quick-login-block.js', 
            ['wp-blocks', 'wp-element'], 
            filemtime(plugin_dir_path(__FILE__) . 'blocks/quick-login-block.js'), 
            true 
        );
    }


    public function registerLoginWidget() {
        register_widget('BetterQuickLoginWidget');
    }


    public function customLoginForm() { 
        if ( get_option('bql_login_form') == 1 ){
            ob_start();
            include(plugin_dir_path(self::MAIN_FILE) . 'templates/message.php');
            echo ob_get_clean();
            self::$message = NULL;

            return include(plugin_dir_path(self::MAIN_FILE) . 'templates/login-form.php');
        }
    }


    public function loginForm() {
        ob_start();
        $template =  (is_user_logged_in()) ? 'already-logged.php':'login-form.php';
        include(plugin_dir_path(self::MAIN_FILE) . 'templates/'.$template);
        return ob_get_clean();
    }

    /**
     * Handles the request of a login and sends the email with a link to login
     * 
     */
    public function loginRequest() {

        if (is_user_logged_in())
            return;

        $nonce = ( isset( $_POST['nonce']) ) ? sanitize_key( $_POST['nonce'] ) : false;

        if ( isset( $_POST['ql_username_email']) 
            AND wp_verify_nonce( $nonce, 'quick_login_request' ) ) {

            $user_login = sanitize_text_field( $_POST['ql_username_email'] );

            // send link via email
            $user = get_user_by(is_email($user_login) == FALSE ?'login':'email', $user_login);

            if ($user) {
                // Generate a unique token (for simplicity, using a timestamp)
                $token = md5(uniqid().$user->ID).'-'.strtotime('+'.get_option('bql_expire_time').' minutes');

                // Store the token and user ID in the database 
                update_user_meta($user->ID, 'quicklogin_token', $token);

                // get the edirect url after login
                $redirect_URL = NULL;
                if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false)
                    $redirect_URL = home_url();
                else
                    $redirect_URL = self::currentURL();

                // Send an email to the user with the login link
                $login_link = add_query_arg(array('ql_token' => $token, 'nonce' => wp_create_nonce('quick_login')), $redirect_URL);
                
                $subject = __('Your Log in Link', 'better-quick-login');
                $message = __('Click the following link to log in: ', 'better-quick-login'). $login_link;
                wp_mail($user->user_email, $subject, $message);

                self::$message = ['type'=>'notice', 'message'=>__('Please check your email. You will soon receive an email with a login link.', 'better-quick-login')];
            }
            else 
                self::$message = ['type'=>'error', 'message'=>__('There was a problem sending your email. Please try again or contact an admin.', 'better-quick-login')] ;

        }
    }


    /**
     * Handle auto-login when the token and nonce is present in the URL
     * 
     */
    function autoLogin() {

        if (is_user_logged_in())
            return;

        $nonce = sanitize_key( $_GET['nonce'] );
        
        //review the token is sent and the nonce is correct
        if ( isset($_GET['ql_token']) AND isset( $_GET['nonce'] ) 
            AND wp_verify_nonce( $nonce, 'quick_login' ) 
        ) {

            $token = sanitize_text_field( $_GET['ql_token'] );
            $expire_time =  explode('-', $token)[1];

            // control that the link has not expired
            if ( is_numeric($expire_time) AND $expire_time > time() ){

                // search the user
                $user  = get_users(array('meta_key' => 'quicklogin_token', 'meta_value' => $token, 'number' => 1));

                if (!empty($user)) {

                    // Log in the user
                    wp_set_current_user($user[0]->ID);
                    wp_set_auth_cookie($user[0]->ID, get_option('bql_keep_logged_in')==1 ? TRUE:FALSE); 
                    delete_user_meta($user[0]->ID, 'quicklogin_token');
                    wp_redirect(self::currentURL());
                    exit;
                }
                else
                    self::$message = ['type'=>'error', 'message'=>__('Token time expired please try to login again', 'better-quick-login' )];

            }
            else
                self::$message = ['type'=>'error', 'message'=>__('Token time expired please try to login again', 'better-quick-login' )];

        }

    }

    /**
     * Limit subscriber to have only ONE session at a time.
     * from https://sleeksoft.in/limit-only-one-session-per-user-wordpress/
     */
    public function forceOneSessionPerUser() {
        
        if ( get_option('bql_force_one_session') == 1 ) {
            //Get current user who is logged in
            $user = wp_get_current_user();
            
            //Check if user's role is subscriber
            //if( in_array('subscriber', $user->roles) ){
                //Get current user's session
                $sessions = WP_Session_Tokens::get_instance( $user->ID );

                //Get all his active wordpress sessions
                $all_sessions = $sessions->get_all();
            
                //If there is more than one session then destroy all other sessions except the current session.
                if ( count( $all_sessions ) > 1 ) {
                    $sessions->destroy_others( wp_get_session_token() );
                }
            //}
        };
    }



    /**
     * Function that initiates the plugin text domain
     *
     */
    public function addPluginTextDomain(){
        load_plugin_textdomain( 'better-quick-login', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    
    /**
     * Add the admin menu for plugin settings.
     */
    public function addAdminMenu(){
        // Add the admin menu for settings
        add_submenu_page(
            'options-general.php',
            __('Quick Login Settings', 'better-quick-login'),
            __('Quick Login', 'better-quick-login'),
            'manage_options',
            'bql-settings',
            [$this, 'renderSettingsPage']
        );
    }


    public function registerPluginSettings(){
        register_setting('bql-settings', 'bql_recaptcha_key', [
            'type' => 'string',
            'default' => '', 
            'sanitize_callback' => 'sanitize_text_field',
        ]);

        register_setting('bql-settings', 'bql_expire_time', [
            'type' => 'integer',
            'default' => 10,
            'sanitize_callback' => 'absint',
        ]);

        register_setting('bql-settings', 'bql_keep_logged_in', [
            'type' => 'integer',
            'default' => 1,
            'sanitize_callback' => 'absint',
        ]);

        register_setting('bql-settings', 'bql_force_one_session', [
            'type' => 'integer',
            'default' => 0,
            'sanitize_callback' => 'absint',
        ]);

        register_setting('bql-settings', 'bql_login_form', [
            'type' => 'integer',
            'default' => 0,
            'sanitize_callback' => 'absint',
        ]);
    }

    public function renderSettingsPage(){
        include(plugin_dir_path(self::MAIN_FILE) . 'templates/admin-settings.php');
    }


    /**
     * Add links to settings and sponsorship in plugin row meta.
     *
     * @param array $plugin_meta The existing plugin meta.
     * @param string $plugin_file The plugin file path.
     * @return array Modified plugin meta with added links.
     */
    public function addPluginRowMeta($plugin_meta, $plugin_file)
    {
        if (plugin_basename(self::MAIN_FILE) !== $plugin_file) {
            return $plugin_meta;
        }

        $settings_page_url = admin_url('options-general.php?page=bql-settings');

        $plugin_meta[] = sprintf(
            '<a href="%1$s"><span class="dashicons dashicons-admin-settings" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            $settings_page_url,
            esc_html_x('Settings', 'verb', 'better-quick-login')
        );

        $plugin_meta[] = sprintf(
            '<a href="%1$s"><span class="dashicons dashicons-star-filled" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            'https://paypal.me/chema/10EUR',
            esc_html_x('Sponsor', 'verb', 'better-quick-login')
        );

        return $plugin_meta;
    }

    /**
     * Display a donation message in the WordPress admin.
     */
    public function displayDonationMessage()
    {
        // Display the donation message
        if ((isset($_GET['page']) && $_GET['page'] === 'bql-settings') && !isset($_COOKIE['bql_donation_message_closed'])) {
            echo '<div id="donation-message" class="notice notice-info is-dismissible" style="background-color: #f5f5f5; border-left: 4px solid #0073aa; padding: 10px;">
                <p style="font-size: 16px;">';
            echo __('Enjoy using our plugin? Consider <a href="https://paypal.me/chema/10EUR" target="_blank" id="donate-link">making a donation</a> to support our work! THANKS!', 'better-quick-login');
            echo '</p></div>';
        }
    }

    /**
     * Add JavaScript for handling the donation message.
     */
    public function addDonationMessageJS()
    {
        // Add JavaScript for handling the donation message
        if (!isset($_COOKIE['bql_donation_message_closed'])) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {

                    $('#donate-link').click(function () {
                        $('#donation-message').remove();
                        var expirationDate = new Date();
                        expirationDate.setDate(expirationDate.getDate() + 30); // Expires in 30 days
                        document.cookie = 'bql_donation_message_closed=true; expires=' + expirationDate.toUTCString() + '; path=/';

                    });
                });
            </script>
            <?php
        }
    }

    /**
     * Set default options when the plugin is activated
     */
    public function setDefaultOptions()
    {
        $default_options = [
            'bql_recaptcha_key' => '',
            'bql_expire_time' => 10,
            'bql_keep_logged_in' => 1,
            'bql_force_one_session' => 0,
            'bql_login_form' => 0
        ];

        foreach ($default_options as $option_key => $option_value) {
            if (get_option($option_key) === false) {
                update_option($option_key, $option_value);
            }
        }
    }


    public static function currentURL() {
        if (isset($_SERVER['REQUEST_URI'])) {
            $req_uri = $_SERVER['REQUEST_URI'];

            $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
            $home_path_regex = sprintf('|^%s|i', preg_quote($home_path, '|'));

            // Trim path info from the end and the leading home path from the front.
            $req_uri = ltrim($req_uri, '/');
            $req_uri = preg_replace($home_path_regex, '', $req_uri);
            $req_uri = trim(home_url(), '/') . '/' . ltrim($req_uri, '/');

            return $req_uri;
        }

        return ''; // Return an empty string if REQUEST_URI is not set
    }


}

// Initialize the plugin
new BetterQuickLogin();



?>
