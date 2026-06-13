<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers plugin settings via the Settings API.
 */
class Internet_Melli_Admin_Settings
{
    public function register()
    {
        register_setting(
            'internet_melli_settings',
            'internet_melli_enabled',
            array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_backend_enabled',
            array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 1
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_blocked_domains_frontend',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => 'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_blocked_domains_backend',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => 'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_version',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '1.2'
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_sw_guarantee',
            array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0
            )
        );
    }
}
