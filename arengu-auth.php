<?php

/**
 * Plugin Name: Arengu Auth
 * Plugin URI:  https://github.com/arengu/arengu-auth-wp-plugin
 * Description: Connect Arengu to your WordPress authentication system.
 * Version:     {{ VERSION }}
 * Author:      Arengu
 * Author URI:  https://www.arengu.com
 * License:     Apache License 2.0
 * Text Domain: arengu-auth
 */

defined('WPINC') or die('WPINC is not defined');

require dirname(__FILE__) . '/vendor/autoload.php';

define('ARENGU_PLUGIN_FILE', __FILE__);

\ArenguAuth\Setup::execute();
