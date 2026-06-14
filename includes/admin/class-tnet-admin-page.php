<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tnet_Admin_Page
{
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
    }

    public function render()
    {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
        $enabled    = get_option('tnet_enabled', 0);
        $backend_enabled = get_option('tnet_backend_enabled', 1);
        $sw_guarantee    = get_option('tnet_sw_guarantee', 0);

        $blocked_domains_frontend = get_option(
            'tnet_blocked_domains_frontend',
            'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
        );

        $current_version = $this->version;

        include __DIR__ . '/views/page.php';
    }
}
