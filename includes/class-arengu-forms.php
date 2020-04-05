<?php

class Arengu_Forms {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'ARENGU_FORMS_VERSION' ) ) {
			$this->version = ARENGU_FORMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'arengu-forms';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * - Arengu_Forms_Loader. Orchestrates the hooks of the plugin.
	 * - Arengu_Forms_i18n. Defines internationalization functionality.
	 * - Arengu_Forms_Admin. Defines all hooks for the admin area.
	 * - Arengu_Forms_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-arengu-forms-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-arengu-forms-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-arengu-forms-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-arengu-forms-public.php';

		$this->loader = new Arengu_Forms_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Arengu_Forms_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new Arengu_Forms_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_tinymce_buttons' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_arengu_settings' );
	}

	private function define_public_hooks() {
		$plugin_public = new Arengu_Forms_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_filter( 'script_loader_tag', $plugin_public, 'add_async_attribute', 10, 3 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'register_rest_routes' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
