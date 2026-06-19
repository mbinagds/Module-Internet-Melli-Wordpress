# Plugin Analysis & Debug Report
## TalashNet External Request Blocker — v1.4.2
### Bilingual: English / فارسی

---

## CRITICAL ISSUES (WordPress.org Rejection Risk)

---

### 1. Self-Update Mechanism — `class-tnet-updater.php`

**EN:** WordPress.org Guideline 9 explicitly prohibits plugins from implementing their own update mechanisms that bypass the official WordPress.org update API. The entire `Tnet_Updater` class downloads and installs plugin updates from `mirror.talashnet.ir`. This will almost certainly cause rejection or removal.

**FA:** گایدلاین ۹ وردپرس.اورگ به صراحت اجازه نمی‌دهد پلاگین‌ها مکانیزم آپدیت مستقل از سیستم رسمی وردپرس داشته باشند. کلاس `Tnet_Updater` فایل آپدیت را از `mirror.talashnet.ir` دانلود و نصب می‌کند. این موضوع احتمالاً باعث رد شدن پلاگین می‌شود.

---

### 2. Emergency Tool Download — `tnet_download_emergency_tool()`

**EN:** The plugin downloads a raw PHP file from `http://mirror.talashnet.ir/utility/direct-plugin-manager.txt` over HTTP (not HTTPS) and serves it to the admin. Distributing an unauthenticated PHP management tool via an unencrypted channel is a critical security risk. WordPress.org reviewers will flag this.

**FA:** پلاگین یک فایل PHP خام را از طریق HTTP (نه HTTPS) از سرور خارجی دانلود کرده و به ادمین تحویل می‌دهد. توزیع ابزار مدیریت PHP از طریق کانال رمزنگاری‌نشده یک مشکل امنیتی جدی است و به احتمال زیاد توسط بررسی‌کننده‌های وردپرس.اورگ رد خواهد شد.

---

### 3. All External Endpoints Use HTTP — `class-tnet-updater.php`, `tnet_send_feedback()`

**EN:** Every call to `mirror.talashnet.ir` uses `http://`, not `https://`. This opens the door to man-in-the-middle attacks — an attacker on the network could inject malicious plugin code into the update download.

**FA:** تمام درخواست‌ها به `mirror.talashnet.ir` از پروتکل `http://` استفاده می‌کنند. این یعنی هر مهاجمی روی شبکه می‌تواند محتوای دریافت‌شده (از جمله فایل آپدیت پلاگین) را تغییر دهد.

---

## HIGH-SEVERITY BUGS

---

### 4. XSS in Domain List Rendering — `admin-settings.js` lines 249, 417

**EN:** Domain names are inserted directly into HTML without escaping:

```js
// Line 249 — FrontendDomainManager
html += '<span class="im-domain-text">' + domain + '</span>';

// Line 417 — BackendDomainManager
html += '<span class="im-domain-text">' + item.domain + '</span>';
```

If a stored domain string contains `<img src=x onerror=alert(1)>`, it executes when the admin page renders. This is a stored XSS vulnerability. Should use `.text()` or a proper DOM API instead of string concatenation.

**FA:** نام دامنه‌ها مستقیماً و بدون escape وارد HTML می‌شوند. اگر یک دامنه در دیتابیس حاوی کد HTML یا JavaScript باشد، هنگام بارگذاری صفحه ادمین اجرا می‌شود. این یک آسیب‌پذیری Stored XSS است.

---

### 5. Unescaped Release Notes from External Server — `admin-settings.js` line 579

**EN:**

```js
$imReleaseNotes.html('<h4>...</h4>' + response.release_notes);
```

`response.release_notes` comes directly from `mirror.talashnet.ir` and is inserted as raw HTML. If the update server is compromised, arbitrary HTML/JS could be injected into the admin panel.

**FA:** محتوای `release_notes` از سرور خارجی می‌آید و مستقیماً با `.html()` در صفحه رندر می‌شود. در صورت نفوذ به سرور، می‌توان کد جاوااسکریپت مخرب را به پنل ادمین تزریق کرد.

---

### 6. `www.` Stripping Inconsistency in Blocker Whitelist — `class-tnet-blocker.php` lines 67–74, 110–118

