<?php

namespace ArenguAuth;

class Shortcode
{
    private $inserted_sdk = false;

    public function apply($params)
    {
        if (isset($params['id']) && ctype_digit($params['id'])) {
            $output = "<div data-arengu-form-id=\"{$params['id']}\"></div>";

            if (! $this->inserted_sdk) {
                $this->inserted_sdk = true;

                $output .= '<script async src="https://sdk.arengu.com/forms.js"></script>';
            }

            return $output;
        }

        return null;
    }
}
