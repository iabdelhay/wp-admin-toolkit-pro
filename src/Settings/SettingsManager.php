<?php
namespace WPAdminToolkitPro\Settings;

class SettingsManager {
    public function registerSettings() {
        register_setting('wp_admin_toolkit_pro_settings', 'wp_atk_pro_settings');

        add_settings_section(
            'wp_atk_pro_main',
            'Main Settings',
            'null',
            'wp_admin_toolkit_pro'
        );
    }

    public function addSettingField($id, $title, $section = 'wp_atk_pro_main') {
        add_settings_field(
            $id,
            $title,
            [$this, 'render'],
            'wp_admin_toolkit_pro',
            $section,
            ['label_for' => $id, 'field_type' => 'checkbox']
        );
    }

    public function render($args) {
        $options = get_option('wp_atk_pro_settings');
        $value = isset($options[$args['label_for']]) ? $options[$args['label_for']] : '';
        switch ($args['field_type']) {
            case 'checkbox':
                echo '<input type="checkbox" id="'. $args['label_for'] .'" name="wp_atk_pro_settings['. $args['label_for'] .']" value="1" '. checked($value, 1, false) .'>';
                break;
            // Additional case handlers for other types of inputs can be added here.
        }
    }
}
