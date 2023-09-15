<?php
// If uninstall is not called from WordPress, exit.
defined('WP_UNINSTALL_PLUGIN') or die('Slow down cowboy');

// Delete plugin settings here
delete_option( 'bql_recaptcha_key' );
delete_option( 'bql_expire_time' );
delete_option( 'bql_keep_logged_in' );
delete_option( 'bql_force_one_session' );
delete_option( 'bql_login_form' );