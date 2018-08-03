<?php

/**
 * Plugin Name:       Arengu Forms
 * Plugin URI:        https://github.com/arengu/forms-wp-plugin
 * Description:       Easily embed Arengu Forms into your webpage with our WordPress plugin.
 * Version:           1.0.0
 * Author:            Arengu
 * Author URI:        https://www.arengu.com
 * License:           Apache License 2.0
 * Text Domain:       arengu-forms
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ARENGU_FORMS_VERSION', '1.0.0' );

function activate_arengu_forms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arengu-forms-activator.php';
	Arengu_Forms_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_arengu_forms' );

function deactivate_arengu_forms() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-arengu-forms-deactivator.php';
	Arengu_Forms_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_arengu_forms' );

require plugin_dir_path( __FILE__ ) . 'includes/class-arengu-forms.php';

function run_arengu_forms() {

	$arengu_forms = new Arengu_Forms();
	$arengu_forms->run();

}

run_arengu_forms();
