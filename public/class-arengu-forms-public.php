<?php

const ARENGU_SDK_URL = 'https://sdk.arengu.com/forms.js';

/**
 * The public-facing functionality of the plugin.
 */
class Arengu_Forms_Public {

	/**
	 * Add async attribute to script tags
	 */
	public function add_async_attribute($tag, $handle) {
		$scripts = array('arengu-forms-sdk');

		foreach($scripts as $script) {
			 if ($script !== $handle) return $tag;
			 return str_replace('src', 'async src', $tag);
		}

		return $tag;
	}

	/**
	 * Register public shortcodes
	 */
	public function register_shortcodes() {
		add_shortcode('arengu-form', array($this, 'arengu_forms_shortcode'));
	}

	/**
	 * @param $id
	 */
	public function arengu_forms_shortcode($params) {
		if ($params && $params['id']) {
			return "<div data-arengu-form-id='". $params['id'] ."'></div>";
		}

		return null;
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script('arengu-forms-sdk', esc_url(ARENGU_SDK_URL));
	}

	/**
	 * Helper function to parse auth cookie
	 * @param $user_id - String - User ID.
	 * @param $remember - Boolean - The default the cookie is kept without remembering is two days. When $remember is set, the cookies will be kept for 14 days.
	 * @param $secure - Boolean - Whether the auth cookie should only be sent over HTTPS.
	 */
	function generate_auth_cookie ($user_id, $remember = false, $secure = true) {
		wp_set_auth_cookie($user_id, $remember, $secure);
		$user = get_userdata($user_id);

		$response = new stdClass();
		$response->user->id = $user->data->ID;
		$response->user->email = $user->data->user_email;

		return $response;
	}

	public function custom_add_user_meta ($meta, $user_id) {
		foreach ($meta as $key => $value) {
			add_user_meta($user_id, $key, $value);
		}
	}

	/**
	 * Custom endpoint to register new users and receive auth cookie
	 */
	public function register_user ($request) {
		if (get_option('signup_endpoint') != 1) {
			$response = new WP_REST_Response(
				array(
					'error_code' => 'signup_endpoint_not_enabled',
					'error_message' => 'Signup endpoint is not enabled in your plugin settings.',
				)
			);
			$response->set_status(400);

			return $response;
		}

		$email = sanitize_email($request['email']);
		$username = $request['username'] ? sanitize_user($request['username']) : $email;
		$password = $request['password'] ? trim($request['password']) : wp_generate_password();
		$remember = $request['request'];
		$secure = $request['secure'];
		$meta = $request['meta'];

		$new_user_id = wp_create_user($username, $password, $email);

		if (is_wp_error($new_user_id)) {
			$response = new WP_REST_Response(
				array(
					'error_code' => $new_user_id->get_error_code(),
					'error_message' => $new_user_id->get_error_message(),
					'debug' => $email
				)
			);
			$response->set_status(400);

			return $response;
		}

		if ($meta) {
			$this->custom_add_user_meta($meta, $new_user_id);
		}

		$responseObj = $this->generate_auth_cookie($new_user_id, $remember, $secure);

		$response = new WP_REST_Response($responseObj);
    $response->set_status(200);

    return $response;
	}

	/**
	 * Custom endpoint to check if email is available
	 */
	public function check_email ( $request ) {
		$email = sanitize_email($request['email']);

		$isEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

		if (!$isEmail) {
			$response = new WP_REST_Response(
				array(
					'error_code' => 'invalid_email',
					'error_message' => 'Sorry, you need to provide a valid email.'
				)
			);
			$response->set_status(400);

			return $response;
		}

		$email_exists = email_exists($email);

		$response = new WP_REST_Response(
			array(
				'email_exists' => $email_exists !== false
			)
		);
		$response->set_status(200);

		return $response;
	}

	/**
	 * Custom endpoint to log in users and receive auth cookie
	 */
	public function auth_user ($request) {
		if (get_option('login_endpoint') != 1) {
			$response = new WP_REST_Response(
				array(
					'error_code' => 'login_endpoint_not_enabled',
					'error_message' => 'Login endpoint is not enabled in your plugin settings.',
				)
			);
			$response->set_status(400);

			return $response;
		}

		$email = sanitize_email($request['email']);
		$password = $request['password'];
		$remember = $request['request'];
		$secure = $request['secure'];

		$user = wp_authenticate($email, $password);

		if (is_wp_error($user)) {
			$response = new WP_REST_Response(
				array(
					'error_code' => $user->get_error_code(),
					'error_message' => $user->get_error_message(),
				)
			);
			$response->set_status(400);

			return $response;
		}

		$responseObj = $this->generate_auth_cookie($user->ID, $remember, $secure);

		$response = new WP_REST_Response($responseObj);
		$response->set_status(200);

    return $response;
	}

	/**
	 * Custom endpoint to sign up / log in users without password
	 */
	public function passwordless_auth_user ($request) {
		if (get_option('passwordless_endpoint') != 1) {
			$response = new WP_REST_Response(
				array(
					'error_code' => 'passwordless_endpoint_not_enabled',
					'error_message' => 'Passwordless endpoint is not enabled in your plugin settings.',
				)
			);
			$response->set_status(400);

			return $response;
		}

		$email = sanitize_email($request['email']);
		$remember = $request['request'];
		$secure = $request['secure'];

		$email_exists = email_exists($email);

		if ($email_exists === false) {
			$response = new WP_REST_Response(
				array(
					'error_code' => 'email_not_exists',
					'error_message' => 'Sorry, this email is not registered.',
				)
			);
			$response->set_status(400);

			return $response;
		}

		$responseObj = $this->generate_auth_cookie($email_exists, $remember, $secure);

		$response = new WP_REST_Response($responseObj);
    $response->set_status(200);

    return $response;
	}

	function check_auth_key ($request) {
		$headers = apache_request_headers();
		$auth = $headers['Authorization'];
		
		if (!$auth) {
			return false;
		}

		$private_key = get_option('private_key');

		if (!$private_key) {
			return false;
		}

		if ($auth == $private_key) {
			return true;
		}

		return false;
	}

	/**
	 * Register custom endpoints on WordPress REST API
	 */
	public function register_rest_routes () {
		register_rest_route( 'arengu', 'signup', array(
			'methods' => 'POST',
			'callback' => array( $this, 'register_user'),
			'permission_callback' => array( $this, 'check_auth_key'),
			'show_in_index' => false,
		));
		register_rest_route( 'arengu', 'auth', array(
			'methods' => 'POST',
			'callback' => array( $this, 'auth_user'),
			'permission_callback' => array( $this, 'check_auth_key'),
			'show_in_index' => false,
		));
		register_rest_route( 'arengu', 'checkEmail', array(
			'methods' => 'POST',
			'callback' => array( $this, 'check_email'),
			'permission_callback' => array( $this, 'check_auth_key'),
			'show_in_index' => false,
		));
		register_rest_route( 'arengu', 'passwordless/auth', array(
			'methods' => 'POST',
			'callback' => array( $this, 'passwordless_auth_user'),
			'permission_callback' => array( $this, 'check_auth_key'),
			'show_in_index' => false,
		));
	}

}
