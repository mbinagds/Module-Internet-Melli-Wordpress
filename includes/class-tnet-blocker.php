<?php
if (! defined('ABSPATH')) {
    exit;
}

class Tnet_Blocker
{

    const OPTION_BACKEND_DOMAINS = 'tnet_blocked_domains_backend';

    public static function init()
    {

        if ((string) get_option('tnet_backend_enabled', '1') === '0') {
            return;
        }

        add_action('http_api_debug', array(__CLASS__, 'log_and_learn_domains'), 10, 5);
        add_filter('pre_http_request', array(__CLASS__, 'block_by_backend_list'), 10, 3);
        add_filter('snitch_inspect_request_hosts', array(__CLASS__, 'filter_snitch_hosts_from_backend'));
    }

    protected static function get_host_from_url($url)
    {
        if (empty($url)) {
            return false;
        }

        $host = wp_parse_url($url, PHP_URL_HOST);

        if ($host && 0 === strpos($host, 'www.')) {
            $host = substr($host, 4);
        }

        return $host;
    }

    public static function get_backend_domains()
    {
        $json = get_option(self::OPTION_BACKEND_DOMAINS, '[]');
        $data = json_decode($json, true);

        if (! is_array($data)) {
            $data = array();
        }

        return $data;
    }

    public static function save_backend_domains(array $domains)
    {
        update_option(self::OPTION_BACKEND_DOMAINS, wp_json_encode($domains));
    }

    public static function log_and_learn_domains($response, $context, $class, $args, $url)
    {

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

        foreach ($domains as $item) {
            if (! isset($item['domain'])) {
                continue;
            }

            if ($item['domain'] === $host) {
                return;
            }
        }

        $domains[] = array(
            'domain'  => $host,
            'enabled' => true,
        );

        self::save_backend_domains($domains);
    }

    public static function block_by_backend_list($preempt, $args, $url)
    {

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
                return new WP_Error(
                    'tnet_blocked',
                    sprintf(
                        /* translators: %s: host name */
                        __('Request to %s blocked by TalashNet External Request Blocker.', 'talashnet-external-request-blocker'),
                        esc_html($host)
                    )
                );
            }
        }

        return $preempt;
    }

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

        $hosts = array_values(array_unique($hosts));

        return $hosts;
    }
}
