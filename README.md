# WP AdminToolkit Pro

A foundational package for WordPress to manage settings and forms.

## Installation

### Using Composer

1. Add the package to your Composer project:
    ```bash
    composer require iabdelhay/wp-admin-toolkit-pro
    ```

2. Include the autoloader in your WordPress plugin setup:
    ```php
    require_once __DIR__ . '/vendor/autoload.php';
    ```

3. Initialize the plugin:
    ```php
    $plugin = new WPAdminToolkitPro\Bootstrap();
    $plugin->run();
    ```

## Usage

### Adding Settings

To add custom settings, use the `SettingsManager` class:
```php
add_action('wp_atk_pro_loaded', function() {
    $settingsManager = new WPAdminToolkitPro\Settings\SettingsManager();
    $settingsManager->registerSection('wp_atk_pro_settings', 'custom_section', 'My Custom Settings');
    $settingsManager->addSettingField('wp_atk_pro_settings', 'custom_section', 'custom_setting', 'My Custom Setting');
});
