<?php

namespace ArenguAuth\Route;

class CheckEmail extends AbstractRoute
{
    public function register()
    {
        parent::simpleRegister('POST', 'check_email');
    }

    public function execute($request)
    {
        $email = $this->getTrimmedString($request, 'email');
        $user = $this->getUserByEmail($email);

        return $this->buildRestResponse(
            200,
            ['email_exists' => !is_wp_error($user)]
        );
    }
}
