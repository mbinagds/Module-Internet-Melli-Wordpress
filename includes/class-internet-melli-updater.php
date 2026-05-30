<?php
/**
 * Updater Class for Internet Melli Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Internet_Melli_Updater {

    private $update_url = 'https://mirror.talashnet.ir/wordpress/internet-melli/update.json';
    private $current_version;
    private $plugin_slug = 'internet-melli';

    public function __construct($current_version) {
        $this->current_version = $current_version;
    }

    public function check_for_update() {
        $response = wp_remote_get($this->update_url, array(
            'timeout'   => 15,
            'sslverify' => false
        ));

        if (is_wp_error($response)) {
            return array(
                'status'  => 'error',
                'message' => __('خطا در اتصال به سرور: ', 'internet-melli') . $response->get_error_message()
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['version'])) {
            return array(
                'status'  => 'error',
                'message' => __('پاسخ نامعتبر از سرور', 'internet-melli')
            );
        }

        $has_update = version_compare($data['version'], $this->current_version, '>');

        return array(
            'status'         => 'success',
            'has_update'     => $has_update,
            'new_version'    => $data['version'],
            'download_url'   => $data['download_url'] ?? '',
            'release_notes'  => $data['release_notes'] ?? '',
            'current_version'=> $this->current_version
        );
    }

    public function download_and_install($download_url) {
        if (!current_user_can('install_plugins')) {
            return array('status' => 'error', 'message' => __('دسترسی ندارید', 'internet-melli'));
        }

        if (empty($download_url)) {
            return array('status' => 'error', 'message' => __('لینک خالی است', 'internet-melli'));
        }

        // دریافت نسخه جدید از API
        $api_response = $this->check_for_update();
        $new_version = $api_response['new_version'] ?? 'unknown';

        // دانلود فایل
        $response = wp_remote_get($download_url, array(
            'timeout'   => 60,
            'sslverify' => false,
            'stream'    => true,
            'filename'  => WP_CONTENT_DIR . '/temp-update.zip'
        ));

        if (is_wp_error($response)) {
            return array('status' => 'error', 'message' => __('خطا در دانلود: ', 'internet-melli') . $response->get_error_message());
        }

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return array('status' => 'error', 'message' => __('دانلود ناموفق', 'internet-melli'));
        }

        $download_file = WP_CONTENT_DIR . '/temp-update.zip';
        $plugin_dir = WP_PLUGIN_DIR . '/' . $this->plugin_slug;

        if (!file_exists($plugin_dir)) {
            @unlink($download_file);
            return array('status' => 'error', 'message' => __('پوشه یافت نشد', 'internet-melli'));
        }

        // بکاپ
        $backup_dir = WP_CONTENT_DIR . '/plugin-backups/' . $this->plugin_slug . '-' . date('Y-m-d-H-i-s');
        if (!is_dir(dirname($backup_dir))) {
            mkdir(dirname($backup_dir), 0755, true);
        }
        $this->recurse_copy($plugin_dir, $backup_dir);

        // حذف فایل‌های قبلی
        $this->delete_directory($plugin_dir);

        // استخراج
        $result = $this->unzip_plugin($download_file, $plugin_dir);
        @unlink($download_file);

        if (!$result['status']) {
            $this->delete_directory($plugin_dir);
            $this->recurse_copy($backup_dir, $plugin_dir);
            return $result;
        }

        wp_clean_plugins_cache();

        // استفاده از نسخه API به جای فایل
        return array(
            'status'      => 'success',
            'message'     => sprintf(__('افزونه با موفقیت به نسخه %s آپدیت شد', 'internet-melli'), $new_version),
            'new_version' => $new_version,
            'backup_dir'  => $backup_dir
        );
    }

    private function unzip_plugin($zip_file, $destination) {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        WP_Filesystem();

        if (!is_object($wp_filesystem) || !$wp_filesystem->exists($zip_file)) {
            return array('status' => 'error', 'message' => __('خطا در فایل سیستم', 'internet-melli'));
        }

        $temp_dir = WP_CONTENT_DIR . '/temp-unzip-' . time();
        wp_mkdir_p($temp_dir);

        $unzipresult = unzip_file($zip_file, $temp_dir);

        if (is_wp_error($unzipresult)) {
            $this->delete_directory($temp_dir);
            return array('status' => 'error', 'message' => __('خطا در استخراج: ', 'internet-melli') . $unzipresult->get_error_message());
        }

        $source_dir = $this->find_correct_plugin_folder($temp_dir);

        if (!$source_dir) {
            $this->delete_directory($temp_dir);
            return array('status' => 'error', 'message' => __('پوشه یافت نشد', 'internet-melli'));
        }

        $this->recurse_copy($source_dir, $destination);
        $this->delete_directory($temp_dir);

        return array('status' => 'success', 'version' => 'ok', 'destination' => $destination);
    }

    private function find_correct_plugin_folder($temp_dir) {
        $items = scandir($temp_dir);
        $dirs = array_filter($items, function($item) use ($temp_dir) {
            return is_dir($temp_dir . '/' . $item) && $item !== '.' && $item !== '..';
        });
        $dirs = array_values($dirs);

        if (count($dirs) === 1 && $dirs[0] === $this->plugin_slug) {
            return $temp_dir . '/' . $dirs[0];
        }

        if (count($dirs) === 1) {
            $inner_dir = $temp_dir . '/' . $dirs[0];
            $inner_items = scandir($inner_dir);
            $inner_dirs = array_filter($inner_items, function($item) use ($inner_dir) {
                return is_dir($inner_dir . '/' . $item) && $item !== '.' && $item !== '..';
            });
            $inner_dirs = array_values($inner_dirs);

            if (count($inner_dirs) === 1 && $inner_dirs[0] === $this->plugin_slug) {
                return $inner_dir . '/' . $inner_dirs[0];
            }

            if (count($inner_dirs) === 1) {
                return $inner_dir . '/' . $inner_dirs[0];
            }
        }

        $php_files = glob($temp_dir . '/*.php');
        if (!empty($php_files)) {
            return $temp_dir;
        }

        return !empty($dirs) ? $temp_dir . '/' . $dirs[0] : null;
    }

    private function recurse_copy($src, $dst) {
        if (!is_dir($src)) {
            return;
        }

        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function delete_directory($dir) {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            $path = "$dir/$file";
            if (is_dir($path)) {
                $this->delete_directory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}