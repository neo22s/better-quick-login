<?php defined('ABSPATH') or die('Slow down cowboy');?>
<?php if ( get_option('bql_force_one_session') == 1 ):?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<script>
function onSubmit(token) {
        document.getElementById("quick_login_form").submit();
    }
</script>
<?php endif;?>

<div id="login" class="quick-login-form">
<form method="post" id="quick_login_form" action="">
    <input class="input" type="text" id="ql_username_email" name="ql_username_email" placeholder="<?php echo __('Username or Email', 'better-quick-login'); ?>" required>
    <?php wp_nonce_field( 'quick_login_request', 'nonce', false ) ?>
    <button 
        class="g-recaptcha button button-primary button-large"
        <?php if ( get_option('bql_force_one_session') == 1 ):?>
            data-sitekey="<?php echo esc_attr(get_option('bql_recaptcha_key')); ?>" 
            data-callback='onSubmit'  
            data-action='submit'
        <?php endif;?>
        name="bql-submit" id="bql-submit"/>
        <?php echo __('Login', 'better-quick-login'); ?>
    </button>
</form>
</div>