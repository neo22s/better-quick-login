<?php defined('ABSPATH') or die('Slow down cowboy');?>

<?php
    $current_user = wp_get_current_user();
    $authorPostsUrl = get_author_posts_url($current_user->ID);
    $logout_link = wp_logout_url(home_url());
?>

<p>
    <?php echo __('You are currently logged in as', 'better-quick-login'); ?>
    <a href="<?php echo $authorPostsUrl; ?>" title="<?php echo $current_user->display_name; ?>">
    	<?php echo $current_user->display_name; ?>
    </a>
    -
    <a href="<?php echo $logout_link; ?>" title="<?php echo __('Log out of this account', 'better-quick-login'); ?>">
    	<?php echo __('Log out', 'better-quick-login'); ?> &raquo;
    </a>
</p>