**EN:** `get_host_from_url()` strips `www.` from the checked host, but the whitelist builds `$site_host` from `parse_url(home_url(), ...)` without stripping `www.`. On sites whose home URL is `https://www.example.com`, the whitelist contains `www.example.com` while the checked host is `example.com` — the site's own domain would **not** be whitelisted and would be auto-learned and potentially blocked by the backend blocker.

```php
// Bug: both functions have this inconsistency
$site_host = parse_url(home_url(), PHP_URL_HOST); // "www.example.com" — www NOT stripped
// vs
$host = self::get_host_from_url($url);            // "example.com"     — www IS stripped
```

**FA:** تابع `get_host_from_url` پیشوند `www.` را از دامنه حذف می‌کند، اما whitelist از `parse_url(home_url())` ساخته می‌شود که `www.` را حذف نمی‌کند. در سایت‌هایی که آدرس آن‌ها با `www.` شروع می‌شود، دامنه خود سایت در whitelist نیست و ممکن است مسدود شود.

---

### 7. `require_once ABSPATH . 'wp-admin/includes/plugin.php'` on Every Request — `talashnet-external-request-blocker.php` line 18

**EN:** `plugin.php` is a wp-admin file. Loading it unconditionally on every frontend and backend request adds unnecessary overhead and is explicitly against WordPress coding standards (admin files should only be loaded in admin context).

**FA:** فایل `plugin.php` مخصوص پنل ادمین است. لود کردن آن در تمام درخواست‌های فرانت‌اند یعنی در هر بار بارگذاری صفحه توسط بازدیدکننده، این فایل اضافی اجرا می‌شود که بر سرعت سایت تأثیر منفی دارد.

---

## MEDIUM-SEVERITY ISSUES

---

### 8. `wp_ajax_nopriv` for Admin-Only Action — `talashnet-external-request-blocker.php` line 57

**EN:**

```php
add_action('wp_ajax_nopriv_tnet_send_feedback', array($this, 'tnet_send_feedback'));
```

The feedback form is displayed only inside `wp-admin`. A logged-out user can never reach it, so the `nopriv` hook is dead code — but it also exposes the AJAX handler to unauthenticated requests, which could be abused to flood the external feedback server.

**FA:** فرم فیدبک فقط در پنل ادمین نمایش داده می‌شود و کاربران غیر لاگین هرگز به آن دسترسی ندارند. وجود `nopriv` غیرضروری بوده و امکان ارسال اسپم به سرور فیدبک را از طریق درخواست‌های احراز هویت‌نشده فراهم می‌کند.

---

### 9. `sslverify => false` in Updater — `class-tnet-updater.php` lines 27, 72

**EN:** Both `wp_remote_get()` calls in the updater explicitly disable SSL verification. This means the plugin will silently accept a certificate from any entity (including a man-in-the-middle attacker) when downloading updates.

**FA:** هر دو درخواست `wp_remote_get()` در updater با `'sslverify' => false` فراخوانی می‌شوند. یعنی گواهی SSL سرور بررسی نمی‌شود و یک مهاجم می‌تواند خودش را جای سرور جا بزند.

---

### 10. PHP `date()` Instead of `wp_date()` — `class-tnet-updater.php` line 95

**EN:**

```php
$backup_dir = WP_CONTENT_DIR . '/plugin-backups/' . $this->plugin_slug . '-' . date('Y-m-d-H-i-s');
```

`date()` uses the server's system timezone, not the WordPress timezone. Should be `gmdate()` or `wp_date()`.

**FA:** تابع `date()` از timezone سیستم استفاده می‌کند نه timezone وردپرس. باید از `gmdate()` یا `wp_date()` استفاده شود.

---

### 11. Error Suppression with `@` — `class-tnet-updater.php`

**EN:** The updater uses `@unlink()`, `@rmdir()`, `@mkdir()` in multiple places. Error suppression hides real failures and makes debugging impossible. Should be replaced with proper `file_exists()` / `is_writable()` checks.

**FA:** در چند جا از عملگر `@` برای جلوگیری از نمایش خطاها استفاده شده. این روش اشکال‌زدایی را ناممکن می‌کند و باید با بررسی مستقیم وجود فایل/پوشه جایگزین شود.

