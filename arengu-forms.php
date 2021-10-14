<?php

/**
 * The main file in older v2 versions of this plugin was called
 * `arengu-forms.php`, but with v3 it was renamed to `arengu-auth.php`
 * to better reflect its nature.
 *
 * Turns out, if you had v2 installed, WordPress remembers the path and
 * tries to load the old file, which obviously fails.
 *
 * Fix it creating this dummy file that only loads the new renamed file.
 */

require('arengu-auth.php');

