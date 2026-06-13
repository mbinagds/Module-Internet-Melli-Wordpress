<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueues admin CSS/JS and localizes strings for the settings page.
 */
class Internet_Melli_Admin_Assets
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles($hook)
    {
        if ('toplevel_page_internet-melli' !== $hook) {
            return;
        }
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            INTERNET_MELLI_URL . 'assets/css/admin-style.css',
            array(),
            $this->version
        );
    }

    public function enqueue_scripts($hook)
    {
        if ('toplevel_page_internet-melli' !== $hook) {
            return;
        }
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            INTERNET_MELLI_URL . 'assets/js/admin-settings.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name . '-admin',
            'internetMelli',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('internet_melli_nonce'),
                'strings' => array(
                    'saving' => __('در حال ذخیره...', 'internet-melli'),
                    'saved' => __('تنظیمات با موفقیت ذخیره شد!', 'internet-melli'),
                    'error' => __('خطایی رخ داد. لطفا دوباره تلاش کنید.', 'internet-melli'),
                    'testing' => __('در حال تست...', 'internet-melli'),
                    'testsw' => __('تست وضعیت', 'internet-melli'),
                    'test_success' => __('ریکوئستر فعال است!', 'internet-melli'),
                    'test_inactive' => __('ریکوئستر فعال نیست.', 'internet-melli'),
                    'domain_empty' => __('لطفاً یک دامنه وارد کنید', 'internet-melli'),
                    'domain_invalid' => __('لطفاً یک دامنه معتبر وارد کنید', 'internet-melli'),
                    'domain_exists' => __('این دامنه قبلاً اضافه شده است', 'internet-melli'),
                    'domain_added' => __('دامنه اضافه شد', 'internet-melli'),
                    'domain_removed' => __('دامنه حذف شد', 'internet-melli'),
                    'domain_updated' => __('دامنه ویرایش شد', 'internet-melli'),
                    'no_domains' => __('هنوز دامنه‌ای اضافه نشده است', 'internet-melli'),
                    'checking'        => __('در حال بررسی...', 'internet-melli'),
                    'check_update'    => __('بررسی آپدیت', 'internet-melli'),
                    'install_update'  => __('دانلود و نصب آپدیت', 'internet-melli'),
                    'update_confirm'  => __('آیا مطمئن هستید که می‌خواهید افزونه را آپدیت کنید؟', 'internet-melli'),
                    'update_available' => __('نسخه جدید یافت شد!', 'internet-melli'),
                    'no_update'       => __('شما از آخرین نسخه استفاده می‌کنید', 'internet-melli'),
                    'release_notes'   => __('تغییرات:', 'internet-melli'),
                    'updating'        => __('در حال آپدیت...', 'internet-melli'),
                    'no_download_url' => __('لینک دانلود یافت نشد', 'internet-melli'),
                    'blocked_domains'   => __('دامنه‌های مسدود شده', 'internet-melli'),
                    'count_unit'        => __('عدد', 'internet-melli'),
                )
            )
        );
    }
}
