<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inlines an SVG asset from assets/svg/.
 *
 * Inlining (rather than <img src>) preserves fill="currentColor" so the icons
 * keep inheriting their hover color from CSS, exactly as the original markup did.
 */
class Internet_Melli_Admin_Svg
{
    /**
     * Echo the contents of assets/svg/{$name}.svg.
     *
     * @param string $name Icon slug (e.g. 'bale', 'telegram', 'instagram').
     */
    public static function render($name)
    {
        $name = preg_replace('/[^a-z0-9_-]/i', '', (string) $name);
        $file = INTERNET_MELLI_PATH . 'assets/svg/' . $name . '.svg';

        if ($name !== '' && is_readable($file)) {
            echo file_get_contents($file); // Trusted local, static SVG markup.
        }
    }
}
