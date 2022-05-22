<?php
/**
 * Main plugin class.
 *
 * @package WP_Plugin_Starter
 * @since   1.0.0
 */

namespace WP_Plugin_Starter;

defined('ABSPATH') || exit;
 use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use \Firebase\JWT\JWT;
use My_Custom_My_Account_Endpoint;


/**
 *
 *
 *
 * Main plugin class.
 *
 * @since 1.0.0
 */
class Plugin
{

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance;

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, esc_html__('Cloning is forbidden.', 'wp-plugin-starter'), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, esc_html__('Unserializing instances of this class is forbidden.', 'wp-plugin-starter'), '1.0.0');
	}

	/**
	 * Main plugin class instance.
	 *
	 * Ensures only one instance of the plugin is loaded or can be loaded.
	 *
	 * @return object Main instance of the class.
	 */
	final public static function instance()
	{
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Plugin Constructor.
	 */

	public function __construct()
	{


		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_filter('plugin_action_links_' . plugin_basename(WPS_PLUGIN_FILE), array($this, 'plugin_action_links'));
// 	     add_action('storefront_header',  array( $this, 'test123' ), 41);
		add_action('wp_loaded', array($this, 'createNewUserAngLogginOrChangeLogginIfExists'), 41);

	}
	function misha_custom_button_html( $button_html ) {
		$button_html = str_replace( 'Place order1', 'Submit', $button_html );
		return $button_html;
	}

	public function changeTextofPlaceOrder() {


	}




	public function loginExternalUsr($email)
	{

		$user = get_user_by('email', $email);

		if (isset($user)) {
			$remember = true;
			wp_set_auth_cookie($user->ID, $remember);
			header('Refresh: 0');
		} else {

		}
	}

	public function decodeKeycloakJWT($usertoken_to_verify)
	{


			if (!isset($usertoken_to_verify)) {

// echo  "empty usertoken_to_verify HTTP_X_WP_AUTHORIZATION";
			} else {


				$arrContextOptions = array(
						"ssl" => array(
								"verify_peer" => false,
								"verify_peer_name" => false,
						),
				);

				$auth_keys = file_get_contents('https://keycloak.cloudsdapp.com/auth/realms/sudancoin/protocol/openid-connect/certs', false, stream_context_create($arrContextOptions));
				$auth_keys_json = json_decode($auth_keys);


				$resooo = json_encode($auth_keys_json);

				$jwks = JWK::parseKeySet(json_decode($resooo, true));
				$public_keys = JWK::parseKeySet(json_decode($resooo, true));
				$keys = array_keys($public_keys);

				$jwks1 = ['keys' => $jwks];


				$decoded = JWT::decode($usertoken_to_verify, $public_keys[$keys[0]], ['RS256']);


				return $decoded;
}
	}


	public function createNewUserAngLogginOrChangeLogginIfExists(): void
	{
		try {
			if (isset($_SERVER['HTTP_X_WP_AUTHORIZATION'])) {
				$usertoken_to_verify = $_SERVER['HTTP_X_WP_AUTHORIZATION'];
				$decodedJwt = $this::decodeKeycloakJWT($usertoken_to_verify);

				$jwtUser = get_user_by('email', $decodedJwt->email);


				if (!$jwtUser || !$jwtUser->exists()) {

					// jwt user doesn't exists need to be created;
					$this::createUser($decodedJwt->email);
					$this::loginExternalUsr($decodedJwt->email);
				} else {
					// jwt user exists need just to login but checck if   any other user logged from this browser
					$current_user = wp_get_current_user();
					if (is_user_logged_in() && $current_user && $current_user->exists()) {
						$current_user_email = $current_user->user_email;
						if ($current_user_email !== $decodedJwt->email) {
							$this::loginExternalUsr($decodedJwt->email);
						} // ignore current logged in user is same as user  in jwt, ignore me

					} else { //  no user logged in, and jwt user exists just login
						$this::loginExternalUsr($decodedJwt->email);
					}
				}


			} else {

			}
		} catch (\Exception $e) {
 		}
	}

	public function createUser($user_email)
	{

		$user_id = username_exists($user_email);

 		if (!$user_id && !email_exists($user_email)) {
 			$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
			$user_id = wp_create_user($user_email, $random_password, $user_email);
			wp_update_user([
					'ID' => $user_id, // this is the ID of the user you want to update.
					'first_name' => $user_email,
					'last_name' => $user_email,
			]);
		} else {

		}

	}

	/**
	 * Registers settings page under Settings.
	 */
	public function admin_menu()
	{
		add_options_page(esc_html__('WP Plugin Starter settings', 'wp-plugin-starter'), esc_html__('Settings', 'wp-plugin-starter'), 'manage_options', 'wp-plugin-starter-settings', array($this, 'page_wrapper'));
	}

	/**
	 * Set up a div for the app to render into.
	 */
	public function page_wrapper()
	{
		?>
		<div class="wp-plugin-starter-settings">
		<h1 class="screen-reader-text hide-if-no-js"><?php echo esc_html_e('WP Plugin Starter Settings', 'wp-plugin-starter'); ?></h1>
		<div id="root" class="wp-plugin-starter-settings__container hide-if-no-js"></div>

		<?php // JavaScript is disabled. ?>
		<div class="wrap hide-if-js wp-plugin-starter-settings-no-js">
			<h1 class="wp-heading-inline"><?php echo esc_html_e('WP Plugin Starter Settings', 'wp-plugin-starter'); ?></h1>
			<div class="notice notice-error notice-alt">
				<p>
					<?php
					$message = esc_html__('The WP Plugin Starter Settings requires JavaScript. Please enable JavaScript in your browser settings.', 'wp-plugin-starter');

					/**
					 * Filters the message displayed in the setting interface when JavaScript is
					 * not enabled in the browser.
					 *
					 * @param string $message The message being displayed.
					 * @since 1.0.0
					 *
					 */
					echo wp_kses_post(apply_filters('plugin_starter_settings_no_javascript_message', $message));
					?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts()
	{
		$screen = get_current_screen();
		$asset_file = include plugin_dir_path(WPS_PLUGIN_FILE) . 'build/index.asset.php';

		// Register scripts.
		wp_register_script(
				'wp-plugin-starter-settings',
				plugins_url('build/index.js', WPS_PLUGIN_FILE),
				$asset_file['dependencies'],
				$asset_file['version'],
				true
		);

		// Register styles.
		wp_register_style(
				'wp-plugin-starter-settings',
				plugins_url('build/index.css', WPS_PLUGIN_FILE),
				array(),
				filemtime(plugin_dir_path(WPS_PLUGIN_FILE) . 'build/index.css')
		);

		// Add RTL support for admin styles.
		wp_style_add_data('wp-plugin-starter-settings', 'rtl', 'replace');

		if (
				isset($screen->id)
				&& 'settings_page_wp-plugin-starter-settings' === $screen->id
		) {
			wp_enqueue_style('wp-plugin-starter-settings');
			wp_enqueue_script('wp-plugin-starter-settings');
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
	public function plugin_action_links($links)
	{
		$action_links = array(
				'settings' => '<a href="' . admin_url('options-general.php?page=wp-plugin-starter-settings') . '" aria-label="' . esc_attr__('View Plugin settings', 'wp-plugin-starter') . '">' . esc_html__('Settings', 'wp-plugin-starter') . '</a>',
		);

		return array_merge($action_links, $links);
	}


}
