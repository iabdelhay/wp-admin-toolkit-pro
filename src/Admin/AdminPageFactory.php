<?php
namespace WPAdminToolkitPro\Admin;

class AdminPageFactory {
    public function addAdminPages() {
        $this->createAdminPage('main_settings', [
            'page_title' => 'WP AdminToolkit Pro Settings',
            'menu_title' => 'AdminToolkit Pro',
            'capability' => 'manage_options',
            'menu_slug' => 'wp-admin-toolkit-pro',
            'icon_url' => 'dashicons-admin-tools',
            'position' => null
        ]);
    }

    public static function createAdminPage($type, $args) {
        switch ($type) {
            case 'main_settings':
                add_menu_page(
                    $args['page_title'],
                    $args['menu_title'],
                    $args['capability'],
                    $args['menu_slug'],
                    [new AdminPageController($args['menu_slug']), 'render'],
                    $args['icon_url'],
                    $args['position']
                );
                break;

            // Additional cases for other types of admin pages can be added here.
        }
    }
}
