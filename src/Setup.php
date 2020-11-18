<?php

namespace ArenguAuth;

class Setup
{
    public static function execute()
    {
        register_activation_hook(ARENGU_PLUGIN_FILE, '\ArenguAuth\Setup::activate');
        register_deactivation_hook(ARENGU_PLUGIN_FILE, '\ArenguAuth\Setup::deactivate');
        register_uninstall_hook(ARENGU_PLUGIN_FILE, '\ArenguAuth\Setup::uninstall');

        add_action('init', '\ArenguAuth\Setup::registerShortcode');
        add_action('admin_menu', '\ArenguAuth\Setup::registerSettings');
        add_action('rest_api_init', '\ArenguAuth\Setup::registerRoutes');
    }

    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function generateKey($length)
    {
        return self::base64UrlEncode(wp_generate_password($length));
    }

    public static function activate()
    {
        $config = Config::getInstance();
        $key_length = $config->get('key_length');

        $config->setIfUnset('private_key', self::generateKey($key_length));
        $config->setIfUnset('jwt_secret', self::generateKey($key_length));
    }

    public static function deactivate()
    {
    }

    public static function uninstall()
    {
        $config = Config::getInstance();

        $config->del('private_key');
        $config->del('jwt_secret');
    }

    public static function registerShortcode()
    {
        add_shortcode('arengu-form', [new Shortcode(), 'apply']);
    }

    public static function registerRoutes()
    {
        $config = Config::getInstance();

        $routes = [
            new Route\Login($config),
            new Route\Passwordless($config),
            new Route\Signup($config),
            new Route\Jwt($config),
            new Route\CheckEmail($config),
        ];

        array_walk(
            $routes,
            function ($route) {
                $route->register();
            }
        );

        // wordpress doesn't send the WWW-Authenticate header on a 401 error
        add_filter('rest_post_dispatch', function($response) {
            if ($response->get_status() === 401) {
                $response->header('WWW-Authenticate', 'Bearer');
            }

            return $response;
        });
    }

    public static function registerSettings()
    {
        add_menu_page(
            'Arengu Auth Settings',
            'Arengu Auth',
            'manage_options',
            'arengu-auth-settings',
            [new AdminPage(Config::getInstance()), 'render'],
            Config::getInstance()->get('plugin_url') . 'assets/logo.svg'
        );

        register_setting(Config::prefix('api_settings'), Config::prefix('disallow_admins'));
        register_setting(Config::prefix('key_settings'), Config::prefix('private_key'));
        register_setting(Config::prefix('jwt_settings'), Config::prefix('jwt_secret'));
    }
}
