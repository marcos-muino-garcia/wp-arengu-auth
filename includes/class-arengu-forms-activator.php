<?php

/**
 * Fired during plugin activation.
 */
class Arengu_Forms_Activator {

	public static function activate() {
		$random_password = wp_generate_password();
		$hash_password = wp_hash_password($random_password);

		add_option('signup_endpoint');
		add_option('login_endpoint');
		add_option('passwordless_endpoint');
		add_option('private_key', 'Bearer ' . base64_encode($hash_password));
	}

}
