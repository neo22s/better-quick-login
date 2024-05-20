<?php defined('ABSPATH') or die('Slow down cowboy');?>

<?php if ( isset( BQLC_BetterQuickLogin::$message ) ) :?>
    <div class="quick-login-<?php echo esc_html(BQLC_BetterQuickLogin::$message['type']); ?>">
        <?php echo esc_html(BQLC_BetterQuickLogin::$message['message']); ?>
    </div>
    <?php 
endif;
?>