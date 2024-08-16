<?php 
namespace WPAdminToolkitPro\Admin\Contracts;

interface AdminSubMenu
{
    /**
     * Get the parent menu slug if this page should appear as a submenu.
     *
     * @return string|null
     */
    public function getParentMenuSlug(): ?string;
}