<?php

/**
 * Admin coordinator for the Internet Melli plugin.
 *
 * Preserves the original public API (the methods internet-melli.php wires to
 * WordPress hooks) and delegates each responsibility to a focused module under
 * includes/admin/:
 *   - Internet_Melli_Admin_Menu     menu + submenu registration
 *   - Internet_Melli_Admin_Settings Settings API registration
 *   - Internet_Melli_Admin_Assets   CSS/JS enqueue + localization
 *   - Internet_Melli_Admin_Ajax     AJAX toggle/test handlers
 *   - Internet_Melli_Admin_Page      settings page rendering (view partials)
 *   - Internet_Melli_Admin_Svg       inline SVG helper
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/admin/class-internet-melli-admin-svg.php';
require_once __DIR__ . '/admin/class-internet-melli-admin-menu.php';
require_once __DIR__ . '/admin/class-internet-melli-admin-settings.php';
require_once __DIR__ . '/admin/class-internet-melli-admin-assets.php';
require_once __DIR__ . '/admin/class-internet-melli-admin-ajax.php';
require_once __DIR__ . '/admin/class-internet-melli-admin-page.php';

class Internet_Melli_Admin
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
        $this->updater = new Internet_Melli_Updater($version);

        $this->menu = new Internet_Melli_Admin_Menu();
        $this->settings = new Internet_Melli_Admin_Settings();
        $this->assets = new Internet_Melli_Admin_Assets($plugin_name, $version);
        $this->ajax = new Internet_Melli_Admin_Ajax();
        $this->page = new Internet_Melli_Admin_Page($version);
    }

    /**
     * Add admin menu page
     */
    public function add_menu_page()
    {
        $this->menu->add_menu_page(array($this, 'render_admin_page'));
    }

    /**
     * Register settings
     */
    public function register_settings()
    {
        $this->settings->register();
    }

    /**
     * Render admin page
     */
    public function render_admin_page()
    {
        $this->page->render();
    }

    /**
     * Enqueue admin styles
     */
    public function enqueue_styles($hook)
    {
        $this->assets->enqueue_styles($hook);
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook)
    {
        $this->assets->enqueue_scripts($hook);
    }

    /**
     * Handle AJAX toggle request
     */
    public function handle_toggle()
    {
        $this->ajax->handle_toggle();
    }

    /**
     * Handle AJAX test request
     */
    public function handle_test()
    {
        $this->ajax->handle_test();
    }
}
