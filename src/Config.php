<?php
namespace WPAdminToolkitPro;

use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\Core\Singleton;

class Config implements SingletonContract
{
    use Singleton;

    private string $adminFolder = 'admin';

    public function __construct(
        private readonly string $pluginKey = 'wp_admin_toolkit_pro', 
        private readonly string $pluginName = 'WP Admin toolkit pro',
        private readonly string $version = '1.0.0',
        private string | null $pluginRoot = null,
    )
    {}

    public function getPluginKey(): string
    {
        return $this->pluginKey;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the plugin root directory.
     *
     * @return string
     */
    public function getPluginRoot()
    {
        return $this->pluginRoot;
    }


    public function setAdminFolder(string $adminFolder = 'admin'): static
    {
        $this->adminFolder = $adminFolder;

        return $this;
    }

    /**
     * Get the plugin root directory.
     *
     * @return string
     */
    public function setPluginRoot(string $pluginRoot): static
    {
        if(is_null($this->pluginRoot)){
            $this->pluginRoot = $pluginRoot;
        }

        return $this;
    }
}
