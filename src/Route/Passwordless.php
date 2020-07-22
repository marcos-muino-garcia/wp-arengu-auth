<?php

namespace ArenguAuth\Route;

class Passwordless extends Login
{
    public function register()
    {
        parent::simpleRegister('POST', 'passwordless_login');
    }

    public function execute($request, $allow_passwordless = false)
    {
        // delegate to normal login but allow explicitly the absence of a password
        return parent::execute($request, true);
    }
}
