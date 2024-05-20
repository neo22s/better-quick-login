<?php
defined('ABSPATH') or die('Slow down cowboy');

class BQLC_BetterQuickLoginWidget extends WP_Widget {
    // Widget constructor
    public function __construct() {
        parent::__construct(
            'quick_login_widget',
            __('Quick Login', 'better-quick-login'),
            array(
                'description' => __('Quick login widget.', 'better-quick-login'),
            )
        );
    }

    // Widget output
    public function widget($args, $instance) {
        // Display your custom login form here
        $template =  (is_user_logged_in()) ? 'already-logged.php':'login-form.php';
        include(plugin_dir_path(__FILE__) . '../templates/'.$template);
    }
}

?>