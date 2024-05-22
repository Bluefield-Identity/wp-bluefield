<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap" id="bluefield-identity-admin">

<div class="bf--page-title-wrapper">
    <div class="bf--page-header">
    <?php
        $logo = BLUEFIELD_PLUGIN_URL . 'resources/images/bluefield_logo_b.webp';
        echo '<h1>Bluefield Identity - Settings</h1>';
        settings_errors();
    ?>
    </div>
</div>

<div class="bluefield_content_wrapper">
    <?php
    $active_tab = isset( $_GET[ 'page' ] ) ? sanitize_text_field(wp_unslash($_GET[ 'page' ])) : 'bluefield_identity-api-settings';
    ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=bluefield_identity-api-settings" class="nav-tab <?php echo ($active_tab == 'bluefield_identity-api-settings' || $active_tab == 'bluefield_identity') ? 'nav-tab-active' : ''; ?>">API Settings</a>
        <a href="?page=bluefield_identity-client-vars" class="nav-tab <?php echo $active_tab == 'bluefield_identity-client-vars' ? 'nav-tab-active' : ''; ?>">Additional Variables (Advanced)</a>
    </h2>

    <form method="post" action="options.php">
        <?php
            if( $active_tab === 'bluefield_identity-api-settings' || $active_tab === 'bluefield_identity' ) {
                settings_fields('blid-common-settings-options');
                do_settings_sections('bluefield_identity-api-settings');
                submit_button();

            } elseif ($active_tab === 'bluefield_identity-client-vars' ) {
                ?>
                <div class="bf_client_vars">
                    <?php
                        settings_fields('blid-client-variables-options');
                        do_settings_sections('bluefield_identity-client-vars');
                        submit_button();
                    ?>
                </div>
                <?php
            }

        ?>
    </form>
</div>
</div>
