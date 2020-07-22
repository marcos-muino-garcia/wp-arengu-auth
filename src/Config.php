<?php

namespace ArenguAuth;

class Config
{
    private $data;

    private static $prefix = 'arengu';
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();

            self::$instance->data = [
                'plugin_path' => dirname(__DIR__),
                'plugin_url' => plugin_dir_url(__DIR__),
                'key_length' => 64, // bytes
                'jwt_alg' => 'HS256',
                'jwt_expiry' => 300,
            ];
        }

        return self::$instance;
    }

    public static function get($key)
    {
        return array_key_exists($key, self::$instance->data) ?
            self::$instance->data[$key] :
            get_option(self::prefix($key));
    }

    public static function set($key, $value)
    {
        if (array_key_exists($key, self::$instance->data[$key])) {
            return new \Exception('Cannot overwrite a hardcoded config value');
        }

        add_option(self::prefix($key), $value);
    }

    public static function del($key)
    {
        if (array_key_exists($key, self::$instance->data[$key])) {
            return new \Exception('Cannot delete a hardcoded config value');
        }

        delete_option(self::prefix($key));
    }

    public static function prefix($key)
    {
        return self::$prefix . "_{$key}";
    }
}
