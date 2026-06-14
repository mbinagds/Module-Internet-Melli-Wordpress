<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tnet_Admin_Menu
{
    /**
     * @param callable $render_callback Render callback used for every menu entry.
     */
    public function add_menu_page($render_callback)
    {
        add_menu_page(
            __('TalashNet External Request Blocker', 'talashnet-external-request-blocker'),
            __('External Blocker', 'talashnet-external-request-blocker'),
            'manage_options',
            'talashnet-external-request-blocker',
            $render_callback,
            'dashicons-lock',
            100
        );

        add_submenu_page(
            'talashnet-external-request-blocker',
            __('Plugin Settings', 'talashnet-external-request-blocker'),
            __('Plugin Settings', 'talashnet-external-request-blocker'),
            'manage_options',
            'talashnet-external-request-blocker',
            $render_callback
        );

        add_submenu_page(
            'talashnet-external-request-blocker',
            __('Emergency Plugin Activation', 'talashnet-external-request-blocker'),
            __('Emergency Activation', 'talashnet-external-request-blocker'),
            'manage_options',
            'talashnet-external-request-blocker&tab=active-plugins',
            $render_callback
        );

        add_filter('submenu_file', function ($submenu_file) {
            global $plugin_page;
            if ($plugin_page == 'talashnet-external-request-blocker') {
                $tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
                if ($tab == 'active-plugins') {
                    return 'talashnet-external-request-blocker&tab=active-plugins';
                }
                return 'talashnet-external-request-blocker';
            }
            return $submenu_file;
        });
    }
}
