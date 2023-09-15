<?php defined('ABSPATH') or die('Slow down cowboy');?>

<?php if (isset(BetterQuickLogin::$message)) :?>
    <div class="quick-login-<?php echo BetterQuickLogin::$message['type']; ?>">
        <?php echo BetterQuickLogin::$message['message']; ?>
    </div>
    <?php 
endif;
?>