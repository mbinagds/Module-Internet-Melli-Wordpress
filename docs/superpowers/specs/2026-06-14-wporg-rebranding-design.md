# Design: WP.org Rebranding — TalashNet External Request Blocker

**Date:** 2026-06-14
**Scope:** Rename plugin from "Internet Melli" to "TalashNet External Request Blocker" and prepare for WordPress.org directory submission.

---

## Goals

1. New plugin name: **TalashNet External Request Blocker**
2. New slug: `talashnet-external-request-blocker`
3. New PHP prefix: `tnet_` (functions/options/nonces), `Tnet_` (classes), `TNET_` (constants)
4. Author: `mbinagds` as primary; listed as contributor
5. `readme.txt` rewritten in professional English with mandatory External Services section
6. Plugin header updated with English name, author, description

---

## Identifier Mapping

### PHP Classes

| Old | New |
|-----|-----|
| `Internet_Melli` | `Tnet_Plugin` |
| `Internet_Melli_Admin` | `Tnet_Admin` |
| `Internet_Melli_Blocker` | `Tnet_Blocker` |
| `Internet_Melli_Remover` | `Tnet_Remover` |
| `Internet_Melli_Updater` | `Tnet_Updater` |
| `Internet_Melli_Admin_Menu` | `Tnet_Admin_Menu` |
| `Internet_Melli_Admin_Settings` | `Tnet_Admin_Settings` |
| `Internet_Melli_Admin_Assets` | `Tnet_Admin_Assets` |
| `Internet_Melli_Admin_Ajax` | `Tnet_Admin_Ajax` |
| `Internet_Melli_Admin_Page` | `Tnet_Admin_Page` |
| `Internet_Melli_Admin_Svg` | `Tnet_Admin_Svg` |

### Constants

| Old | New |
|-----|-----|
| `INTERNET_MELLI_VERSION` | `TNET_VERSION` |
| `INTERNET_MELLI_PATH` | `TNET_PATH` |
| `INTERNET_MELLI_URL` | `TNET_URL` |

### Functions

| Old | New |
|-----|-----|
| `internet_melli_check_update` | `tnet_check_update` |
| `internet_melli_install_update` | `tnet_install_update` |
| `ajax_delete_all_data` | `tnet_delete_all_data` |

### AJAX Actions

| Old | New |
|-----|-----|
| `internet_melli_toggle` | `tnet_toggle` |
| `internet_melli_test` | `tnet_test` |
| `check_plugin_update` | `tnet_check_update` |
| `install_plugin_update` | `tnet_install_update` |
| `internet_melli_delete_all` | `tnet_delete_all` |
| `im_send_feedback` | `tnet_send_feedback` |

### WP Options

| Old | New |
|-----|-----|
| `internet_melli_enabled` | `tnet_enabled` |
| `internet_melli_backend_enabled` | `tnet_backend_enabled` |
| `internet_melli_blocked_domains_frontend` | `tnet_blocked_domains_frontend` |
| `internet_melli_blocked_domains_backend` | `tnet_blocked_domains_backend` |
| `internet_melli_sw_guarantee` | `tnet_sw_guarantee` |
| `internet_melli_version` | `tnet_version` |

### Other

| Old | New |
|-----|-----|
| Text domain `internet-melli` | `talashnet-external-request-blocker` |
| Nonce `internet_melli_nonce` | `tnet_nonce` |
| JS variable `internetMelli` | `tnetPlugin` |
| `$plugin_slug = 'internet-melli'` | `'talashnet-external-request-blocker'` |

---

## File Renames

