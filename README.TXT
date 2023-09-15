=== Better Quick Login ===
Contributors: deambulando
Donate link: https://paypal.me/chema/10EUR
Tags: passwordless, login, quick login, authentication, user management, reCAPTCHA, widget, auth
Requires at least: 6.0
Tested up to: 6.3
Stable tag: 1.0
Requires PHP: 8
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Passwordless login system for WordPress.

== Description ==

The Quick Login plugin provides a convenient way for users to log in to your WordPress site without using a password. It allows users to request a login link via email, eliminating the need for a username and password. This plugin is especially useful for sites where user convenience and security are top priorities.

== Features ==

- Simplified login process, only input user name or email. No Password needed.
- Option to force one session per user (limit subscribers to one active session at a time)
- Customizable login form (can be enabled/disabled)
- Auto-login via unique tokens and email links
- reCAPTCHA support for enhanced security
- Widget option for adding the login form to your site's sidebar

== Installation ==

1. Upload the 'quick-login' folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Usage ==

To add the Quick Login form to your content, you can use the provided shortcode: `[quicklogin]`. You can also use the custom block named "Quick Login Block" in the Gutenberg editor.

To customize plugin settings, navigate to 'Quick Login' under the 'Settings' menu in the WordPress dashboard. You can also enable reCAPTCHA for added security.

To add the Quick Login form to your site's sidebar, you can use the provided widget named "Quick Login Widget."

== Screenshots ==

1. Quick Login Form
2. Plugin Settings Page

== Frequently Asked Questions ==

= How can I add the Quick Login form to my content? =

You can use the provided shortcode `[quicklogin]` ,Widget or use the custom block named "Quick Login Block" in the Gutenberg editor.

= Can I customize the appearance of the login form? =

Yes, you can customize the login form by modifying the template file `login-form.php` located in the `templates` folder.

= How do I limit subscribers to one session at a time? =

In the plugin settings, enable the option "Force One Session Per User." This will ensure that subscribers can only have one active session at any given time.

= How can I enable reCAPTCHA for added security? =

In the plugin settings, you can enter your reCAPTCHA key. This will enable reCAPTCHA on the login form to enhance security.

= How can I add the Quick Login form to my site's sidebar? =

You can use the provided widget named "Quick Login Widget" to add the login form to your site's sidebar.

== Changelog ==

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
Initial release.

== Support ==

For support or feature requests, please visit the [support forum](https://wordpress.org/support/plugin/quick-login).

== Donations ==

If you find this plugin helpful, consider [making a donation](https://paypal.me/chema/10EUR) to support our work. Thank you!

== Credits ==

The Quick Login plugin is developed by [Chema].

== License ==

This plugin is licensed under the GPL2 license. See [License details](https://www.gnu.org/licenses/gpl-2.0.html).
