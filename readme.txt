=== Plugin Name ===
Contributors: jcow, ekaj
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jacobsnyder%40gmail%2ecom&lc=US&item_name=Jacob%20Snyder&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: log in, login, template login, themed login
Requires at least: 3.6.1
Tested up to: 4.4
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a log in page at /login/ and manages password recovery and user notification feedback for the log in process.

== Description ==

Creates a log in page at /login/ and manages password recovery and user notification feedback for the log in process. Everything gets managed within your page.php template or page-login.php template in order to fit into the theme better.

By default, this plugin creates a virtual page, but if you add a page with slug 'login', the plugin will begin to use that.

= Add a redirect for logged in users =

Controls where logged in users go when they login or when they visit the '/login/' page. You can either return the post_id of the post/page to send them to, or the slug of the post/page to send them to.

`
// Redirect using post id
add_filter( 'sewn/login/logged_in_redirect', 'custom_sewn_logged_in_redirect_id' );
function custom_sewn_logged_in_redirect_id()
{
	return 4;
}
`

`
// Redirect using post slug
add_filter( 'sewn/login/logged_in_redirect', 'custom_sewn_logged_in_redirect_slug' );
function custom_sewn_logged_in_redirect_slug()
{
	return 'post-slug';
}
`

= Sewn In Notification Box Support =

If you install the <a href="https://wordpress.org/plugins/sewn-in-notifications/">Sewn In Notification Box</a>, this plugin will start using that. This is handy to keep all of your notifications in a centralized location.

== Installation ==

* Install plugin either via the WordPress.org plugin directory, or by uploading the files to your server.
* Activate the plugin via the Plugins admin page.


== Frequently Asked Questions ==

= None yet. =

== Screenshots ==

1. A log in form example
2. Password recovery form

== Changelog ==

= 1.1.4 - 2015-12-21 =

- Because login forms can be used on any page, and that is the goal of this plugin, we had to turn off the login url rewrite until the form action url gets fixed in next version of WordPress. This means urls will show up on the site for wp-login.php potentially, but users will still be using the /login page as the default.

= 1.1.3 - 2015-12-20 =

- In 4.4, WP changed how the log in url is added to the form action which conflicted with the plugin rewrite of login urls. Turned off the rewrite on the custom login page for now.

= 1.1.2 - 2015-08-25 =

- Updated redirect to check post id, then post slug, then just use the string outright.

= 1.1.1 - 2015-08-25 =

- Changed the default redirect to standard profile

= 1.1.0 - 2015-06-12 =

- Launched in the repo


== Upgrade Notice ==

= 1.1.3 =
This update is required to work with 4.4.

= 1.1.2 =
Updated redirect to check post id, then post slug, then just use the string outright.

= 1.1.1 =
The new version changes the default redirect for "/login/" page to the user's profile, this can be overridden using this filter: add_filter( 'sewn/login/logged_in_redirect', 'custom_sewn_logged_in_redirect_slug' ); Look at the plugin page for more info.

= 1.1.0 =
This is the first version in the Wordpress repository.
