<?php
namespace WPAdminToolkitPro\Admin;

class AdminPageController {
    private $page_slug;

    public function __construct($page_slug) {
        $this->page_slug = $page_slug;
    }

    public function render() {
        include __DIR__ . '/../views/settings-page.php';
    }
}
