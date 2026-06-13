# 2. What technologies does this project use?

This is a classic **WordPress plugin** — no build step, no package manager, no
framework. It is plain PHP plus vanilla browser assets.

## Core stack

| Area | Technology | Notes |
|------|-----------|-------|
| Language | **PHP** | Plugin requires WordPress 5.0+, tested up to WP 6.9. Object-oriented (singleton + helper classes). |
| Platform | **WordPress** | Uses the WP Plugin API: actions, filters, options API, AJAX (`admin-ajax.php`), `admin-post.php`, nonces, settings API, `WP_Filesystem`, rewrite rules. |
| Frontend (admin) | **JavaScript (vanilla + jQuery)** | `assets/js/admin-settings.js`. jQuery is pulled in as a WordPress-bundled dependency. |
| Frontend (admin) | **CSS** | `assets/css/admin-style.css` — custom admin UI (cards, tabs, toggle switches), RTL-oriented. |
| Browser blocking | **Service Worker API** | `sw.js` is generated server-side as a JS string and registered in the browser to intercept `fetch` requests. |
| i18n | **GNU gettext** | `.po`/`.mo` translation files for `fa_IR` and `en_US`, loaded via `load_plugin_textdomain`. |

## WordPress hooks / APIs relied on

- **HTTP API filters:** `pre_http_request`, `http_api_debug` (backend blocking + auto-learn).
- **Asset filters:** `script_loader_src`, `style_loader_src` (frontend stripping).
- **Admin:** `admin_menu`, `admin_init`, `admin_enqueue_scripts`, settings API (`register_setting`).
- **AJAX:** `wp_ajax_*` handlers for toggle, test, update check/install, delete-all, feedback.
- **Frontend:** `wp_head` (Service Worker registration script), `wp_enqueue_scripts`.
- **Routing:** `add_rewrite_rule` to serve `/sw.js`, `template_redirect`, `flush_rewrite_rules`.
- **Lifecycle:** `register_activation_hook`, `register_deactivation_hook`.
- **Filesystem:** `WP_Filesystem`, `unzip_file`, `request_filesystem_credentials` (used by the updater).
- Optional integration with the third-party **Snitch** plugin via `snitch_inspect_request_hosts`.

## External services (vendor-hosted)

- `http://mirror.talashnet.ir/wordpress/internet-melli/updater/update-api.php` — update check API.
- `http://mirror.talashnet.ir/wordpress/internet-melli/feedback/get.php` — feedback endpoint.
- `http://mirror.talashnet.ir/wordpress/internet-melli/utility/direct-plugin-manager.txt` — emergency tool download.

## What it does NOT use

No Composer, npm, webpack, Node, database tables (only the WP options table),
test framework, or CI config. Everything ships as flat source files.
