<?php

/**
 * Admin coordinator for the TalashNet External Request Blocker plugin.
 *
 * Delegates each responsibility to a focused module under includes/admin/:
 *   - Tnet_Admin_Menu     menu + submenu registration
 *   - Tnet_Admin_Settings Settings API registration
 *   - Tnet_Admin_Assets   CSS/JS enqueue + localization
 *   - Tnet_Admin_Ajax     AJAX toggle/test handlers
 *   - Tnet_Admin_Page     settings page rendering (view partials)
 *   - Tnet_Admin_Svg      inline SVG helper
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/admin/class-tnet-admin-svg.php';
require_once __DIR__ . '/admin/class-tnet-admin-menu.php';
require_once __DIR__ . '/admin/class-tnet-admin-settings.php';
require_once __DIR__ . '/admin/class-tnet-admin-assets.php';
require_once __DIR__ . '/admin/class-tnet-admin-ajax.php';
require_once __DIR__ . '/admin/class-tnet-admin-page.php';

class Tnet_Admin
{
    private $plugin_name;
    private $version;
    private $updater;

    private $menu;
    private $settings;
    private $assets;
    private $ajax;
    private $page;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->updater = new Tnet_Updater($version);

        $this->menu = new Tnet_Admin_Menu();
        $this->settings = new Tnet_Admin_Settings();
        $this->assets = new Tnet_Admin_Assets($plugin_name, $version);
        $this->ajax = new Tnet_Admin_Ajax();
        $this->page = new Tnet_Admin_Page($version);
    }

    public function add_menu_page()
    {
        $this->menu->add_menu_page(array($this, 'render_admin_page'));
    }

    public function register_settings()
    {
        $this->settings->register();
    }

    public function render_admin_page()
    {
        $this->page->render();
    }

    public function enqueue_styles($hook)
    {
        $this->assets->enqueue_styles($hook);
    }

    public function enqueue_scripts($hook)
    {
        $this->assets->enqueue_scripts($hook);
    }

    public function handle_toggle()
    {
        $this->ajax->handle_toggle();
    }

    public function handle_test()
    {
        $this->ajax->handle_test();
    }
}
