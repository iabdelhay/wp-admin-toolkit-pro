<?php
namespace WPAdminToolkitPro;

use WPAdminToolkitPro\Admin\AdminPageFactory;
use WPAdminToolkitPro\Settings\SettingsManager;
use WPAdminToolkitPro\Form\FormHandler;

class Bootstrap 
{
    public function __construct() {
        $this->loadDependencies();
        $this->defineHooks();
    }

    private function loadDependencies() {
        require_once __DIR__ . '/functions.php';
    }

    private function defineHooks() {
        add_action('admin_menu', [new AdminPageFactory(), 'addAdminPages']);
        add_action('admin_init', [new SettingsManager(), 'registerSettings']);
        add_action('admin_post_wp_atk_pro_form', [new FormHandler(), 'handleFormSubmission']);
        add_action('wp_ajax_wp_atk_pro_form', [new FormHandler(), 'handleFormSubmission']);
    }

    public function run() {
        do_action('wp_atk_pro_loaded');
    }

    public static function activate() {
        // Code to run during activation.
    }

    public static function deactivate() {
        // Code to run during deactivation.
    }
}
