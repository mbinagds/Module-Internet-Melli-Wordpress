<?php
if (!defined('ABSPATH')) {
    exit;
}

class Tnet_Admin_Settings
{
    public function register()
    {
        register_setting(
            'tnet_settings',
            'tnet_enabled',
            array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 0,
            )
        );

        register_setting(
            'tnet_settings',
            'tnet_backend_enabled',
            array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 1,
            )
        );

        register_setting(
            'tnet_settings',
            'tnet_blocked_domains_frontend',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default'           => 'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org',
            )
        );

        register_setting(
            'tnet_settings',
            'tnet_blocked_domains_backend',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default'           => 'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org',
            )
        );

        register_setting(
            'tnet_settings',
            'tnet_version',
            array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '1.4.2',
            )
        );

        register_setting(
            'tnet_settings',
            'tnet_sw_guarantee',
            array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 0,
            )
        );
    }
}
