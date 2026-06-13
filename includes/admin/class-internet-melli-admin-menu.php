<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers the plugin's admin menu and submenus.
 */
class Internet_Melli_Admin_Menu
{
    /**
     * @param callable $render_callback Render callback used for every menu entry.
     */
    public function add_menu_page($render_callback)
    {

        // منوی اصلی
        add_menu_page(
            __('مسدودکننده سایت‌های خارجی', 'internet-melli'),
            __('مسدودکننده خارجی', 'internet-melli'),
            'manage_options',
            'internet-melli',
            $render_callback,
            'dashicons-lock',
            100
        );

        // منوی اصلی (تنظیمات)
        add_submenu_page(
            'internet-melli',
            __('تنظیمات افزونه', 'internet-melli'),
            __('تنظیمات افزونه', 'internet-melli'),
            'manage_options',
            'internet-melli',
            $render_callback
        );

        // منوی دوم (فعال‌سازی اضطراری)
        add_submenu_page(
            'internet-melli',
            __('فعال‌سازی اورژانسی افزونه‌ها', 'internet-melli'),
            __('فعال‌سازی اورژانسی افزونه‌ها', 'internet-melli'),
            'manage_options',
            'internet-melli&tab=active-plugins',
            $render_callback
        );

        add_filter('submenu_file', function ($submenu_file) {
            global $plugin_page;
            if ($plugin_page == 'internet-melli') {
                $tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
                if ($tab == 'active-plugins') {
                    return 'internet-melli&tab=active-plugins';
                }
                return 'internet-melli';
            }
            return $submenu_file;
        });
    }
}
