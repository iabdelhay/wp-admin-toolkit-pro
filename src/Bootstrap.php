<?php
namespace WPAdminToolkitPro;

use WPAdminToolkitPro\Config;
use WPAdminToolkitPro\Admin\AdminPageFactory;
use WPAdminToolkitPro\Contracts\SingletonContract;
use WPAdminToolkitPro\Core\Singleton;
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
        $config = Config::instance($pluginKey, $pluginName, $version, self::guessPluginRoot());

        self::ensurePackageConfiguredCorrectly();

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

    /**
     * Guess the developer's plugin root directory.
     *
     * @return string
     */
    protected static function guessPluginRoot()
    {
        // Use debug_backtrace to identify where this class is being called from
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $firstCall = isset($backtrace[1]) ? $backtrace[1] : null;

        if ($firstCall && isset($firstCall['file'])) {
            // Determine the plugin root by moving up the directory structure
            return dirname($firstCall['file'], 1); // Adjust levels as needed
        }

        return '';
    }

    private static function ensurePackageConfiguredCorrectly(): void
    {
        $config = Config::instance();

        if(is_null($config->getPluginRoot())){
            throw new \Exception("It seems that the 'WPAdminToolkitPro' couldn't guess the {$config->getPluginName()} path. Please use the method 'setPluginRoot' from this object instance to set correctly you'r plugin root directory. ");
        }
    }

    public static function activate() {
        // Code to run during activation.
    }

    public static function deactivate() {
        // Code to run during deactivation.
    }
}
