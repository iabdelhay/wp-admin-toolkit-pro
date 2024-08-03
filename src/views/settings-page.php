<div class="wrap">
    <h1><?php echo $this->config->getPluginName() ?></h1>
    <form method="post" action="options.php">
        <?php
        settings_fields($this->config->getPluginKey());
        do_settings_sections($this->config->getPluginKey());
        submit_button();
        ?>
    </form>
</div>
