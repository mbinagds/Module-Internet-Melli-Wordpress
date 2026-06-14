<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tnet_Admin_Assets
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    public function enqueue_styles($hook)
    {
        if ('toplevel_page_talashnet-external-request-blocker' !== $hook) {
            return;
        }
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            TNET_URL . 'assets/css/admin-style.css',
            array(),
            $this->version
        );
    }

    public function enqueue_scripts($hook)
    {
        if ('toplevel_page_talashnet-external-request-blocker' !== $hook) {
            return;
        }
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            TNET_URL . 'assets/js/admin-settings.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-admin',
            'tnetPlugin',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('tnet_nonce'),
                'strings'  => array(
                    'saving'           => __('Saving...', 'talashnet-external-request-blocker'),
                    'saved'            => __('Settings saved successfully!', 'talashnet-external-request-blocker'),
                    'error'            => __('An error occurred. Please try again.', 'talashnet-external-request-blocker'),
                    'testing'          => __('Testing...', 'talashnet-external-request-blocker'),
                    'testsw'           => __('Test Status', 'talashnet-external-request-blocker'),
                    'test_success'     => __('Service Worker is active!', 'talashnet-external-request-blocker'),
                    'test_inactive'    => __('Service Worker is not active.', 'talashnet-external-request-blocker'),
                    'domain_empty'     => __('Please enter a domain.', 'talashnet-external-request-blocker'),
                    'domain_invalid'   => __('Please enter a valid domain.', 'talashnet-external-request-blocker'),
                    'domain_exists'    => __('This domain has already been added.', 'talashnet-external-request-blocker'),
                    'domain_added'     => __('Domain added.', 'talashnet-external-request-blocker'),
                    'domain_removed'   => __('Domain removed.', 'talashnet-external-request-blocker'),
                    'domain_updated'   => __('Domain updated.', 'talashnet-external-request-blocker'),
                    'no_domains'       => __('No domains added yet.', 'talashnet-external-request-blocker'),
                    'checking'         => __('Checking...', 'talashnet-external-request-blocker'),
                    'check_update'     => __('Check for Updates', 'talashnet-external-request-blocker'),
                    'install_update'   => __('Download and Install Update', 'talashnet-external-request-blocker'),
                    'update_confirm'   => __('Are you sure you want to update the plugin?', 'talashnet-external-request-blocker'),
                    'update_available' => __('New version available!', 'talashnet-external-request-blocker'),
                    'no_update'        => __('You are using the latest version.', 'talashnet-external-request-blocker'),
                    'release_notes'    => __('Changelog:', 'talashnet-external-request-blocker'),
                    'updating'         => __('Updating...', 'talashnet-external-request-blocker'),
                    'no_download_url'  => __('Download URL not found.', 'talashnet-external-request-blocker'),
                    'blocked_domains'  => __('Blocked Domains', 'talashnet-external-request-blocker'),
                    'count_unit'       => __('items', 'talashnet-external-request-blocker'),
                )
            )
        );
    }
}
