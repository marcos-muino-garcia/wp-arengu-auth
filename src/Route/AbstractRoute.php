<?php

namespace ArenguAuth\Route;

use Firebase\JWT\JWT;
use ArenguAuth\Config;

abstract class AbstractRoute
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    abstract public function register();

    abstract public function execute($request);

    public function hasPermission()
    {
        return $this->checkAuthKey();
    }

    protected function simpleRegister($methods, $path)
    {
        $methods = is_array($methods) ? $methods : [$methods];

        register_rest_route(
            'arengu_auth',
            $path,
            [
                'methods' => $methods,
                'callback' => [$this, 'execute'],
                'permission_callback' => [$this, 'hasPermission'],
                'show_in_index' => false,
            ]
        );
    }

    protected function checkAuthKey()
    {
        return hash_equals(
            'Bearer ' . $this->config->get('private_key'),
            $this->getAuthHeader()
        );
    }

    protected function getAuthHeader()
    {
        // https://bugs.php.net/bug.php?id=72915
        // https://github.com/symfony/symfony/issues/19693

        $header = null;

        if (isset($_SERVER['Authorization'])) {
            $header = $_SERVER['Authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $apache_headers = apache_request_headers();

            if (isset($apache_headers['Authorization'])) {
                $header = $apache_headers['Authorization'];
            }
        }

        return $header;
    }

    protected function buildRestResponse($status, $body = null)
    {
        $response = new \WP_REST_Response($body);

        $response->set_status($status);

        return $response;
    }

    protected function buildRestError($status, $code, $message, $data = null)
    {
        $body = [
            'error_code' => $code,
            'error_message' => $message,
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        return $this->buildRestResponse($status, $body);
    }

    protected function getTrimmedString($arr, $key)
    {
        return ! empty($arr[$key]) && (
            is_string($arr[$key]) ||
            is_int($arr[$key]) ||
            is_float($arr[$key]) ||
            is_bool($arr[$key])
            ) ?
            (string) trim($arr[$key]) :
            '';
    }

    protected function getTrimmedStrings($arr, $keys)
    {
        $output = [];

        foreach ($keys as $key) {
            $output[$key] = $this->getTrimmedString($arr, $key);
        }

        return $output;
    }

    protected function getTokenParams($request)
    {
        $params = [
            'expires_in' => (int) $this->getTrimmedString($request, 'expires_in'),
            'redirect_uri' => $this->getTrimmedString($request, 'redirect_uri'),
        ];

        if (!$params['expires_in']) {
            $params['expires_in'] = $this->config->get('jwt_expiry');
        }

        return $params;
    }

    protected function getUserById($user_id)
    {
        $user = get_user_by('id', $user_id);

        if (!$user) {
            return new \WP_Error(
                'user_not_found',
                'Cannot find a user with that ID.'
            );
        }

        return $user;
    }

    protected function getUserByEmail($email)
    {
        $user = get_user_by('email', $email);

        if (!$user) {
            return new \WP_Error(
                'user_not_found',
                'Cannot find a user with that email.'
            );
        }

        return $user;
    }

    protected function getUserByCredentials($email, $password)
    {
        $user = wp_authenticate($email, $password);

        // wp_authenticate already returns a nice WP_Error in
        // case of a problem, but its message suggests trying
        // with the username instead of the email, which we
        // don't support, so generate a more fitting error
        if (is_wp_error($user)) {
            return new \WP_Error(
                'email_not_exists',
                'Sorry, this email is not registered.'
            );
        }

        return $user;
    }

    protected function signup($email, $password, $first_name, $last_name)
    {
        $data = [
            'user_email' => $email,
            'user_login' => $email, // !
            'user_pass' => $password ? $password : wp_generate_password(25),
        ];

        if (strlen($first_name)) {
            $data['first_name'] = $first_name;
        }

        if (strlen($last_name)) {
            $data['last_name'] = $last_name;
        }

        $user_id_or_error = wp_insert_user($data);

        if (is_wp_error($user_id_or_error)) {
            return $user_id_or_error;
        }

        return get_user_by('id', $user_id_or_error);
    }

    protected function buildToken($user_id, $user_email, $expires_in, $redirect_uri)
    {
        $secret = $this->config->get('jwt_secret');

        $payload = [
            'iss' => $_SERVER['SERVER_NAME'],
            'exp' => $_SERVER['REQUEST_TIME'] + $expires_in,
            'email' => $user_email,
            'sub' => (string) $user_id,
        ];

        if ($redirect_uri) {
            $payload['redirect_uri'] = $redirect_uri;
        }

        return JWT::encode($payload, $secret, $this->config->get('jwt_alg'));
    }

    public function presentUser($user)
    {
        return [
            'id' => $user->ID,
            'email' => $user->user_email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];
    }
}
