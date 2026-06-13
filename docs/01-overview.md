# 1. What does this project do?

**Internet Melli (مسدودکننده سایت‌های خارجی)** is a **WordPress plugin** that blocks
outgoing requests to foreign/external domains in order to keep a site fast and
stable while Iran's "National Internet" (اینترنت ملی) is active and the
international internet is throttled or disrupted.

When the international internet is degraded, WordPress sites that load external
resources (Google Fonts, Gravatar, CDNs such as Cloudflare/jsDelivr/unpkg,
Microsoft/Clarity analytics, FontAwesome, etc.) become very slow or render a
blank white page while the browser waits on requests that will never complete.
This plugin removes/blocks those external requests so the site loads from local
resources only.

## How it blocks (three independent layers)

The plugin blocks on both the **backend** (PHP/server side) and the **frontend**
(browser side):

| Layer | Class / Mechanism | What it does |
|-------|-------------------|--------------|
| **Backend blocker** | `Internet_Melli_Blocker` | Hooks WordPress HTTP API (`pre_http_request`). Auto-learns every external host the site contacts (`http_api_debug`) and blocks the ones on the backend list. Speeds up wp-admin/dashboard and server-side calls. |
| **Frontend remover** | `Internet_Melli_Remover` | Filters `script_loader_src` / `style_loader_src` to strip `<script>`/`<link>` tags pointing at blocked domains before the page is sent to the browser. |
| **Frontend requestor (Service Worker)** | `sw.js` generated in `internet-melli.php` | An optional Service Worker registered in the browser that intercepts `fetch` events and returns a `403` for any URL containing a blocked domain. Used during severe disruptions (requires SSL). |

## Key features

- Toggle backend blocker and frontend requestor independently from the admin panel.
- Customizable lists of blocked domains (separate lists for frontend and backend).
- Auto-learning of backend domains (every external host the site calls is recorded so the admin can enable/disable each one).
- A whitelist that always allows the site's own host plus `talashnet.com` / `mirror.talashnet.ir`.
- Self-update mechanism that checks `mirror.talashnet.ir` for new plugin versions and downloads/installs them (with automatic backup/rollback).
- "Emergency plugin manager" tool — downloads a standalone `direct-plugin-manager.php` you can drop in the site root to enable/disable plugins when wp-admin itself is inaccessible.
- Feedback form that posts to the vendor's server.
- Full bilingual UI (Persian `fa_IR` default, English `en_US`).
- "Guarantee" option that physically writes `sw.js` to the WordPress root to avoid 404 routing issues.

## Vendor

Developed by **تلاش نت (Talashnet)** — <https://talashnet.com>.
Original author: Sina Shiri (`sshnevis`); maintainer: Mobina Ghodousian (`mbinagds`).
Licensed **GPL-2.0+**. Current version: **1.4.2**.
