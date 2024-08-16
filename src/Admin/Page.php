<?php 
namespace WPAdminToolkitPro\Admin;

use WPAdminToolkitPro\Config;

abstract class Page
{
    public function getMenuSlug(): string
    {
        return $this->generateSlugFromClass();
    }

    public function getPageTitle(): string
    {
        return $this->generateTitleFromClass();
    }

    public function getMenuTitle(): string
    {
        return $this->generateTitleFromClass();
    }

    public function getCapability(): string
    {
        return 'manage_options';
    }

    public function getIconUrl(): string
    {
        return 'dashicons-admin-generic';
    }

    public function getPosition(): ?int
    {
        return 25;
    }

    /**
     * Load a view file from the plugin's views folder, optionally passing data to it,
     * and return the HTML content as a string.
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    protected function view(string $path, array $data = []): string
    {
        $directory = Config::instance()->getPluginMainDirectory();

        $templatePath =  $directory . DIRECTORY_SEPARATOR . $path . '.php';
        
        if (file_exists($templatePath)) {
            extract($data);

            // Start output buffering
            ob_start();
            
            include $templatePath;
            
            // Get the content of the buffer and clean it
            $content = ob_get_clean();
            
            return $content;
        } else {
            // Optionally return a warning or handle the error differently
            return "View file '{$path}.php' not found in views folder.";
        }
    }

    /**
     * Generates a slug from the class name, ignoring the word "Page".
     *
     * @return string
     */
    private function generateSlugFromClass(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $className = str_replace('Page', '', $className); // Remove "Page"
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $className));
    }

    /**
     * Generates a title from the class name, ignoring the word "Page".
     *
     * @return string
     */
    private function generateTitleFromClass(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $className = str_replace('Page', '', $className); // Remove "Page"
        return ucwords(str_replace(['-', '_'], ' ', preg_replace('/(?<!^)[A-Z]/', ' $0', $className)));
    }
}