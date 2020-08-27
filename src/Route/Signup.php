<?php

namespace ArenguAuth\Route;

class Signup extends AbstractRoute
{
    public function register()
    {
        parent::simpleRegister('POST', 'signup');
    }

    public function execute($request, $allow_passwordless = false)
    {
        $p = $this->getTrimmedStrings(
            $request,
            ['email', 'password', 'expires_in', 'redirect_uri', 'first_name', 'last_name']
        );

        $user = $this->signup($p['email'], $p['password'], $p['first_name'], $p['last_name']);

        if (is_wp_error($user)) {
            unset($p['password']);

            return $this->buildRestError(
                400,
                $user->get_error_code(),
                $user->get_error_message(),
                $p
            );
        }

        $meta = $request->get_param('meta');

        if (is_array($meta)) {
            foreach ($meta as $key => $value) {
                add_user_meta($user->ID, $key, $value);
            }
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
                'login_url' => get_rest_url() . $this->config->get('prefix') . "/login_jwt/{$token}",
            ]
        );
    }
}
