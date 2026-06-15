<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tnet_Updater {

    private $update_url = 'http://mirror.talashnet.ir/wordpress/internet-melli/updater/update-api.php';

    private $current_version;
    private $plugin_slug = 'talashnet-external-request-blocker';

    public function __construct($current_version) {
        $this->current_version = $current_version;
    }

    public function check_for_update() {
        $query_params = http_build_query([
            'site_url' => get_site_url(),
            'version'  => $this->current_version,
            'slug'     => $this->plugin_slug
        ]);

        $api_url = $this->update_url . '?' . $query_params;

        $response = wp_remote_get($api_url, array(
            'timeout'   => 15,
            'sslverify' => false
        ));

        if (is_wp_error($response)) {
            return array(
                'status'  => 'error',
                'message' => __('Connection error: ', 'talashnet-external-request-blocker') . $response->get_error_message()
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['version'])) {
            return array(
                'status'  => 'error',
                'message' => __('Invalid response from server.', 'talashnet-external-request-blocker')
            );
        }

        $has_update = version_compare($data['version'], $this->current_version, '>');

        return array(
            'status'          => 'success',
            'has_update'      => $has_update,
            'new_version'     => $data['version'],
            'download_url'    => $data['download_url'] ?? '',
            'release_notes'   => $data['release_notes'] ?? '',
            'current_version' => $this->current_version
        );
    }

    public function download_and_install($download_url) {
        if (!current_user_can('install_plugins')) {
            return array('status' => 'error', 'message' => __('You do not have permission to install plugins.', 'talashnet-external-request-blocker'));
        }

        if (empty($download_url)) {
            return array('status' => 'error', 'message' => __('Download URL is empty.', 'talashnet-external-request-blocker'));
        }

        $api_response = $this->check_for_update();
        $new_version = $api_response['new_version'] ?? 'unknown';

        $response = wp_remote_get($download_url, array(
            'timeout'   => 60,
            'sslverify' => false,
            'stream'    => true,
            'filename'  => WP_CONTENT_DIR . '/temp-update.zip'
        ));

        if (is_wp_error($response)) {
            return array('status' => 'error', 'message' => __('Download error: ', 'talashnet-external-request-blocker') . $response->get_error_message());
        }

        if (wp_remote_retrieve_response_code($response) !== 200) {
            return array('status' => 'error', 'message' => __('Download failed.', 'talashnet-external-request-blocker'));
        }

        $download_file = WP_CONTENT_DIR . '/temp-update.zip';
        $plugin_dir = WP_PLUGIN_DIR . '/' . $this->plugin_slug;

        if (!file_exists($plugin_dir)) {
            @unlink($download_file);
            return array('status' => 'error', 'message' => __('Plugin directory not found.', 'talashnet-external-request-blocker'));
        }

        $backup_dir = WP_CONTENT_DIR . '/plugin-backups/' . $this->plugin_slug . '-' . date('Y-m-d-H-i-s');
        if (!is_dir(dirname($backup_dir))) {
            mkdir(dirname($backup_dir), 0755, true);
        }
        $this->recurse_copy($plugin_dir, $backup_dir);

        $this->delete_directory($plugin_dir);

        $result = $this->unzip_plugin($download_file, $plugin_dir);
        @unlink($download_file);

        if ($result['status'] !== 'success') {
            $this->delete_directory($plugin_dir);
            $this->recurse_copy($backup_dir, $plugin_dir);
            return $result;
        }

        wp_clean_plugins_cache();

        return array(
            'status'      => 'success',
            'message'     => sprintf(__('Plugin successfully updated to version %s.', 'talashnet-external-request-blocker'), $new_version),
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
            return array('status' => 'error', 'message' => __('Filesystem error.', 'talashnet-external-request-blocker'));
        }

        $temp_dir = WP_CONTENT_DIR . '/temp-unzip-' . time();
        wp_mkdir_p($temp_dir);

        $unzipresult = unzip_file($zip_file, $temp_dir);

        if (is_wp_error($unzipresult)) {
            $this->delete_directory($temp_dir);
            return array('status' => 'error', 'message' => __('Extraction error: ', 'talashnet-external-request-blocker') . $unzipresult->get_error_message());
        }

        $source_dir = $this->find_correct_plugin_folder($temp_dir);

        if (!$source_dir) {
            $this->delete_directory($temp_dir);
            return array('status' => 'error', 'message' => __('Plugin folder not found in archive.', 'talashnet-external-request-blocker'));
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
