<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handlers for the settings page (toggle save + requestor test).
 */
class Internet_Melli_Admin_Ajax
{
    public function handle_toggle()
    {
        check_ajax_referer('internet_melli_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('دسترسی غیرمجاز', 'internet-melli')));
        }



        $enabled = isset($_POST['enabled']) ? intval($_POST['enabled']) : 0;
        update_option('internet_melli_enabled', $enabled);

        $backend_enabled = isset($_POST['backend_enabled']) ? intval($_POST['backend_enabled']) : 0;
        update_option('internet_melli_backend_enabled', $backend_enabled);

        $sw_guarantee = isset($_POST['sw_guarantee']) ? intval($_POST['sw_guarantee']) : 0;
        update_option('internet_melli_sw_guarantee', $sw_guarantee);



        if (isset($_POST['blocked_domains_frontend'])) {
            $blocked_domains_frontend =  sanitize_textarea_field(wp_unslash($_POST['blocked_domains_frontend']));
            update_option('internet_melli_blocked_domains_frontend', $blocked_domains_frontend);
        }

        if (isset($_POST['blocked_domains_backend'])) {
            $backend_domains_backend = sanitize_text_field(wp_unslash($_POST['blocked_domains_backend']));
            update_option('internet_melli_blocked_domains_backend', $backend_domains_backend);
        }


        $sw_file = ABSPATH . 'sw.js';

        if ($sw_guarantee) {

            if (class_exists('Internet_Melli') && method_exists('Internet_Melli', 'generate_sw_content')) {

                $sw_content = Internet_Melli::generate_sw_content();
                $written = false;

                // ✅ روش اول: file_put_contents
                if (is_writable(ABSPATH) || (file_exists($sw_file) && is_writable($sw_file))) {
                    $result = @file_put_contents($sw_file, $sw_content);
                    if ($result !== false) {
                        $written = true;
                    }
                }

                // ✅ اگر روش اول شکست خورد → WP Filesystem
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

                // ✅ اگر هر دو روش شکست خورد
                if (! $written) {
                    wp_send_json_error(array(
                        'message' => __('امکان ایجاد فایل sw.js وجود ندارد. دسترسی نوشتن روی روت وردپرس بررسی شود.', 'internet-melli')
                    ));
                }
            }
        } else {

            $deleted = false;

            // ✅ حذف با روش معمولی
            if (file_exists($sw_file)) {
                if (@unlink($sw_file)) {
                    $deleted = true;
                }
            }

            // ✅ اگر حذف معمولی نشد → WP Filesystem
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





        // Flush rewrite rules
        flush_rewrite_rules();

        wp_send_json_success(array(
            'message' => __('تنظیمات ذخیره شد!', 'internet-melli'),
            'backend_enabled' => $backend_enabled,
            'enabled' => $enabled
        ));
    }

    public function handle_test()
    {
        check_ajax_referer('internet_melli_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('دسترسی غیرمجاز', 'internet-melli')));
        }

        $enabled = get_option('internet_melli_enabled', 0);
        $backend_enabled = get_option('internet_melli_backend_enabled', 0);

        wp_send_json_success(array(
            'enabled' => $enabled,
            'backend_enabled' => $backend_enabled,
            'message' => $enabled ? __('ریکوئستر فعال است', 'internet-melli') : __('ریکوئستر غیرفعال است', 'internet-melli')
        ));
    }
}
