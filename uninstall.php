<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('signup_endpoint');
delete_option('login_endpoint');
delete_option('passwordless_endpoint');
delete_option('private_key');
 
// for site options in Multisite
delete_site_option('signup_endpoint');
delete_site_option('login_endpoint');
delete_site_option('passwordless_endpoint');
delete_site_option('private_key');