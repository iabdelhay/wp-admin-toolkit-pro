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
        private string | null $pluginRootDirectory = null,
        private string | null $pluginMainDirectory = null,
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
    public function getPluginRootDirectory()
    {
        return $this->pluginRootDirectory;
    }

    /**
     * Get the plugin root directory.
     *
     * @return string
     */
    public function getPluginMainDirectory()
    {
        return $this->pluginMainDirectory;
    }


    public function setAdminFolder(string $adminFolder = 'admin'): static
    {
        $this->adminFolder = $adminFolder;

        return $this;
    }

    public function getAdminFolder(): string
    {
        return $this->adminFolder;
    }

    /**
     * set the plugin root directory.
     *
     * @return string
     */
    public function setPluginRootDirectory(string $pluginRootDirectory): static
    {
        if(is_null($this->pluginRootDirectory)){
            $this->pluginRootDirectory = $pluginRootDirectory;
        }

        return $this;
    }

    /**
     * Set the plugin main directory.
     *
     * @return string
     */
    public function setPluginMainDirectory(string $pluginMainDirectory): static
    {
        if(is_null($this->pluginMainDirectory)){
            $this->pluginMainDirectory = $pluginMainDirectory;
        }

        return $this;
    }
}
