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
            ['email', 'password']
        );

        $user =
            $allow_passwordless ?
            $this->getUserByEmail($p['email']) :
            $this->getUserByCredentials($p['email'], $p['password']);

        if (is_wp_error($user)) {
            $status = 400;
            $code = $user->get_error_code();

            if ($code === 'invalid_username') {
                $status = 404;
            }

            return $this->buildRestError(
                $status,
                $code,
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

        $t = $this->getTokenParams($request);

        $token = $this->buildToken(
            $user->ID,
            $p['email'],
            $t['expires_in'],
            $t['redirect_uri']
        );

        return $this->buildRestResponse(
            200,
            [
                'user' => $this->presentUser($user),
                'token' => $token,
                'login_url' => get_rest_url() . $this->config->get('prefix') . "/login_jwt/{$token}",
            ]
        );
    }
}
