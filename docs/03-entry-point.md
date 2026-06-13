# 3. Where is the main entry point?

## `internet-melli.php` (repository root)

This is the **WordPress plugin bootstrap file** and the single entry point.
WordPress discovers it via the plugin header comment block at the top:

```php
/**
 * Plugin Name:       مسدودکننده سایت‌های خارجی (Internet Melli)
 * Version:           1.4.2
 * Text Domain:       internet-melli
 * ...
 */
```

### What it does, in order

1. **Guard** — `if (!defined('ABSPATH')) exit;` prevents direct access outside WordPress.
2. **Defines constants** — `INTERNET_MELLI_VERSION`, `INTERNET_MELLI_PATH`, `INTERNET_MELLI_URL`.
3. **Loads the four helper classes** from `includes/` (admin, updater, blocker, remover).
4. **Defines the `Internet_Melli` singleton class** — the heart of the plugin:
   - `get_instance()` — singleton accessor (called at the bottom of the file).
   - `__construct()` — registers **all** hooks (admin menu, settings, enqueue,
     `wp_head` SW injection, AJAX handlers, `admin_post` emergency-tool download,
     deactivation hook) and initializes `Internet_Melli_Blocker` and
     `Internet_Melli_Remover`.
   - `generate_sw_content()` / `serve_sw_js()` / `add_sw_to_head()` — build,
     serve, and register the Service Worker.
   - `im_send_feedback()`, `im_download_emergency_tool()`, `deactivate()`.
5. **Global AJAX functions** — `internet_melli_check_update()`,
   `internet_melli_install_update()`, `ajax_delete_all_data()`.
6. **Boots the plugin** — `Internet_Melli::get_instance();` at the end.
7. **Activation hook** — sets default options and adds the `sw.js` rewrite rule on activation.

### Execution flow summary

```
WordPress loads internet-melli.php
        │
        ├─ require_once includes/class-internet-melli-admin.php
        ├─ require_once includes/class-internet-melli-updater.php
        ├─ require_once includes/class-internet-melli-blocker.php
        ├─ require_once includes/class-internet-melli-remover.php
        │
        └─ Internet_Melli::get_instance()
                 ├─ new Internet_Melli_Admin(...)   → admin UI, settings, AJAX
                 ├─ Internet_Melli_Blocker::init()  → backend HTTP blocking + auto-learn
                 └─ Internet_Melli_Remover::init()  → strip frontend script/style tags
```

There is **no other entry point** — the plugin is purely event-driven through
the WordPress hook system after this file loads.
