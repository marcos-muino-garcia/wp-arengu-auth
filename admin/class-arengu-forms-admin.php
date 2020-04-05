<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Arengu_Forms_Admin {


	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	function register_tinymce_buttons( $buttons ) {	

		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugin') );
		add_filter( 'mce_buttons', array( $this, 'add_tinymce_buttons' ) );

	}

	function register_arengu_settings () {
		$random_password = wp_generate_password();
		$new_api_key = base64_encode(wp_hash_password($random_password));

		add_menu_page('Arengu Settings', 'Arengu', 'manage_options', 'arengu-settings', array($this, 'arengu_settings_page'), plugins_url('img/arengu-logo-icon.svg', __FILE__));
		register_setting('arengu_endpoint_settings', 'signup_endpoint');
		register_setting('arengu_endpoint_settings', 'login_endpoint');
		register_setting('arengu_endpoint_settings', 'passwordless_endpoint');
		register_setting('arengu_key_settings', 'private_key', array(
			'default' => 'Bearer ' . wp_hash_password($new_api_key),
		));
	}

	public function register_tinymce_plugin($plugin_array) {

		$plugin_array['arengu_forms_button'] =  plugins_url( 'js/arengu-forms-tinymce.js', __FILE__ );
		return $plugin_array;

	}

	function arengu_settings_page() {
		$random_password = wp_generate_password();
		$new_api_key = base64_encode(wp_hash_password($random_password));
		?>
		<div class="wrap">
			<h1>Arengu Settings</h1>
			<p>Enable or disable custom endpoints to use with Arengu Forms.</p>
			<form class="form-table" method="post" action="options.php">
				<?php settings_fields('arengu_endpoint_settings'); ?>
				<?php do_settings_sections('arengu_endpoint_settings'); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="arengu-signup-endpoint">
								<input id="arengu-signup-endpoint" name="signup_endpoint" type="checkbox" value="1" <?php checked( '1', get_option('signup_endpoint'), true ); ?> /> Signup endpoint
							</label>
						</th>
						<td>
							<code>
								<?php echo get_rest_url() . 'arengu/signup'; ?>
							</code>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="arengu-login-endpoint">
								<input id="arengu-login-endpoint" name="login_endpoint" type="checkbox" value="1" <?php checked( '1', get_option('login_endpoint'), true ); ?> /> Login endpoint
							</label>
						</th>
						<td>
							<code>
								<?php echo get_rest_url() . 'arengu/auth'; ?>
							</code>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="arengu-passwordless-endpoint">
								<input id="arengu-passwordless-endpoint" name="passwordless_endpoint" type="checkbox" value="1" <?php checked( '1', get_option('passwordless_endpoint'), true ); ?> /> Passwordless endpoint
							</label>
						</th>
						<td>
							<code>
								<?php echo get_rest_url() . 'arengu/passwordless/auth'; ?>
							</code>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<h2>Private API Key</h2>
			<p>Use this private API key as Authorization header value to make HTTP requests to custom endpoints. <b>Do not share your key with anyone!</b></p>
			<form class="form-table" method="post" action="options.php">
				<?php settings_fields('arengu_key_settings'); ?>
				<?php do_settings_sections('arengu_key_settings'); ?>
				<code>
					<?php echo get_option('private_key') ?>
				</code>
				<input name="private_key" type="hidden" value="<?php echo 'Bearer ' . $new_api_key ?>">
				<?php submit_button('Regenerate API key'); ?>
			</form>
		</div>
		<?php
	}

	function add_tinymce_buttons ( $buttons ) {

		$buttons[] = 'arengu_forms_button';
		return $buttons;

	}

}