---

### 12. `block_by_backend_list` — Incorrect `$preempt` Guard — `class-tnet-blocker.php` line 101

**EN:**

```php
if (! empty($preempt) && is_wp_error($preempt)) {
    return $preempt;
}
```

This only short-circuits if `$preempt` is a `WP_Error`. If another filter already short-circuited the request with a successful response object, this code continues executing and could overwrite that decision. The correct check is `if (!empty($preempt)) { return $preempt; }`.

**FA:** این چک فقط وقتی `$preempt` یک `WP_Error` باشد زودهنگام خارج می‌شود. اگر یک فیلتر قبلی درخواست را با یک پاسخ موفق کوتاه کرده باشد، کد ادامه اجرا می‌یابد و ممکن است آن پاسخ را بازنویسی کند.

---

### 13. `serve_sw_js()` Adds Rewrite Rule on Every Request — `talashnet-external-request-blocker.php` lines 59–60

**EN:**

```php
add_action('init', [$this, 'serve_sw_js']);
add_action('template_redirect', [$this, 'serve_sw_js']);
```

`serve_sw_js()` calls `add_rewrite_rule()` on every request. Rewrite rules should be added once on activation and flushed then, not re-added per request. The double hook (`init` + `template_redirect`) also means the function runs twice per page load.

**FA:** `add_rewrite_rule()` در هر درخواست HTTP اجرا می‌شود. قانون rewrite باید فقط یک بار در هنگام فعال‌سازی پلاگین اضافه شود. همچنین این متد در دو هوک (`init` و `template_redirect`) ثبت شده و در هر بارگذاری صفحه دو بار اجرا می‌شود.

---

### 14. Backup Directory Has No Cleanup Mechanism — `class-tnet-updater.php` line 95

**EN:** Each update creates a full backup of the plugin at `wp-content/plugin-backups/`. There is no code to clean up old backups. On active sites with frequent updates this directory will grow indefinitely.

**FA:** هر بار که آپدیت نصب می‌شود، یک نسخه پشتیبان کامل از پلاگین در `wp-content/plugin-backups/` ذخیره می‌شود. هیچ مکانیزم پاکسازی وجود ندارد و این پوشه به‌مرور بسیار حجیم می‌شود.

---

### 15. Auto-Learn List Has No Size Limit — `class-tnet-blocker.php` line 90

**EN:** Every new external host a site contacts is appended to the backend domains list in the database with no cap. On high-traffic sites with many integrations, this list could grow into thousands of entries, causing `foreach` iteration on every HTTP request to become a performance bottleneck.

**FA:** هر دامنه‌ی خارجی که سایت با آن ارتباط برقرار می‌کند به لیست اضافه می‌شود بدون هیچ محدودیتی. در سایت‌های پرترافیک این لیست ممکن است به هزاران آیتم برسد و `foreach` روی آن در هر درخواست HTTP، سرعت سایت را کاهش دهد.

---

## LOW-SEVERITY / CODE QUALITY ISSUES

---

### 16. Loose Comparison `==` Instead of `===` — Multiple Files

**EN:**

```php
// page.php
if ($active_tab == 'settings')        // should be ===
if ($active_tab == 'active-plugins')  // should be ===

// class-tnet-admin-menu.php
if ($plugin_page == 'talashnet-external-request-blocker') // should be ===

// tabs.php
$active_tab == 'settings' ? 'im-tab-active' : ''          // should be ===
```

**FA:** در چند جا از مقایسه شل `==` به جای `===` استفاده شده. در PHP این تفاوت می‌تواند باعث مقایسه‌های غیرمنتظره شود.

---

### 17. Missing `esc_attr()` on Active Tab CSS Class — `tabs.php` lines 6, 11

**EN:** The ternary output is a hardcoded string with no XSS risk here, but the expression should be wrapped in `esc_attr()` for completeness and to follow WordPress standards.

**FA:** خروجی ternary رشته‌ای ثابت است و خطر XSS ندارد، اما برای رعایت استانداردهای وردپرس باید از `esc_attr()` استفاده شود.

---

