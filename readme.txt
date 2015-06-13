=== Plugin Name ===
Contributors: jcow, ekaj
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jacobsnyder%40gmail%2ecom&lc=US&item_name=Jacob%20Snyder&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted
Tags: log in, login, template login, themed login
Requires at least: 3.6.1
Tested up to: 4.2.2
Stable tag: 1.1.0
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
add_filter( 'sewn_login/logged_in_redirect', 'custom_sewn_logged_in_redirect_id' );
function custom_sewn_logged_in_redirect_id()
{
	return 4;
}
`

`
// Redirect using post slug
add_filter( 'sewn_login/logged_in_redirect', 'custom_sewn_logged_in_redirect_slug' );
function custom_sewn_logged_in_redirect_slug()
{
	return 'post-slug';
}
'

## Sewn In Notification Box Support

If you install the Sewn In Notification Box, this plugin will start using that. This is handy to keep all of your notifications in a centralized location.

== Installation ==

* Install plugin either via the WordPress.org plugin directory, or by uploading the files to your server.
* Activate the plugin via the Plugins admin page.


== Frequently Asked Questions ==

= None yet. =

== Screenshots ==

1. A log in form example

== Changelog ==

= 1.1.0 - 2015-06-12 =

- Launched in the repo


== Upgrade Notice ==

= 1.1.0 =
This is the first version in the Wordpress repository.
