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

	public function register_tinymce_plugin($plugin_array) {

		$plugin_array['arengu_forms_button'] =  plugins_url( 'js/arengu-forms-tinymce.js', __FILE__ );
		return $plugin_array;

	}

	function add_tinymce_buttons ( $buttons ) {

		$buttons[] = 'arengu_forms_button';
		return $buttons;

	}

}