| Old filename | New filename |
|---|---|
| `internet-melli.php` | `talashnet-external-request-blocker.php` |
| `includes/class-internet-melli-admin.php` | `includes/class-tnet-admin.php` |
| `includes/class-internet-melli-blocker.php` | `includes/class-tnet-blocker.php` |
| `includes/class-internet-melli-remover.php` | `includes/class-tnet-remover.php` |
| `includes/class-internet-melli-updater.php` | `includes/class-tnet-updater.php` |
| `includes/admin/class-internet-melli-admin-ajax.php` | `includes/admin/class-tnet-admin-ajax.php` |
| `includes/admin/class-internet-melli-admin-assets.php` | `includes/admin/class-tnet-admin-assets.php` |
| `includes/admin/class-internet-melli-admin-menu.php` | `includes/admin/class-tnet-admin-menu.php` |
| `includes/admin/class-internet-melli-admin-page.php` | `includes/admin/class-tnet-admin-page.php` |
| `includes/admin/class-internet-melli-admin-settings.php` | `includes/admin/class-tnet-admin-settings.php` |
| `includes/admin/class-internet-melli-admin-svg.php` | `includes/admin/class-tnet-admin-svg.php` |

**Content-only (no rename):**
- `assets/js/admin-settings.js`
- View files under `includes/admin/views/`
- `readme.txt`

**Languages files — renamed to match new text domain:**
- `languages/internet-melli-fa_IR.po/.mo` → `languages/talashnet-external-request-blocker-fa_IR.po/.mo`
- `languages/internet-melli-en_US.po/.mo` → `languages/talashnet-external-request-blocker-en_US.po/.mo`
- Update `Language:` and `X-Domain:` headers inside the `.po` files.
- `.mo` files must be recompiled with `msgfmt` after `.po` edits; note in plan.

**HTML element IDs in view templates + JS** — IDs like `internet-melli-test-btn` appear in both PHP views and JS selectors. These must be renamed together (PHP view → JS) to stay in sync. New IDs will use `tnet-` prefix (e.g., `tnet-test-btn`).

**External service note — updater slug:** `Tnet_Updater` sends `slug=internet-melli` to the update API at `mirror.talashnet.ir`. After rename this becomes `slug=talashnet-external-request-blocker`. The remote API must be updated by TalashNet to recognise the new slug, or the update check will silently return no results. Flag this as a post-implementation task outside this codebase.

---

## Plugin Header (new)

```
Plugin Name:       TalashNet External Request Blocker
Plugin URI:        https://talashnet.com
Description:       Blocks outgoing requests to foreign domains to keep your WordPress site fast during Iran's National Internet disruptions. Supports backend blocking, frontend tag stripping, and an optional Service Worker layer.
Version:           1.4.2
Author:            mbinagds
Author URI:        https://talashnet.com
License:           GPL-2.0+
Text Domain:       talashnet-external-request-blocker
Domain Path:       /languages
```

---

## readme.txt structure (new)

```
=== TalashNet External Request Blocker ===
Contributors: mbinagds
Tags: performance, block, national internet, Iran, service worker
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.4.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

[Short description — one line]

== Description ==
[Full English description of the three blocking layers]

== Installation ==
[Standard WP install steps in English]

== External Services ==
This plugin connects to mirror.talashnet.ir (operated by TalashNet) for:
1. Plugin update checks and downloads — triggered manually by the site admin.
2. Optional feedback submission — only when the user explicitly submits the feedback form.

No data is sent automatically without user action.

[Privacy Policy placeholder]
[Terms of Service placeholder]

== Frequently Asked Questions ==

== Changelog ==
[Existing changelog entries, translated to English]
```

---

## Implementation Order

1. Rename + update 5 top-level include files (`class-tnet-*.php`)
2. Rename + update 6 admin class files (`class-tnet-admin-*.php`)
3. Rename + update main entry file → `talashnet-external-request-blocker.php` (updates require_once paths + all identifiers)
4. Update view files under `includes/admin/views/` (option names, nonce names, AJAX actions)
5. Update `assets/js/admin-settings.js` (AJAX action names, nonce field, JS variable name, HTML IDs that must stay in sync with PHP)
6. Rewrite `readme.txt`
7. Update `includes/admin/views/` — verify no missed identifiers
8. Final grep sweep to confirm zero occurrences of old identifiers
