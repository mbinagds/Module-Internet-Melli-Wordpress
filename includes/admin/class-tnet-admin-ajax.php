<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tnet_Admin_Ajax
{
    public function handle_toggle()
    {
        check_ajax_referer('tnet_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'talashnet-external-request-blocker')));
        }

        $enabled = isset($_POST['enabled']) ? absint(wp_unslash($_POST['enabled'])) : 0;
        update_option('tnet_enabled', $enabled);

        $backend_enabled = isset($_POST['backend_enabled']) ? absint(wp_unslash($_POST['backend_enabled'])) : 0;
        update_option('tnet_backend_enabled', $backend_enabled);

        $sw_guarantee = isset($_POST['sw_guarantee']) ? absint(wp_unslash($_POST['sw_guarantee'])) : 0;
        update_option('tnet_sw_guarantee', $sw_guarantee);

        if (isset($_POST['blocked_domains_frontend'])) {
            $blocked_domains_frontend = sanitize_textarea_field(wp_unslash($_POST['blocked_domains_frontend']));
            update_option('tnet_blocked_domains_frontend', $blocked_domains_frontend);
        }

        if (isset($_POST['blocked_domains_backend'])) {
            $raw_backend = wp_unslash($_POST['blocked_domains_backend']);
            $decoded_backend = json_decode($raw_backend, true);
            $safe_backend = is_array($decoded_backend) ? wp_json_encode($decoded_backend) : '[]';
            update_option('tnet_blocked_domains_backend', $safe_backend);
        }

        $sw_file = ABSPATH . 'sw.js';

        if ($sw_guarantee) {

            if (class_exists('Tnet_Plugin') && method_exists('Tnet_Plugin', 'generate_sw_content')) {

                $sw_content = Tnet_Plugin::generate_sw_content();
                $written = false;

                if (is_writable(ABSPATH) || (file_exists($sw_file) && is_writable($sw_file))) {
                    $result = @file_put_contents($sw_file, $sw_content);
                    if ($result !== false) {
                        $written = true;
                    }
                }

                if (! $written) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    $creds = request_filesystem_credentials('', '', false, false, null);

                    if (WP_Filesystem($creds)) {
                        global $wp_filesystem;
                        if ($wp_filesystem->put_contents($sw_file, $sw_content, FS_CHMOD_FILE)) {
                            $written = true;
                        }
                    }
                }

                if (! $written) {
                    wp_send_json_error(array(
                        'message' => __('Cannot write sw.js. Check write permissions on the WordPress root.', 'talashnet-external-request-blocker')
                    ));
                }
            }
        } else {

            $deleted = false;

            if (file_exists($sw_file)) {
                if (@unlink($sw_file)) {
                    $deleted = true;
                }
            }

            if (! $deleted && file_exists($sw_file)) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                $creds = request_filesystem_credentials('', '', false, false, null);

                if (WP_Filesystem($creds)) {
                    global $wp_filesystem;
                    if ($wp_filesystem->delete($sw_file)) {
                        $deleted = true;
                    }
                }
            }
        }

        flush_rewrite_rules();

        wp_send_json_success(array(
            'message'         => __('Settings saved!', 'talashnet-external-request-blocker'),
            'backend_enabled' => $backend_enabled,
            'enabled'         => $enabled
        ));
    }

    public function handle_test()
    {
        check_ajax_referer('tnet_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Unauthorized access.', 'talashnet-external-request-blocker')));
        }

        $enabled         = get_option('tnet_enabled', 0);
        $backend_enabled = get_option('tnet_backend_enabled', 0);

        wp_send_json_success(array(
            'enabled'         => $enabled,
            'backend_enabled' => $backend_enabled,
            'message'         => $enabled
                ? __('Service Worker is active.', 'talashnet-external-request-blocker')
                : __('Service Worker is inactive.', 'talashnet-external-request-blocker')
        ));
    }
}
