<?php

namespace ArenguAuth\Route;

use Firebase\JWT\JWT as JwtLib;
use Firebase\JWT\ExpiredException;

class Jwt extends AbstractRoute
{
    public function register()
    {
        parent::simpleRegister('GET', 'login_jwt/(?P<token>[^/]+)');
    }

    public function hasPermission()
    {
        return true; // public endpoint
    }

    public function execute($request)
    {
        $secret = $this->config->get('jwt_secret');

        if (!$secret) {
            return $this->friendlyError(500, 'The secret was not configured.');
        }

        try {
            $decodedToken = (array) JwtLib::decode(
                $this->getTrimmedString($request, 'token'),
                $secret,
                [$this->config->get('jwt_alg')]
            );
        } catch (ExpiredException $ex) {
            return $this->friendlyError(401, 'The provided token is expired.');
        } catch (\Exception $ex) {
            return $this->friendlyError(400, 'The provided token is invalid.');
        }

        $issuer = $this->getTrimmedString($decodedToken, 'iss');
        $email = $this->getTrimmedString($decodedToken, 'email');
        $user_id = $this->getTrimmedString($decodedToken, 'sub');
        $redirect_uri = $this->getTrimmedString($decodedToken, 'redirect_uri');

        if ($issuer !== $_SERVER['SERVER_NAME'] || ! $email || ! $user_id) {
            return $this->friendlyError(400, 'The provided token is missing data.');
        }

        $user = $this->getUserById($user_id);

        // check for freaky coincidence where 2 users somehow managed to exchange email
        // addresses between them after the token was generated
        if ((string) $user->ID !== $user_id) {
            return $this->friendlyError(500, 'There was a problem with the session, please try again.');
        }

        wp_set_auth_cookie($user->ID, true);

        $response = $this->buildRestResponse(302);

        $response->header(
            'Location',
            $redirect_uri ? $redirect_uri : get_edit_profile_url()
        );

        return $response;
    }

    public function friendlyError($status, $msg)
    {
        header('Content-Type: text/html;charset=utf-8', true);
        include $this->config->get('plugin_path') . '/assets/friendly-error.tpl.php';
        die();
    }
}
