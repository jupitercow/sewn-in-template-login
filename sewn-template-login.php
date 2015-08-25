<?php

/**
 * @link              https://github.com/jupitercow/sewn-in-template-login
 * @since             1.1.0
 * @package           Sewn_Login
 *
 * @wordpress-plugin
 * Plugin Name:       Sewn In Template Log In
 * Plugin URI:        https://wordpress.org/plugins/sewn-in-template-login/
 * Description:       Add log in form to a page template. Moves everything to a page template.
 * Version:           1.1.1
 * Author:            Jupitercow
 * Author URI:        http://Jupitercow.com/
 * Contributor:       Jake Snyder
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sewn_login
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$class_name = 'Sewn_Login';
if (! class_exists($class_name) ) :

class Sewn_Login
{
	/**
	 * The unique prefix for Sewn In.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $prefix         The string used to uniquely prefix for Sewn In.
	 */
	protected $prefix;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $settings       The array used for settings.
	 */
	protected $settings;

	/**
	 * Load the plugin.
	 *
	 * @since	1.1.0
	 * @return	void
	 */
	public function run()
	{
		$this->settings();

		register_activation_hook( __FILE__,   array($this, 'activation') );
		register_deactivation_hook( __FILE__, array($this, 'deactivation') );

		add_action( 'init',                   array($this, 'init') );
	}

	/**
	 * Class settings
	 *
	 * @author  Jake Snyder
	 * @since	1.1.0
	 * @return	void
	 */
	public function settings()
	{
		$this->prefix      = 'sewn';
		$this->plugin_name = strtolower(__CLASS__);
		$this->version     = '1.1.1';
		$this->settings    = array(
			'loaded'   => false,
			'pages'    => array(
				'login' => array(
					'page_name'  => 'login',
					'page_title' => 'Log In'
				)
			),
			'strings' => array(
				'lost_password_title'        => __( "Lose something?", $this->plugin_name ),
				'user_login_label'           => __( "Username or Email", $this->plugin_name ),
				'lost_password_submit'       => __( "Get New Password", $this->plugin_name ),
				'recover_link_text'          => __( "Lost Password?", $this->plugin_name ),
				'recover_link_attr_title'    => __( "Recover Lost Password", $this->plugin_name ),
				'notification_failed'        => __( "There was a problem with your username or password.", $this->plugin_name ),
				'notification_loggedout'     => __( "You are now logged out.", $this->plugin_name ),
				'notification_recovered'     => __( "Check your e-mail for the confirmation link.", $this->plugin_name ),
				'notification_password'      => __( "Please enter your username or email. You will receive a link to create a new password via email.", $this->plugin_name ),
				'notification_passworderror' => __( "<strong>ERROR</strong>: Enter a username or e-mail address.", $this->plugin_name ),
			),
		);
		$this->settings['messages'] = array(
			'failed' => array(
				'key'     => 'action',
				'value'   => 'failed',
				'message' => $this->settings['strings']['notification_failed'],
				'args'    => 'error=true&page='.$this->settings['pages']['login']['page_name'],
			),
			'loggedout' => array(
				'key'     => 'action',
				'value'   => 'loggedout',
				'message' => $this->settings['strings']['notification_loggedout'],
				'args'    => 'page='.$this->settings['pages']['login']['page_name'],
			),
			'recovered' => array(
				'key'     => 'action',
				'value'   => 'recovered',
				'message' => $this->settings['strings']['notification_recovered'],
				'args'    => 'page='.$this->settings['pages']['login']['page_name'],
			),
			'password' => array(
				'key'     => 'action',
				'value'   => 'password',
				'message' => $this->settings['strings']['notification_password'],
				'args'    => 'page='.$this->settings['pages']['login']['page_name'],
			),
			'passworderror' => array(
				'key'     => 'action',
				'value'   => 'passworderror',
				'message' => $this->settings['strings']['notification_passworderror'],
				'args'    => 'error=true&page='.$this->settings['pages']['login']['page_name'],
			),
		);
		$this->settings['login_url'] = home_url( '/' . $this->settings['pages']['login']['page_name'] . '/' );
	}

	/**
	 * Activation of plugin
	 *
	 * @author  Jake Snyder
	 * @since	1.0.2
	 * @return	void
	 */
	public function activation()
	{
		$this->rewrites();
		flush_rewrite_rules();
	}

	/**
	 * Deactivation of plugin
	 *
	 * @author  Jake Snyder
	 * @since	1.0.2
	 * @return	void
	 */
	public function deactivation()
	{
		flush_rewrite_rules();
	}

	/**
	 * On plugins_loaded test if we can use sewn_notifications
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function plugins_loaded()
	{
		// Have the login plugin use frontend notifictions plugin
		if ( apply_filters( "{$this->prefix}/login/use_sewn_notifications", true ) )
		{
			if ( class_exists('Sewn_Notifications') ) {
				add_filter( "{$this->prefix}/notifications/queries", array($this, 'add_notifications') );
			} else {
				add_filter( "{$this->prefix}/login/use_sewn_notifications", '__return_false' );
			}
		}
	}

	/**
	 * Initialize the Class
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function init()
	{
		$this->settings = apply_filters( "{$this->prefix}/login/settings", $this->settings );

		$this->plugins_loaded();

		add_action( "{$this->prefix}/login/the_form",   array($this, 'the_form'), 10, 2 );
		add_filter( "{$this->prefix}/login/get_form",   array($this, 'get_form'), 10 );

		// Set up custom log in page, It seems like something changed and login_head is happening later, either way shake_error_codes happens at a nice point...
		#add_action( 'login_head',                       array($this, 'redirect_wp_login'), 1 );
		add_filter( 'shake_error_codes',                array($this, 'redirect_wp_login') );

		add_filter( 'login_url',                        array($this, 'new_login_url'), 10, 2 );
		add_action( 'wp_login_failed',                  array($this, 'login_failed'), 10, 2 );
		add_filter( 'lostpassword_url',                 array($this, 'lostpassword_url'), 10, 2 );

		// Add a fake post for profile and register pages if they don't already exist
		add_filter( 'the_posts',                        array($this, 'add_post') );

		// better support for the login page
		$this->rewrites();

		// Allow emails for log in
		add_filter( 'authenticate',                     array($this, 'allow_email_login'), 20, 3);

		// Add form to Login Page
		add_filter( 'the_content',                      array($this, 'add_form_to_page') );

		// If user is logged in, redirect the login page to homepage or filter specified
		add_filter( 'wp',                               array($this, 'redirect_logged_in') );
	}

	/**
	 * Add a rewrite for /login/
	 *
	 * @author  Jake Snyder
	 * @since	1.0.2
	 * @return	void
	 */
	public function rewrites()
	{
		// Support the "add" on archive pages to switch to the form
		add_rewrite_rule(
			"login/?$",
			'index.php?pagename=login',
			'top'
		);
	}

	/**
	 * See if not register post exists and add it dynamically if not
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	object $posts Modified $posts with the new register post
	 */
	public function add_post( $posts )
	{
		global $wp, $wp_query;

		// Check if the requested page matches our target, and no posts have been retrieved
		if ( (! $posts || 1 < count($posts)) && array_key_exists(strtolower($wp->request), $this->settings['pages']) )
		{
			// Add the fake post
			$posts   = array();
			$posts[] = $this->create_post( strtolower($wp->request) );

			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset($wp_query->query["error"]);
			$wp_query->query_vars["error"]="";
			$wp_query->is_404=false;
		}
		return $posts;
	}

	/**
	 * Create a dynamic post on-the-fly for the register page.
	 *
	 * source: http://scott.sherrillmix.com/blog/blogger/creating-a-better-fake-post-with-a-wordpress-plugin/
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	object $post Dynamically created post
	 */
	public function create_post( $type )
	{
		// Create a fake post.
		$post = new stdClass();
		$post->ID                    = 1;
		$post->post_author           = 1;
		$post->post_date             = current_time('mysql');
		$post->post_date_gmt         = current_time('mysql', 1);
		$post->post_content          = '';
		$post->post_title            = $this->settings['pages'][$type]['page_title'];
		$post->post_excerpt          = '';
		$post->post_status           = 'publish';
		$post->comment_status        = 'closed';
		$post->ping_status           = 'closed';
		$post->post_password         = '';
		$post->post_name             = $this->settings['pages'][$type]['page_name'];
		$post->to_ping               = '';
		$post->pinged                = '';
		$post->post_modified         = current_time('mysql');
		$post->post_modified_gmt     = current_time('mysql', 1);
		$post->post_content_filtered = '';
		$post->post_parent           = 0;
		$post->guid                  = home_url('/' . $this->settings['pages'][$type]['page_name'] . '/');
		$post->menu_order            = 0;
		$post->post_type             = 'page';
		$post->post_mime_type        = '';
		$post->comment_count         = 0;
		$post->filter                = 'raw';
		return $post;   
	}

	/**
	 * Redirect logged in users
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function redirect_logged_in( $queries )
	{
		if ( is_page($this->settings['pages']['login']['page_name']) && is_user_logged_in() )
		{
			$redirect = get_edit_user_link();
			if ( $logged_in_redirect = apply_filters( "{$this->prefix}/login/logged_in_redirect", false ) )
			{
				if ( is_numeric($logged_in_redirect) ) {
					$redirect = get_permalink($logged_in_redirect);
				} else {
					$page = get_page_by_path($logged_in_redirect);
					if ( is_object($page) ) {
						$redirect = get_permalink( $page->ID );
					}
				}
			}

			if ( $redirect ) {
				wp_redirect( $redirect );
				die;
			}
		}
	}

	/**
	 * Adds notifications to the login page
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	array $queries Messages array
	 */
	public function add_notifications( $queries )
	{
		$queries = array_merge($queries, $this->settings['messages']);
		return $queries;
	}

	/**
	 * Adds a form to the login page, this can be turned off using the filter: 'sewn/login/add_form'
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	string $content The post content for login page with the login form a
	 */
	public function add_form_to_page( $content )
	{
		if ( is_page($this->settings['pages']['login']['page_name']) && is_main_query() && in_the_loop() && apply_filters( "{$this->prefix}/login/add_form", true ) ) {
			$content = apply_filters( "{$this->prefix}/login/get_form", array(), $content );
		}
		return $content;
	}

	/**
	 * Create our log in form, can be accessed using the action: 'sewn/login/the_form'
	 *
	 * @author  Jake Snyder
	 * @since	1.0.3
	 * @return	string $args The arguments for wp_login_form()
	 * @return	string $content The post content for login page with the login form a
	 */
	public function the_form( $args=false, $content='' )
	{
		echo apply_filters( "{$this->prefix}/login/get_form", $args, $content );
	}

	/**
	 * Create our log in form, can be accessed using the filter: 'sewn/login/get_form'
	 *
	 * @author  Jake Snyder
	 * @since	1.0.3
	 * @return	string $args The arguments for wp_login_form()
	 * @return	string $content The post content for login page with the login form a
	 */
	public function get_form( $args, $content='' )
	{
		$messages           = '';
		$password_form      = false;
		$show_password_form = array('password','passworderror');
		if (! $args ) { $args = array(); }

		if (! empty($_REQUEST['action']) )
		{
			$action = $_REQUEST['action'];
			if (! apply_filters( "{$this->prefix}/login/use_sewn_notifications", true ) && apply_filters( "{$this->prefix}/login/show_messages", true ) ) {
				$messages = (! empty($this->settings['messages'][$action]['message']) ) ? $this->settings['messages'][$action]['message'] : '';
			}

			if ( in_array(trim($action), $show_password_form) ) {
				$password_form = true;
			}
		}

		ob_start();
		if (! empty($password_form) ) : ?>

			<article id="content_page" <?php post_class('clearfix'); ?> role="article">
				<header class="article-header">
					<h2><?php echo $this->settings['strings']['lost_password_title']; ?></h2>
				</header>
				<section class="entry-content clearfix" itemprop="articleBody">
					<div class="loginform-container">
						<form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post'); ?>" method="post">
							<p class="login-username">
								<label for="user_login"><?php echo $this->settings['strings']['user_login_label']; ?></label>
								<input type="text" name="user_login" id="user_login" class="input" value="" size="20" />
							</p>
							<p class="password-submit">
								<?php do_action( 'login_form', 'resetpass' ); ?>
								<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php echo $this->settings['strings']['lost_password_submit']; ?>">
								<input type="hidden" name="redirect_to" value="<?php echo add_query_arg('action', 'recovered'); ?>" />
							</p>
						</form>
					</div>
				</section>
			</article>

		<?php else :

			if ( empty($args['label_username']) ) {
				$args['label_username'] = $this->settings['strings']['user_login_label'];
			}

			// If an email or username has been sent, fill it in
			if (! empty($_REQUEST['username']) ) {
				$args['value_username'] = esc_attr( $_REQUEST['username'] );
			} elseif (! empty($_REQUEST['email']) ) {
				$args['value_username'] = esc_attr( $_REQUEST['email'] );
			}

			// Set up args for form
			$args = array_merge( $args, apply_filters( "{$this->prefix}/login/form_args", array(), $args ) );

			// Show lost password link
			if ( apply_filters( "{$this->prefix}/login/show_lost_password", true ) ) {
				add_filter( 'login_form_bottom', array($this, 'password_link') );
			}

			// Create the form
			echo '<div class="loginform-container">';
				wp_login_form( $args );
			echo '</div>';

		endif;

		return $messages . $content . ob_get_clean();
	}

	/**
	 * Add password recovery link
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	string The html for the password recovery link
	 */
	public function password_link()
	{
		$output = '';

		ob_start(); ?>
		<p class="password-recover">
			<a href="<?php echo wp_lostpassword_url( add_query_arg('action', 'recovered', $this->new_login_url()) ); ?>" title="<?php echo $this->settings['strings']['recover_link_attr_title']; ?>">
				<?php echo apply_filters( "{$this->prefix}/login/label_lost_password", $this->settings['strings']['recover_link_text'] ); ?>
			</a>
		</p>
		<?php $output = ob_get_clean();

		return $output;
	}

	/**
	 * Redirect wp_login.php to the new login page
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function redirect_wp_login()
	{
		if ( 'wp-login.php' == $GLOBALS['pagenow'] )
		{
			$redirect_url = $this->new_login_url();

			if (! empty($_REQUEST['action']) )
			{
				if ( 'rp' == $_REQUEST['action'] || 'resetpass' == $_REQUEST['action'] ) {
					return;
				} elseif ( 'lostpassword' == $_REQUEST['action'] ) {
					$redirect_url = add_query_arg( 'action', 'passworderror', $this->new_login_url() );
				} elseif ( 'register' == $_REQUEST['action'] ) {
					$page = get_page_by_path('register');
					if ( $page ) $redirect_url = get_permalink($page->ID);
				}
			}
			elseif (! empty($_REQUEST['loggedout'])  )
			{
				$redirect_url = add_query_arg('action', 'loggedout', $this->new_login_url());
			}

			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Redirect lost password
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	string New lost password url
	 */
	public function lostpassword_url( $lostpassword_url, $redirect )
	{
		return add_query_arg( 'action', 'password', $this->new_login_url( wp_login_url($redirect), $redirect ) );
	}

	/**
	 * New Login URL
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function new_login_url( $login_url='', $redirect='' )
	{
		$login_url = $this->settings['login_url'];
		if (! empty($redirect) ) {
			$login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
		}

		return $login_url;
	}

	/**
	 * Redirect login failed
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function login_failed( $username )
	{
		$referrer = wp_get_referer();

		if ( $referrer && ! strstr($referrer, 'wp-login') && ! strstr($referrer, 'wp-admin') )
		{
			wp_redirect( add_query_arg('action', 'failed', $this->new_login_url()) );
			die;
		}
	}

	/**
	 * Allow email address log in instead of username
	 *
	 * @author  Jake Snyder
	 * @since	1.0.0
	 * @return	void
	 */
	public function allow_email_login( $user, $username, $password ) {
		if ( is_email($username) )
		{
			$user = get_user_by( 'email', $username );
			if ( $user ) {
				$username = $user->user_login;
			}
		}
		return wp_authenticate_username_password( null, $username, $password );
	}
}

$$class_name = new $class_name;
$$class_name->run();
unset($class_name);

endif;