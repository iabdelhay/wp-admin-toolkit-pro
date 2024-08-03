<?php
namespace WPAdminToolkitPro;

use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\Core\Singleton;

class Config implements SingletonContract
{
    use Singleton;

    private $pluginKey;
    private $pluginName;
    private $version;

    public function __construct(
        $pluginKey = 'wp_admin_toolkit_pro', 
        $pluginName = 'WP Admin toolkit pro',
        $version = '1.0.0'
    )
    {
        $this->pluginKey = $pluginKey;
        $this->pluginName = $pluginName;
        $this->version = $version;
    }

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
}