### 18. Update Checker IIFE Runs Before DOM — `admin-settings.js` lines 522–665

**EN:** The update checker IIFE calls `$('#im-check-update-btn')` immediately when the script is parsed, outside of `$(document).ready()`. Since the script is enqueued in the footer this works in practice, but it is fragile and not best practice.

**FA:** بخش update checker بدون `$(document).ready()` اجرا می‌شود. چون اسکریپت در footer لود می‌شود عملاً کار می‌کند، اما این روش شکننده و غیراستاندارد است.

---

### 19. Hard-coded Inline Styles in View Files — Multiple Cards

**EN:** Numerous view partials use inline `style=""` attributes (`style="flex: 1;"`, `style="margin-top: 0;"`, `style="display: flex; gap: 20px;"`, etc.). These should be moved to `admin-style.css` for maintainability and CSP compliance.

**FA:** در چندین فایل view از استایل‌های inline مستقیماً در HTML استفاده شده. این استایل‌ها باید به `admin-style.css` منتقل شوند.

---

### 20. Hard-coded Persian Strings in JavaScript — `admin-settings.js`

**EN:** Several JS strings are hard-coded Persian text instead of using the `tnetPlugin.strings` object already set up via `wp_localize_script()`. Examples: line 235 `'هنوز دامنه‌ای اضافه نشده است'`, line 269 `'نمی‌توانید دامنه خود سایت را مسدود کنید'`, line 673 `'آیا مطمئن هستید؟'`.

**FA:** برخی رشته‌های فارسی مستقیماً در JavaScript نوشته شده‌اند در حالی که باید از شیء `tnetPlugin.strings` که با `wp_localize_script()` تعریف شده استفاده شود تا قابل ترجمه باشند.

---

## Summary Table / جدول خلاصه

| # | Severity | File | Issue |
|---|----------|------|-------|
| 1 | 🔴 Critical | `class-tnet-updater.php` | Self-update mechanism violates WP.org Guideline 9 |
| 2 | 🔴 Critical | `talashnet-external-request-blocker.php` | Emergency PHP download over HTTP |
| 3 | 🔴 Critical | Multiple | All external endpoints use HTTP not HTTPS |
| 4 | 🟠 High | `admin-settings.js:249,417` | Stored XSS in domain list rendering |
| 5 | 🟠 High | `admin-settings.js:579` | XSS via release_notes from update server |
| 6 | 🟠 High | `class-tnet-blocker.php:67,110` | `www.` site host not whitelisted — own domain can be blocked |
| 7 | 🟠 High | `talashnet-external-request-blocker.php:18` | `wp-admin/plugin.php` loaded on every frontend request |
| 8 | 🟡 Medium | `talashnet-external-request-blocker.php:57` | Unnecessary `nopriv` AJAX exposes feedback to unauthenticated requests |
| 9 | 🟡 Medium | `class-tnet-updater.php:27,72` | `sslverify => false` |
| 10 | 🟡 Medium | `class-tnet-updater.php:95` | `date()` uses server timezone instead of `gmdate()` |
| 11 | 🟡 Medium | `class-tnet-updater.php` | `@` error suppression |
| 12 | 🟡 Medium | `class-tnet-blocker.php:101` | Wrong `$preempt` guard skips already-blocked responses |
| 13 | 🟡 Medium | `talashnet-external-request-blocker.php:59–60` | `add_rewrite_rule()` runs on every request, twice |
| 14 | 🟡 Medium | `class-tnet-updater.php:95` | Backup directory never cleaned up |
| 15 | 🟡 Medium | `class-tnet-blocker.php:90` | Auto-learn list has no size limit |
| 16 | 🔵 Low | `page.php`, `tabs.php`, `class-tnet-admin-menu.php` | `==` instead of `===` |
| 17 | 🔵 Low | `tabs.php` | Missing `esc_attr()` on active tab class |
| 18 | 🔵 Low | `admin-settings.js:522` | Update checker IIFE outside `$(document).ready()` |
| 19 | 🔵 Low | View files | Inline styles should be in `admin-style.css` |
| 20 | 🔵 Low | `admin-settings.js` | Hard-coded Persian strings not translatable |
