<?php
namespace WPAdminToolkitPro;

use WPAdminToolkitPro\Config;
use WPAdminToolkitPro\Admin\AdminPageFactory;
use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\core\Singleton;
use WPAdminToolkitPro\Settings\SettingsManager;
use WPAdminToolkitPro\Form\FormHandler;

class Bootstrap implements SingletonContract
{
    use Singleton;

    public static function init(
        string $pluginKey = "", 
        string $pluginName = "", 
        string $version = '1.0.0'
    ): Config
    {
        $config = Config::instance($pluginKey, $pluginName, $version);

        self::instance()->run();

        return $config;
    }

    public function run() 
    {
        $this->loadDependencies();
        $this->defineHooks();

        SettingsManager::init();
        
        do_action('wp_atk_pro_loaded');
    }

    private function loadDependencies() {
        require_once __DIR__ . '/functions.php';
    }

    private function defineHooks() {

        add_action('admin_menu', [new AdminPageFactory(), 'addAdminPages']);

        add_action('admin_post_wp_atk_pro_form', [new FormHandler(), 'handleFormSubmission']);
        add_action('wp_ajax_wp_atk_pro_form', [new FormHandler(), 'handleFormSubmission']);
    }

    public static function activate() {
        // Code to run during activation.
    }

    public static function deactivate() {
        // Code to run during deactivation.
    }
}
