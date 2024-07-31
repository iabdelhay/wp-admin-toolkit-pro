<div class="wrap">
    <h1>WP AdminToolkit Pro Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_admin_toolkit_pro_settings');
        do_settings_sections('wp_admin_toolkit_pro');
        submit_button();
        ?>
    </form>
</div>
