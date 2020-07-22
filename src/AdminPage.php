<?php

namespace ArenguAuth;

class AdminPage
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function render()
    {
        include $this->config->get('plugin_path') . '/assets/admin-page.tpl.php';
    }
}
