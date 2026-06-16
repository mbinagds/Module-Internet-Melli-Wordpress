<?php

/**
 * Plugin Name:       TalashNet External Request Blocker
 * Plugin URI:        https://talashnet.com
 * Description:       Blocks outgoing requests to foreign domains to keep your WordPress site fast during Iran's National Internet disruptions. Supports backend blocking, frontend tag stripping, and an optional Service Worker layer.
 * Version:           1.4.2
 * Author:            mbinagds
 * Author URI:        https://talashnet.com
 * License:           GPL-2.0+
 * Text Domain:       talashnet-external-request-blocker
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin_data = get_plugin_data(__FILE__, false, false);
define('TNET_VERSION', $plugin_data['Version']);
define('TNET_PATH', plugin_dir_path(__FILE__));
define('TNET_URL', plugin_dir_url(__FILE__));

require_once TNET_PATH . 'includes/class-tnet-admin.php';
require_once TNET_PATH . 'includes/class-tnet-updater.php';
require_once TNET_PATH . 'includes/class-tnet-blocker.php';
require_once TNET_PATH . 'includes/class-tnet-remover.php';



class Tnet_Plugin
{
    private static $instance = null;
    private $admin;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->admin = new Tnet_Admin('talashnet-external-request-blocker', TNET_VERSION);

        add_action('init', [$this, 'init']);
        add_action('admin_menu', [$this->admin, 'add_menu_page']);
        add_action('admin_init', [$this->admin, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_scripts']);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_action('wp_ajax_tnet_send_feedback', array($this, 'tnet_send_feedback'));
        add_action('wp_ajax_nopriv_tnet_send_feedback', array($this, 'tnet_send_feedback'));

        add_action('init', [$this, 'serve_sw_js']);
        add_action('template_redirect', [$this, 'serve_sw_js']);

        // AJAX
        add_action('wp_ajax_tnet_toggle', [$this->admin, 'handle_toggle']);
        add_action('wp_ajax_tnet_test', [$this->admin, 'handle_test']);
        add_action('wp_ajax_tnet_check_update', 'tnet_check_update');
        add_action('wp_ajax_tnet_install_update', 'tnet_install_update');
        add_action('wp_ajax_tnet_delete_all', 'tnet_delete_all_data');

        add_action('admin_post_tnet_download_emergency_tool', [$this, 'tnet_download_emergency_tool']);

        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        if (class_exists('Tnet_Blocker')) {
            Tnet_Blocker::init();
        }
        if (class_exists('Tnet_Remover')) {
            Tnet_Remover::init();
        }
    }

    public function init()
    {
        load_plugin_textdomain('talashnet-external-request-blocker', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function tnet_download_emergency_tool()
    {
        $file_url  = 'http://mirror.talashnet.ir/wordpress/internet-melli/utility/direct-plugin-manager.txt';
        $file_name = 'direct-plugin-manager.php';

        $response = wp_remote_get(
            esc_url_raw($file_url),
            array(
                'timeout'     => 30,
                'redirection' => 5,
            )
        );

        if (is_wp_error($response)) {
            wp_die(esc_html($response->get_error_message()));
        }

        $response_code = (int) wp_remote_retrieve_response_code($response);
        $file_content  = wp_remote_retrieve_body($response);

        if (200 !== $response_code) {
            wp_die(esc_html(__('Download error. Response code: ', 'talashnet-external-request-blocker') . $response_code));
        }

        if ('' === $file_content) {
            wp_die(esc_html__('Downloaded file is empty.', 'talashnet-external-request-blocker'));
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        nocache_headers();
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . sanitize_file_name($file_name) . '"');
        header('Content-Length: ' . strlen($file_content));

        echo $file_content;
        exit;
    }

    public function enqueue_frontend_scripts()
    {
        $enabled = (int) get_option('tnet_enabled', 0);

        wp_register_script('tnet-sw-handler', false, [], TNET_VERSION, false);

        if (!$enabled) {
            $js = "if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(function(registrations) {
        for (var i = 0; i < registrations.length; i++) {
            registrations[i].unregister();
        }
    });
}";
        } else {
            $sw_url = home_url('sw.js?ver=' . TNET_VERSION);
            $js     = "if ('serviceWorker' in navigator) {
    var isControlled = navigator.serviceWorker.controller !== null;
    navigator.serviceWorker.register('" . esc_js($sw_url) . "', { scope: '/' })
        .then(function(registration) { return navigator.serviceWorker.ready; })
        .then(function(registration) {
            if (!navigator.serviceWorker.controller || !isControlled) {
                window.location.reload();
            }
        })
        .catch(function(err) {
            console.log('TalashNet ERB SW registration failed:', err);
        });
}";
        }

        wp_add_inline_script('tnet-sw-handler', $js);
        wp_enqueue_script('tnet-sw-handler');
    }

    public function tnet_send_feedback()
    {
        if (
            !isset($_POST['tnet_feedback_nonce']) ||
            !wp_verify_nonce($_POST['tnet_feedback_nonce'], 'tnet_nonce')
        ) {
            wp_send_json_error(array('message' => __('Nonce verification failed.', 'talashnet-external-request-blocker')));
        }

        $text = isset($_POST['text']) ? sanitize_textarea_field($_POST['text']) : '';
        $user = isset($_POST['user']) ? sanitize_text_field($_POST['user']) : '';

        if (empty($text)) {
            wp_send_json_error(array('message' => __('Feedback text is empty.', 'talashnet-external-request-blocker')));
        }

        $payload = array(
            'user' => $user,
            'text' => $text,
            'time' => current_time('mysql')
        );

        $response = wp_remote_post(
            'http://mirror.talashnet.ir/wordpress/internet-melli/feedback/get.php',
            array(
                'timeout' => 15,
                'body'    => $payload
            )
        );

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => __('Could not connect to the feedback server.', 'talashnet-external-request-blocker')));
        }

        $status = wp_remote_retrieve_response_code($response);

        if ($status >= 200 && $status < 300) {
            wp_send_json_success(array('message' => __('Feedback submitted successfully.', 'talashnet-external-request-blocker')));
        } else {
            wp_send_json_error(array(
                'message' => __('Feedback submission failed.', 'talashnet-external-request-blocker') . ' (' . $status . ')'
            ));
        }
    }

    public static function generate_sw_content()
    {
        $blocked_domains = get_option(
            'tnet_blocked_domains_frontend',
            'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
        );

        $domains_array = array_map('trim', explode(',', $blocked_domains));
        $domains_json  = json_encode($domains_array);

        $version = TNET_VERSION;

        return "
const blocked = {$domains_json};
const SW_VERSION = '{$version}';

self.addEventListener('install', (event) => {
    self.skipWaiting();
    console.log('TalashNet ERB SW v' + SW_VERSION + ' installed');
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        clients.claim().then(() => {
            console.log('TalashNet ERB SW v' + SW_VERSION + ' activated');
        })
    );
});

self.addEventListener('fetch', function(event) {
    const url = event.request.url;

    for (let b of blocked) {
        if (url.includes(b)) {
            event.respondWith(new Response('', {status: 403}));
            return;
        }
    }
});
";
    }

    public function serve_sw_js()
    {
        if (isset($_GET['sw']) && $_GET['sw'] === 'talashnet-external-request-blocker') {
            header('Content-Type: application/javascript; charset=utf-8');
            echo self::generate_sw_content();
            exit;
        }

        add_rewrite_rule('^sw\.js$', '?sw=talashnet-external-request-blocker', 'top');
    }

    public function deactivate()
    {
        flush_rewrite_rules();
    }
}




// ==========================================
// AJAX Handlers for Plugin Update
// ==========================================

function tnet_check_update()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tnet_nonce')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('Security error.', 'talashnet-external-request-blocker')
        ));
    }

    if (!current_user_can('manage_options')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('Unauthorized access.', 'talashnet-external-request-blocker')
        ));
    }

    $plugin_data     = get_plugin_data(TNET_PATH . 'talashnet-external-request-blocker.php');
    $current_version = $plugin_data['Version'];

    $updater = new Tnet_Updater($current_version);
    $result  = $updater->check_for_update();

    wp_send_json($result);
}

function tnet_install_update()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tnet_nonce')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('Security error.', 'talashnet-external-request-blocker')
        ));
    }

    if (!current_user_can('install_plugins')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('Unauthorized access.', 'talashnet-external-request-blocker')
        ));
    }

    $download_url = isset($_POST['download_url']) ? esc_url_raw($_POST['download_url']) : '';

    if (empty($download_url)) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('Download URL not found.', 'talashnet-external-request-blocker')
        ));
    }

    $plugin_data     = get_plugin_data(TNET_PATH . 'talashnet-external-request-blocker.php');
    $current_version = $plugin_data['Version'];

    $updater = new Tnet_Updater($current_version);
    $result  = $updater->download_and_install($download_url);

    wp_send_json($result);
}


function tnet_delete_all_data()
{
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array(
            'message' => __('Unauthorized access.', 'talashnet-external-request-blocker')
        ));
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tnet_nonce')) {
        wp_send_json_error(array(
            'message' => __('Invalid security token.', 'talashnet-external-request-blocker')
        ));
    }

    delete_option('tnet_enabled');
    delete_option('tnet_backend_enabled');
    delete_option('tnet_blocked_domains_frontend');
    delete_option('tnet_blocked_domains_backend');
    delete_option('tnet_version');
    delete_option('tnet_sw_cache_version');
    delete_option('tnet_sw_guarantee');

    $sw_file = ABSPATH . 'sw.js';
    if (file_exists($sw_file)) {
        unlink($sw_file);
    }

    wp_send_json_success(array(
        'message' => __('All plugin data deleted successfully.', 'talashnet-external-request-blocker')
    ));
}




Tnet_Plugin::get_instance();

register_activation_hook(__FILE__, function () {
    if (false === get_option('tnet_enabled')) {
        update_option('tnet_enabled', 0);
    }
    if (false === get_option('tnet_backend_enabled')) {
        update_option('tnet_backend_enabled', 1);
    }

    add_rewrite_rule('^sw\.js$', '?sw=talashnet-external-request-blocker', 'top');
    flush_rewrite_rules();
});
