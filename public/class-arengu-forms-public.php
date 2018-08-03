<?php

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

		add_shortcode('arengu-form', array( $this, 'arengu_forms_shortcode'));

	}

	/**
	 * @param $id
	 */
	public function arengu_forms_shortcode($params) {

		return $params && $params['id']
			? "<div data-arengu-form-id='". $params['id'] ."'></div>"
			: null;

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script('arengu-forms-sdk', esc_url('https://sdk.arengu.com/forms.js'));

	}

}
