=== TalashNet External Request Blocker ===
Contributors: mbinagds
Tags: performance, block, national internet, Iran, service worker
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.4.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Blocks outgoing requests to foreign domains to keep your WordPress site fast during Iran's National Internet disruptions.

== Description ==

**TalashNet External Request Blocker** prevents WordPress sites from loading external resources (Google Fonts, Gravatar, CDNs, analytics scripts, etc.) that become unavailable or extremely slow during Iran's National Internet (اینترنت ملی) disruptions. Without blocking, a single unresolvable external resource can cause a blank white page or multi-second delays for every visitor.

The plugin works on three independent layers, so you can enable exactly the level of blocking you need:

**1. Backend Blocker**
Hooks into the WordPress HTTP API (`pre_http_request`) and blocks server-side requests to external hosts. It also auto-learns every external host your site contacts (`http_api_debug`) and records them so you can enable or disable each one individually from the admin panel. Speeds up wp-admin and server-side operations.

**2. Frontend Tag Stripper**
Filters `script_loader_src` and `style_loader_src` to remove `<script>` and `<link>` tags that point to blocked domains before the page is sent to the browser. Zero JavaScript required.

**3. Service Worker (optional)**
Registers a browser Service Worker that intercepts `fetch` events and returns a `403` response for any URL containing a blocked domain. Useful during severe disruptions where resources slip through the other layers. Requires HTTPS.

**Key Features**

* Toggle each blocking layer independently from the admin panel.
* Separate, customizable domain lists for frontend and backend blocking.
* Auto-learning of backend domains — every external host your site calls is discovered and listed automatically.
* Built-in whitelist that always allows your own domain plus `talashnet.com` / `mirror.talashnet.ir`.
* Self-update mechanism — checks `mirror.talashnet.ir` for new versions and installs them with automatic backup and rollback.
* Emergency Plugin Manager — downloads a standalone PHP file you can upload to your server root to enable/disable plugins when wp-admin is inaccessible.
* Fully bilingual admin UI: Persian (fa_IR, default) and English (en_US).
* "SW Guarantee" option physically writes `sw.js` to the WordPress root to avoid 404 routing issues on some server configurations.
* No build step, no external libraries, no database tables — only the WordPress options table is used.

== Installation ==

1. Upload the `talashnet-external-request-blocker` folder to `wp-content/plugins/`.
2. Activate the plugin from the **Plugins** screen in WordPress admin.
3. Navigate to **External Blocker** in the admin sidebar.
4. Enable the blocking layers you need and save settings.

== External Services ==

This plugin connects to **mirror.talashnet.ir** (operated by TalashNet, https://talashnet.com) for the following purposes:

1. **Plugin update checks and downloads** — When the site admin clicks "Check for Updates" in the plugin settings, the plugin sends the current version number and site URL to the update API. If a new version is available, the admin can download and install it from the same panel. No data is sent automatically without user action.

   Endpoint: `http://mirror.talashnet.ir/wordpress/internet-melli/updater/update-api.php`

2. **Optional feedback submission** — The settings page includes a feedback form. Data is only sent when the site admin explicitly submits the form; nothing is sent automatically.

   Endpoint: `http://mirror.talashnet.ir/wordpress/internet-melli/feedback/get.php`

3. **Emergency Plugin Manager download** — The "Emergency Activation" tab offers a downloadable PHP utility file. It is downloaded on demand when the admin clicks the download button.

   Endpoint: `http://mirror.talashnet.ir/wordpress/internet-melli/utility/direct-plugin-manager.txt`

**Privacy Policy:** https://talashnet.com/privacy-policy
**Terms of Service:** https://talashnet.com/terms-of-service

No personal data beyond the site URL and current plugin version is transmitted during update checks. Feedback text is submitted only when the admin explicitly sends it.

== Frequently Asked Questions ==

= Does this plugin affect the frontend for visitors? =

The backend blocker and frontend tag stripper are transparent to visitors. The Service Worker layer requires HTTPS and runs in the visitor's browser — on the very first page load after activation it will reload the page once to take control.

= Why does the Service Worker require HTTPS? =

The browser Service Worker API only works on secure origins (HTTPS or localhost). If your site does not have an SSL certificate, enable only the backend blocker and frontend stripper.

= Will this break payment gateways or SMS services? =

If you use an Iranian payment gateway or SMS API, its domain may be auto-learned and listed as a backend domain. Review the backend domain list after enabling the blocker and set any Iranian service domains to "Open" to allow them through.

= What happens when I deactivate the plugin? =

Deactivation flushes rewrite rules. If you used the "Delete All Data" option, all plugin options are removed from the database and any physically written `sw.js` file is deleted.

== Changelog ==

= 1.4.2 =
* Rebranded to TalashNet External Request Blocker for WordPress.org submission.
* Renamed all internal identifiers to `tnet_` prefix.
* Plugin header, readme, and admin UI updated to English.
* Added External Services disclosure section.

= 1.4 =
* UX improvements.
* Help and guidelines added to admin panel.
* Improved data entry validation.

= 1.3 =
* UX improvements.
* Improved frontend blocking.
* Bug fixes.
* Service Worker served via two methods for reliability.

= 1.2 =
* Improved backend blocker controller.
* Added dashboard speed fix.
* Smart domain suggestion.

= 1.1 =
* Added domain list customization.
* Improved self-update system.

= 1.0 =
* Initial release.

== Credits ==

* Original author: [Sina Shiri](https://github.com/sshnevis)
* Maintainer: [Mobina Ghodousian](https://github.com/mbinagds)
* Developed by [TalashNet](https://talashnet.com)
