<?php $config = \ArenguAuth\Config::getInstance(); ?>

<style>
    .arengu-secret {
        width: 300px; 
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }

    .arengu-copy-button {
        vertical-align: top !important;
    }

    .arengu-warn-renew {
        vertical-align: baseline;
    }
</style>

<div class="wrap">
    <h1>Arengu Auth Settings</h1>

    <h2>API configuration values</h2>
    <p>Use these values to configure the actions in your Arengu flows.</p>

    <table class="form-table">
        <tr>
            <th>Base URL</th>
            <td><p><code><?= get_site_url(); ?></code></p></td>
        </tr>

        <form method="post" action="options.php">
            <?php settings_fields(\ArenguAuth\Config::prefix('key_settings')); ?>
            <tr>
                <th>API key</th>
                <td>
                    <p>
                        <input type="text" readonly class="arengu-secret"
                            id="arengu-private-key" value="<?= $config->get('private_key'); ?>">

                        <button type="button" data-copy="arengu-private-key"
                            class="button button-primary arengu-copy-button">
                            Copy
                        </button>
                    </p>
                    <p>
                        <span class="dashicons dashicons-warning"
                            style="vertical-align: middle; color: orangered"></span>

                        <b>Do not share this with anyone!</b> You can

                        <button
                            name="<?= \ArenguAuth\Config::prefix('private_key'); ?>"
                            class="button button-link arengu-warn-renew"
                            value="<?= \ArenguAuth\Setup::generateKey($config->get('key_length')); ?>">regenerate it
                        </button>

                        if you accidentally did.
                    </p>
                </td>
            </tr>
        </form>

        <form method="post" action="options.php">
            <?php settings_fields(\ArenguAuth\Config::prefix('jwt_settings')); ?>

            <tr>
                <th>JWT secret</th>
                <td>
                    <input type="text" readonly class="arengu-secret"
                        id="arengu-jwt-secret" value="<?= $config->get('jwt_secret'); ?>">

                    <button type="button" data-copy="arengu-jwt-secret"
                        class="button button-primary arengu-copy-button">
                        Copy
                    </button>

                    <p>
                        <span class="dashicons dashicons-warning"
                            style="vertical-align: middle; color: orangered"></span>

                        <b>Do not share this with anyone!</b> You can

                        <button
                            name="<?= \ArenguAuth\Config::prefix('jwt_secret'); ?>"
                            class="button button-link arengu-warn-renew" style="vertical-align: baseline"
                            value="<?= \ArenguAuth\Setup::generateKey($config->get('key_length')); ?>">regenerate it
                        </button>

                        if you accidentally did.
                    </p>
                </td>
            </tr>
        </form>
    </table>

    <!--
    <h2>Advanced settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields(\ArenguAuth\Config::prefix('api_settings')); ?>

        <table class="form-table">
            <tr>
                <th>Secure admin access</th>
                <td>
                    <p>
                        <input type="checkbox" id="disallow-admins-checkbox"
                            name="<?= \ArenguAuth\Config::prefix('disallow_admins'); ?>"
                            value="<?= (int) !\ArenguAuth\Config::get('disallow_admins'); ?>"
                            <?= \ArenguAuth\Config::get('disallow_admins') ? 'checked' : ''; ?>>
                        <label for="disallow-admins-checkbox">
                            Disallow passwordless access for users with "administrator" role
                        </label>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
    -->
</div>

<script>
    (function() {
        document.querySelectorAll('.arengu-warn-renew').forEach(function(elem) {
            elem.addEventListener('click', function(ev) {
                if(!confirm('Are you sure? The previous value will be invalidated and previous ' +
                    'integrations will stop working. This is not reversible, make sure you know ' +
                    'what you are doing!')) {
                    ev.preventDefault();
                }
            });
        });

        document.querySelectorAll('.arengu-copy-button').forEach(function(elem) {
            elem.addEventListener('click', function(ev) {
                elem.disabled = true;

                document.getElementById(elem.dataset.copy).select();
                document.execCommand('copy');
                ev.preventDefault();

                var oldText = elem.innerText;
                elem.innerText = 'Done!';

                setTimeout(function() {
                    elem.innerText = oldText;
                    window.getSelection().removeAllRanges();
                    elem.disabled = false;
                }, 750);
            });
        });
    })();
</script>
