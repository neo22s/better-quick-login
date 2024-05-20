<?php defined('ABSPATH') or die('Slow down cowboy');?>

<?php
    $current_user = wp_get_current_user();
    $authorPostsUrl = get_author_posts_url($current_user->ID);
    $logout_link = wp_logout_url(home_url());
?>

<p>
    <?php esc_html_e('You are currently logged in as', 'better-quick-login'); ?>
    <a href="<?php echo esc_html( $authorPostsUrl ); ?>" title="<?php echo esc_html( $current_user->display_name ); ?>">
    	<?php echo esc_html( $current_user->display_name ); ?>
    </a>
    -
    <a href="<?php echo esc_html( $logout_link) ; ?>" title="<?php esc_html_e('Log out of this account', 'better-quick-login'); ?>">
    	<?php esc_html_e('Log out', 'better-quick-login'); ?> &raquo;
    </a>
</p>