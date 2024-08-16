<?php
namespace WPAdminToolkitPro\Admin;

use WPAdminToolkitPro\Config;

class AdminPageFactory 
{
    private array $pages = [];
    private Config $config;

    public function __construct()
    {
        $this->config = Config::instance();
        $this->pages = $this->guessAdminPages(); // Populate the pages array
    }

    public function addAdminPages() {

        $this->createAdminPage('main_settings', [
            'page_title' => $this->config->getPluginName(),
            'menu_title' => $this->config->getPluginName(),
            'capability' => 'manage_options',
            'menu_slug' => $this->config->getPluginKey(),
            'icon_url' => 'dashicons-admin-tools',
            'position' => null
        ]);

        array_map([$this, 'createAdminPage'], $this->pages);
    }

    private static function createAdminPage($type, $args) {
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

    private function scanDirectoryForAdminPages(string $directory): array
    {
       
    }

    private function getClassNameFromFile(string $fileName): string
    {
    
    }
}
