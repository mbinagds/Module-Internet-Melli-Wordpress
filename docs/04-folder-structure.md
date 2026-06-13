# 4. Folder structure

The project is a small, flat WordPress plugin. There is no build output —
every file is shipped source.

```
Module-Internet-Melli-Wordpress-main/
├── internet-melli.php        ← Main plugin bootstrap / entry point (see doc 03)
├── README.md                 ← One-line GitHub description (English)
├── readme.txt                ← WordPress.org-style readme (Persian) + changelog + credits
├── LICENSE                   ← GPL-2.0 license text
│
├── includes/                 ← PHP classes (one responsibility each)
│   ├── class-internet-melli-admin.php     ← Admin menu, settings page UI, AJAX toggle/test, enqueue
│   ├── class-internet-melli-blocker.php   ← Backend HTTP blocking + auto-learn of external hosts
│   ├── class-internet-melli-remover.php   ← Frontend stripping of <script>/<link> tags
│   └── class-internet-melli-updater.php   ← Self-update: check, download, backup, unzip, rollback
│
├── assets/                   ← Browser-side admin assets
│   ├── css/
│   │   └── admin-style.css                ← Admin panel styling (cards, tabs, toggles; RTL)
│   └── js/
│       └── admin-settings.js              ← Admin panel logic (domain lists, AJAX save, update, feedback)
│
└── languages/                ← gettext translations
    ├── internet-melli-fa_IR.po / .mo      ← Persian (default UI language)
    └── internet-melli-en_US.po / .mo      ← English
```

## Role of each part

### `includes/` — the PHP brains
Loaded by the bootstrap. Each class is single-purpose and self-guarded with
`if (!defined('ABSPATH')) exit;`:

- **`Internet_Melli_Admin`** — renders the wp-admin settings page (two tabs:
  *Settings* and *Emergency plugin activation*), registers options
  (`internet_melli_enabled`, `internet_melli_backend_enabled`,
  `internet_melli_blocked_domains_frontend`, `..._backend`,
  `internet_melli_sw_guarantee`), enqueues CSS/JS, and handles the
  `handle_toggle` / `handle_test` AJAX actions (including writing/deleting the
  physical `sw.js` root file).
- **`Internet_Melli_Blocker`** — server-side. Auto-records every external host
  the site contacts and blocks enabled ones via `pre_http_request`. Stores the
  backend list as JSON `[{domain, enabled}]`. Has a built-in whitelist.
- **`Internet_Melli_Remover`** — strips enqueued scripts/styles whose host
  matches the comma-separated frontend domain list.
- **`Internet_Melli_Updater`** — talks to the vendor update API; downloads a
  zip, backs up the current plugin to `wp-content/plugin-backups/...`, extracts,
  and rolls back on failure.

### `assets/` — the admin UI front end
Only loaded on the plugin's own admin page (`toplevel_page_internet-melli`).
`admin-settings.js` manages the two domain-list editors, AJAX-saves settings,
runs the requestor test, drives the update check/install flow, and submits the
feedback form. `admin-style.css` styles the whole panel.

### `languages/` — internationalization
`.po` (editable source) + compiled `.mo` for Persian and English. Loaded at
runtime by `load_plugin_textdomain('internet-melli', ...)`.

### Root metadata files
- `internet-melli.php` — entry point (also contains the generated Service Worker source).
- `readme.txt` — the canonical changelog and credits (WordPress plugin format).
- `README.md` — short English summary for GitHub.
- `LICENSE` — GPL-2.0.

## Notable runtime-generated file (not in repo)
- **`sw.js`** — the Service Worker. Served dynamically via a rewrite rule
  (`/sw.js` → `?sw=internet-melli`), and *optionally* physically written to the
  WordPress root directory when the "Guarantee requestor" option is enabled.
