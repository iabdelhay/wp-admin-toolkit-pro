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
            'callback' => [new AdminPageController(), 'render'],
            'icon_url' => 'dashicons-admin-tools',
            'position' => null
        ]);

        array_map([$this, 'autoRegisterPages'], $this->pages);
    }

    private function createAdminPage($type, $args): void
    {
        $isSubmenu = isset($args['is_sub_level']) && isset($args['parent_menu_slug']) && $args['is_sub_level'] == true;

        switch ($type) {
            case 'main_settings':
                $this->add_menu_page($args);
                break;
            case 'admin_page':
                if($isSubmenu){
                    $this->add_submenu_page($args);
                }else{
                    $this->add_menu_page($args);
                }
                break;

            // Additional cases for other types of admin pages can be added here.
        }
    }

    private function autoRegisterPages($page): void
    {
        $this->createAdminPage($page['type'], $page['args']);
    }

    private function guessAdminPages(): array
    {
        $pluginMainDirectory = $this->config->getPluginMainDirectory(); // Assuming this returns the plugin root path
        $adminPages = [];

        // Scan the main directory
        $adminPages = array_merge($adminPages, $this->scanDirectoryForAdminPages($pluginMainDirectory));

        // Check if the 'admin' directory exists and scan it
        $adminDir = $pluginMainDirectory . DIRECTORY_SEPARATOR . $this->config->getAdminFolder();
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
    
                // Safely check the file content before including
                if ($this->fileLikelyImplementsAdminPage($filePath)) {
                    include_once $filePath; // Now safe to include the file
    
                    $className = $this->getClassNameFromFile($filePath);
    
                    if (class_exists($className)) {
                        $reflection = new ReflectionClass($className);
                        if ($reflection->implementsInterface('WPAdminToolkitPro\Admin\Contracts\AdminPage')) {

                            $boject = new $className();
                            $isSubmenu = $reflection->implementsInterface('WPAdminToolkitPro\Admin\Contracts\AdminSubMenu');

                            $adminPages[] = [
                                'type' => 'admin_page',
                                'args' => [
                                    'page_title' => $boject->getPageTitle(),
                                    'menu_title' => $boject->getMenuTitle(),
                                    'capability' => $boject->getCapability(),
                                    'menu_slug' => $boject->getMenuSlug(),
                                    'callback' => [$boject, 'render'],
                                    'icon_url' => $boject->getIconUrl(),
                                    'position' => $boject->getPosition(),
                                    'is_sub_level' => $isSubmenu,
                                    'parent_menu_slug' => $isSubmenu ? $boject->getParentMenuSlug() : null,
                                    'boject' => $boject,
                                    'class_name' => $className
                                ]
                            ];
                        }
                    }
                }
            }
        }
    
        return $adminPages;
    }
    
    private function fileLikelyImplementsAdminPage(string $filePath): bool
    {
        $contents = file_get_contents($filePath);

        // Use a regex to find any occurrence of 'implements' followed by 'AdminPage'
        return preg_match('/implements\s+.*\bAdminPage\b/', $contents) === 1;
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
    
        return $namespace ? $namespace . '\\' . $class : $class;
    }

    private function add_menu_page(array $args): void
    {
        add_menu_page(
            $args['page_title'],
            $args['menu_title'],
            $args['capability'],
            $args['menu_slug'],
            $args['callback'],
            $args['icon_url'],
            $args['position']
        );
    }

    private function add_submenu_page(array $args): void
    {
        add_submenu_page(
            $args['parent_menu_slug'],
            $args['page_title'],
            $args['menu_title'],
            $args['capability'],
            $args['menu_slug'],
            $args['callback'],
            $args['position']
        );
    }
}
