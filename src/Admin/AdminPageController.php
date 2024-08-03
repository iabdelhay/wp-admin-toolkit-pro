<?php
namespace WPAdminToolkitPro\Admin;
use WPAdminToolkitPro\Config;

class AdminPageController {

    private $page_slug;
    private Config $config;

    public function __construct($page_slug) {
        $this->page_slug = $page_slug;

        $this->config = Config::instance();
    }

    public function render() {
        include __DIR__ . '/../views/settings-page.php';
    }
}
