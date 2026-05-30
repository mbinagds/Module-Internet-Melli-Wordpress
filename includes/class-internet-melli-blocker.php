<?php
if (! defined('ABSPATH')) {
    exit;
}

class Internet_Melli_Blocker
{

    const OPTION_BACKEND_DOMAINS = 'internet_melli_blocked_domains_backend';

    public static function init()
    {

        if (! (int) get_option('internet_melli_backend_enabled', 0)) {
            return;
        }

        // 1) Auto-add دامنه‌های جدید از همه ریکوئست‌ها
        add_action('http_api_debug', array(__CLASS__, 'log_and_learn_domains'), 10, 5);

        // 2) بلاک کردن بر اساس لیست backend
        add_filter('pre_http_request', array(__CLASS__, 'block_by_backend_list'), 10, 3);

        // 3) (اختیاری) تزریق لیست backend به Snitch
        add_filter('snitch_inspect_request_hosts', array(__CLASS__, 'filter_snitch_hosts_from_backend'));
    }

    /**
     * گرفتن آدرس host از URL
     */
    protected static function get_host_from_url($url)
    {
        if (empty($url)) {
            return false;
        }

        $host = wp_parse_url($url, PHP_URL_HOST);

        // پاک کردن www.
        if ($host && 0 === strpos($host, 'www.')) {
            $host = substr($host, 4);
        }

        return $host;
    }

    /**
     * گرفتن لیست backend از option، به شکل array
     * [
     *   [ 'domain' => 'example.com', 'enabled' => true ],
     *   ...
     * ]
     */
    public static function get_backend_domains()
    {
        $json = get_option(self::OPTION_BACKEND_DOMAINS, '[]');
        $data = json_decode($json, true);

        if (! is_array($data)) {
            $data = array();
        }

        return $data;
    }

    /**
     * ذخیره‌کردن لیست backend در option
     */
    public static function save_backend_domains(array $domains)
    {
        update_option(self::OPTION_BACKEND_DOMAINS, wp_json_encode($domains));
    }

    /**
     * Auto-learn دامنه‌ها: هر بار response می‌آید، host را در backend ثبت می‌کنیم.
     */
    public static function log_and_learn_domains($response, $context, $class, $args, $url)
    {

        // فقط روی context = response کار می‌کنیم
        if ($context !== 'response' || empty($url)) {
            return;
        }

        $host = self::get_host_from_url($url);
        if (! $host) {
            return;
        }

        $site_host = parse_url(home_url(), PHP_URL_HOST);
        $whitelist = array(
            'talashnet.com',
            'mirror.talashnet.ir',
            $site_host,
        );

        if (in_array($host, $whitelist, true)) {
            return;
        }

        $domains = self::get_backend_domains();

        // آیا قبلاً ثبت شده؟
        foreach ($domains as $item) {
            // برای backward compatibility: اگر 'domain' تعریف نشده بود، skip
            if (! isset($item['domain'])) {
                continue;
            }

            if ($item['domain'] === $host) {
                return; // موجود است، کاری نکن
            }
        }

        // دامنه جدید → اضافه با enabled = true
        $domains[] = array(
            'domain'  => $host,
            'enabled' => true,
        );

        self::save_backend_domains($domains);
    }

    /**
     * بلاک کردن ریکوئست قبل از ارسال، بر اساس لیست backend
     */
    public static function block_by_backend_list($preempt, $args, $url)
    {

        // اگر قبلاً چیزی preload کرده، احترام می‌گذاریم
        if (! empty($preempt) && is_wp_error($preempt)) {
            return $preempt;
        }

        $host = self::get_host_from_url($url);
        if (! $host) {
            return $preempt;
        }

        $site_host = parse_url(home_url(), PHP_URL_HOST);
        $whitelist = array(
            'talashnet.com',
            'mirror.talashnet.ir',
            $site_host,
        );

        if (in_array($host, $whitelist, true)) {
            return $preempt;
        }

        $domains = self::get_backend_domains();

        foreach ($domains as $item) {
            if (empty($item['domain'])) {
                continue;
            }

            $enabled = isset($item['enabled']) ? (bool) $item['enabled'] : true;

            if ($item['domain'] === $host && $enabled) {
                // بلاک کن
                return new WP_Error(
                    'internet_melli_blocked',
                    sprintf(
                        /* translators: %s: host name */
                        __('Request to %s blocked by Internet Melli backend list.', 'internet-melli'),
                        esc_html($host)
                    )
                );
            }
        }

        return $preempt;
    }

    /**
     * (اختیاری) پرکردن لیست Snitch hosts از روی backend تو
     * این باعث می‌شود Snitch هم همان دامنه‌ها را بلاک کند.
     */
    public static function filter_snitch_hosts_from_backend($hosts)
    {

        $domains = self::get_backend_domains();

        foreach ($domains as $item) {
            if (empty($item['domain'])) {
                continue;
            }

            $enabled = isset($item['enabled']) ? (bool) $item['enabled'] : true;

            if ($enabled) {
                $hosts[] = $item['domain'];
            }
        }

        // حذف مقادیر تکراری
        $hosts = array_values(array_unique($hosts));

        return $hosts;
    }
}
