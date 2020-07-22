<?php

namespace ArenguAuth\Route;

use ArenguAuth\Config;

class Login extends AbstractRoute
{
    public function register()
    {
        parent::simpleRegister('POST', 'login_password');
    }

    public function execute($request, $allow_passwordless = false)
    {
        $p = $this->getTrimmedStrings(
            $request,
            ['email', 'password', 'expires_in', 'redirect_uri']
        );

        $user =
            $allow_passwordless ?
            $this->getUserByEmail($p['email']) :
            $this->getUserByCredentials($p['email'], $p['password']);

        if (is_wp_error($user)) {
            return $this->buildRestError(
                401,
                $user->get_error_code(),
                $user->get_error_message(),
                $p['email']
            );
        }

        if ($allow_passwordless && Config::get('disallow_admins') && in_array('administrator', $user->roles)) {
            return $this->buildRestError(
                403,
                'admin_access_disabled',
                'Passwordless access for users with "administrator" role is disabled.'
            );
        }

        $token = $this->buildToken(
            $user->ID,
            $p['email'],
            $p['expires_in'] ? $p['expires_in'] : $this->config->get('jwt_expiry'),
            $p['redirect_uri']
        );

        return $this->buildRestResponse(
            200,
            [
                'user' => $this->presentUser($user),
                'token' => $token,
                'login_url' => get_rest_url() . "arengu/login_jwt/{$token}",
            ]
        );
    }
}
