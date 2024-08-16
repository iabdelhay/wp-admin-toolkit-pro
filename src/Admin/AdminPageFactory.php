<?php
namespace WPAdminToolkitPro\Admin;

use ReflectionClass;
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

        array_map([$this, 'autoRegisterPages'], $this->pages);
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
            case 'admin_page':
                add_menu_page(
                    $args['page_title'],
                    $args['menu_title'],
                    $args['capability'],
                    $args['menu_slug'],
                    [$args['class_name'], 'render'],
                    $args['icon_url'],
                    $args['position']
                );
                break;

            // Additional cases for other types of admin pages can be added here.
        }
    }

    private function autoRegisterPages($page): void
    {
        if(!file_exists($page['file'])){
            return;
        }


        $this->createAdminPage($page['type'], $page['args']);
    }

    private function guessAdminPages(): array
    {
        $pluginRoot = $this->config->getPluginRoot(); // Assuming this returns the plugin root path
        $adminPages = [];

        // Scan the root directory
        $adminPages = array_merge($adminPages, $this->scanDirectoryForAdminPages($pluginRoot));

        // Check if the 'admin' directory exists and scan it
        $adminDir = $pluginRoot . DIRECTORY_SEPARATOR . $this->config->getAdminFolder();
        if (is_dir($adminDir)) {
            $adminPages = array_merge($adminPages, $this->scanDirectoryForAdminPages($adminDir));
        }

        return $adminPages;
    }


    private function scanDirectoryForAdminPages(string $directory): array
    {
        $adminPages = [];
        $files = scandir($directory);

        foreach ($files as $file) {
            // Skip . and ..
            if ($file === '.' || $file === '..') {
                continue;
            }

            // Check if the file starts with 'class-page-' and ends with '.php'
            if (strpos($file, 'class-page-') === 0 && substr($file, -4) === '.php') {

                $filePath = $directory . DIRECTORY_SEPARATOR . $file;
                $className = $this->getClassNameFromFile($filePath);

                include_once $filePath;

                if (class_exists($className)) {
                    $reflection = new ReflectionClass($className);

                    if ($reflection->implementsInterface('WPAdminToolkitPro\Admin\Contracts\AdminPage')) {
                        $adminPages[] = [
                            'type' => 'admin_page',
                            'file' => $filePath,
                            'args' => [
                                'page_title' => $className::getPageTitle(),
                                'menu_title' => $className::getMenuTitle(),
                                'capability' => $className::getCapability(),
                                'menu_slug' => $className::getMenuSlug(),
                                'icon_url' => $className::getIconUrl(),
                                'position' => $className::getPosition(),
                                'class_name' => $className
                            ]
                        ];
                    }
                }
            }
        }

        return $adminPages;
    }

    private function getClassNameFromFile(string $filePath): string
    {
        $contents = file_get_contents($filePath);
        $namespace = '';
        $class = '';

        // Extract namespace
        if (preg_match('/namespace\s+(.+?);/s', $contents, $matches)) {
            $namespace = $matches[1];
        }

        // Extract class name
        if (preg_match('/class\s+(\w+)/s', $contents, $matches)) {
            $class = $matches[1];
        }

        return $namespace . '\\' . $class;
    }
}
