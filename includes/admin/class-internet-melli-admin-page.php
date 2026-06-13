<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renders the admin settings page by composing the view partials
 * under admin/views/. Holds no business logic — just the data the
 * views need, then includes the top-level page template.
 */
class Internet_Melli_Admin_Page
{
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Render the whole admin page (both tabs).
     */
    public function render()
    {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';
        $enabled = get_option('internet_melli_enabled', 0);
        $backend_enabled = get_option('internet_melli_backend_enabled', 1);
        $sw_guarantee = get_option('internet_melli_sw_guarantee', 0);

        $blocked_domains_frontend = get_option(
            'internet_melli_blocked_domains_frontend',
            'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
        );

        $current_version = $this->version;

        include __DIR__ . '/views/page.php';
    }
}
