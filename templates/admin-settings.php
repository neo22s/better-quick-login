<?php defined('ABSPATH') or die('Slow down cowboy');?>
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <form method="post" action="options.php">
        <?php settings_fields('bql-settings'); ?>
        <?php do_settings_sections('bql-settings'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><a target="_blank" href="https://developers.google.com/recaptcha"><?php esc_html_e('Recaptcha V3 Site Key', 'better-quick-login'); ?></a></th>
                <td><input type="text" name="bql_recaptcha_key" value="<?php echo esc_attr(get_option('bql_recaptcha_key')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Link Expire Time', 'better-quick-login'); ?></th>
                <td>
                    <select name="bql_expire_time">
                        <option value="10" <?php selected(get_option('bql_expire_time'), 10); ?>>10 <?php esc_html_e('minutes', 'better-quick-login') ?></option>
                        <option value="30" <?php selected(get_option('bql_expire_time'), 30); ?>>30 <?php esc_html_e('minutes', 'better-quick-login') ?></option>
                        <option value="60" <?php selected(get_option('bql_expire_time'), 60); ?>>1 <?php esc_html_e('hour', 'better-quick-login') ?></option>
                        <option value="720" <?php selected(get_option('bql_expire_time'), 720); ?>>12 <?php esc_html_e('hours', 'better-quick-login') ?></option>
                        <option value="1440" <?php selected(get_option('bql_expire_time'), 1440); ?>>1 <?php esc_html_e('day', 'better-quick-login') ?></option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Keep User Logged In', 'better-quick-login'); ?></th>
                <td><input type="checkbox" name="bql_keep_logged_in" value="1" <?php checked(get_option('bql_keep_logged_in'), '1'); ?> /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Force 1 Session per User', 'better-quick-login'); ?></th>
                <td><input type="checkbox" name="bql_force_one_session" value="1" <?php checked(get_option('bql_force_one_session'), '1'); ?> /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php esc_html_e('Add to WordPress Login', 'better-quick-login'); ?></th>
                <td><input type="checkbox" name="bql_login_form" value="1" <?php checked(get_option('bql_login_form'), '1'); ?> /></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>