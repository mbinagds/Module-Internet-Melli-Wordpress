# CLAUDE.md

Guidance for working in this repository.

## What this is

**Internet Melli (مسدودکننده سایت‌های خارجی)** — a WordPress plugin by Talashnet
that blocks requests to foreign/external domains so a site stays fast during
Iran's "National Internet" disruptions. Version 1.4.2, GPL-2.0+.

Plain PHP on WordPress. **No build step, no Composer/npm, no tests, no CI.**
Every file is shipped source. The UI is Persian-first (`fa_IR`) with English
(`en_US`) translations; most code comments and admin strings are in Persian.

Deeper context lives in [`docs/`](./docs/README.md) (overview, technologies,
entry point, folder structure).

## Skills

This repo bundles project-scoped skills under [`.claude/skills/`](./.claude/skills/README.md),
auto-discovered by Claude Code. Two sets:

- **WordPress engineering** (official [WordPress/agent-skills](https://github.com/WordPress/agent-skills),
  all 17 `wp-*` skills) — most relevant here: `wp-plugin-development`, `wp-performance`,
  `wp-plugin-directory-guidelines`, `wp-phpstan`, `wp-wpcli-and-ops`, `wp-rest-api`.
  `wordpress-router` / `wp-project-triage` are the entry points that route to the rest.
- **Superpowers methodology** ([obra/superpowers](https://github.com/obra/superpowers),
  8 curated) — design → plan → execute → debug → verify → review.

See the [skills README](./.claude/skills/README.md) for the full list and rationale
(test/git-worktree/branch superpowers skills were omitted: no test harness, not a git repo).

## Layout

```
internet-melli.php   Entry point: plugin header, constants, Internet_Melli singleton,
                     all hook registration, Service Worker generation, global AJAX fns.
includes/
  class-internet-melli-admin.php    Admin menu, settings page UI, AJAX toggle/test, enqueue.
  class-internet-melli-blocker.php  Backend HTTP blocking + auto-learn external hosts.
  class-internet-melli-remover.php  Frontend stripping of <script>/<link> tags.
  class-internet-melli-updater.php  Self-update: check/download/backup/unzip/rollback.
assets/css/admin-style.css          Admin panel styling (RTL).
assets/js/admin-settings.js         Admin panel logic (jQuery): domain lists, AJAX save, update, feedback.
languages/                          gettext .po/.mo for fa_IR (default) and en_US.
```

## Architecture (three blocking layers)

The plugin blocks external requests in three independent ways. Know which layer
a change affects:

1. **Backend** (`Internet_Melli_Blocker`) — hooks `pre_http_request` /
   `http_api_debug`. Auto-learns every external host the site calls and stores
   the backend list as JSON `[{domain, enabled}]` in option
   `internet_melli_blocked_domains_backend`. Gated by `internet_melli_backend_enabled`.
2. **Frontend stripping** (`Internet_Melli_Remover`) — filters
   `script_loader_src` / `style_loader_src` against the **comma-separated**
   frontend list in option `internet_melli_blocked_domains_frontend`.
3. **Service Worker** (`sw.js`) — generated as a JS string in
   `internet-melli.php` (`generate_sw_content`), served via a `/sw.js` rewrite
   rule, registered in `wp_head`. Returns `403` for blocked URLs. Gated by
   `internet_melli_enabled`. Optionally written physically to the WP root when
   `internet_melli_sw_guarantee` is on.

> Note the two domain lists use **different formats**: backend = JSON array of
> objects; frontend = comma-separated string. Don't mix them.

## Conventions

- Every PHP file starts with `if (!defined('ABSPATH')) exit;`. Keep this.
- `Internet_Melli` is a singleton (`get_instance()`); helper classes use static
  `init()` methods called from the constructor.
- All user-facing strings go through `__()` / `esc_html__()` with text domain
  `'internet-melli'`. After adding/changing strings, update the `.po`/`.mo`
  files in `languages/` for both `fa_IR` and `en_US`.
- AJAX handlers verify the `internet_melli_nonce` nonce **and**
  `current_user_can('manage_options')` (or `install_plugins` for updates).
  Preserve both checks on any new handler.
- A whitelist in the blocker always allows the site's own host plus
  `talashnet.com` / `mirror.talashnet.ir`.
- Admin assets load only on the plugin's page (`toplevel_page_internet-melli`) —
  match that hook check when enqueueing.

## Options (WP options table — the only storage)

`internet_melli_enabled`, `internet_melli_backend_enabled`,
`internet_melli_blocked_domains_frontend`, `internet_melli_blocked_domains_backend`,
`internet_melli_sw_guarantee`, `internet_melli_version`. `ajax_delete_all_data()`
deletes them all plus the root `sw.js`.

## External services (vendor-hosted, `mirror.talashnet.ir`)

- `.../updater/update-api.php` — update check
- `.../feedback/get.php` — feedback POST
- `.../utility/direct-plugin-manager.txt` — emergency plugin manager download

## Working notes

- There is no local way to "run" this — it must be installed in a WordPress
  instance under `wp-content/plugins/internet-melli/`. There is no automated
  test suite; verify changes manually in WordPress admin.
- The repo is **not a git repository**.
- When bumping the version, update the header in `internet-melli.php` (the
  `INTERNET_MELLI_VERSION` constant reads from it via `get_plugin_data`) and the
  changelog in `readme.txt`.
- Activation sets default options and adds the `sw.js` rewrite rule; changes to
  rewrite rules require `flush_rewrite_rules()` (already called on toggle/activate).

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
