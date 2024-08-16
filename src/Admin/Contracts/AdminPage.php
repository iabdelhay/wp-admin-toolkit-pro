<?php 
namespace WPAdminToolkitPro\Admin\Contracts;

interface AdminPage
{
    /**
     * Renders the content of the admin page.
     *
     * This method is called when the admin page is loaded in the WordPress admin.
     * It should output the HTML or other content that should be displayed.
     *
     * @return void
     */
    public static function render(): void;

    /**
     * Retrieves the page title for the admin page.
     *
     * @return string
     */
    public static function getPageTitle(): string;

    /**
     * Retrieves the menu title for the admin page.
     *
     * @return string
     */
    public static function getMenuTitle(): string;

    /**
     * Retrieves the required capability for accessing the admin page.
     *
     * @return string
     */
    public static function getCapability(): string;

    /**
     * Retrieves the menu slug for the admin page.
     *
     * @return string
     */
    public static function getMenuSlug(): string;

    /**
     * Retrieves the icon URL for the admin page.
     *
     * @return string
     */
    public static function getIconUrl(): string;

    /**
     * Retrieves the position in the menu where this admin page should appear.
     *
     * @return int|null
     */
    public static function getPosition(): ?int;
}

