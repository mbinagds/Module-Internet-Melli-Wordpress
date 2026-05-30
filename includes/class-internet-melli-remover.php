<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Internet_Melli_Remover {

    const OPTION_FRONTEND_DOMAINS = 'internet_melli_blocked_domains_frontend';

    public static function init() {

        // حذف js
        add_filter( 'script_loader_src', array( __CLASS__, 'filter_script_src' ), 10, 2 );

        // حذف css
        add_filter( 'style_loader_src', array( __CLASS__, 'filter_style_src' ), 10, 2 );
    }

    /**
     * گرفتن host از URL
     */
    protected static function get_host_from_url( $url ) {

        if ( empty( $url ) ) {
            return false;
        }

        $host = wp_parse_url( $url, PHP_URL_HOST );

        if ( $host && strpos( $host, 'www.' ) === 0 ) {
            $host = substr( $host, 4 );
        }

        return $host;
    }

    /**
     * گرفتن لیست دامنه‌های frontend
     */
    protected static function get_blocked_domains() {

        $domains = get_option(
            self::OPTION_FRONTEND_DOMAINS,
            'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
        );

        $domains = array_map( 'trim', explode( ',', $domains ) );

        return $domains;
    }

    /**
     * بررسی اینکه دامنه باید بلاک شود
     */
    protected static function should_block( $url ) {

        $host = self::get_host_from_url( $url );

        if ( ! $host ) {
            return false;
        }

        $blocked = self::get_blocked_domains();

        foreach ( $blocked as $domain ) {

            if ( empty( $domain ) ) {
                continue;
            }

            if ( strpos( $host, $domain ) !== false ) {
                return true;
            }
        }

        return false;
    }

    /**
     * فیلتر JS
     */
    public static function filter_script_src( $src, $handle ) {

        if ( self::should_block( $src ) ) {
            return false;
        }

        return $src;
    }

    /**
     * فیلتر CSS
     */
    public static function filter_style_src( $src, $handle ) {

        if ( self::should_block( $src ) ) {
            return false;
        }

        return $src;
    }
}
