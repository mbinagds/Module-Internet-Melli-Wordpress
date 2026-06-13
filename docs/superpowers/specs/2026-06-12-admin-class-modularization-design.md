# Spec — Modularize `class-internet-melli-admin.php` + extract SVGs

Date: 2026-06-12 · Status: implemented

## Goal
Break the 1024-line `includes/class-internet-melli-admin.php` "god class" into
several short, single-responsibility modules, and move all inline SVGs into
separate `.svg` files. **No behavior change** — same HTML output, hooks, nonces,
and capability checks.

## Constraints discovered (via graphify)
`internet-melli.php` constructs `new Internet_Melli_Admin('internet-melli', $version)`
and wires WordPress hooks to its public methods (`add_menu_page`,
`register_settings`, `enqueue_styles`, `enqueue_scripts`, `handle_toggle`,
`handle_test`, `render_admin_page`). That public surface **must be preserved** so
the entry point needs no edits. Two pre-existing quirks are preserved verbatim:
the active-plugins tab leaves `im-admin-grid`/wrapper `<div>`s unclosed, and
`card-settings` references an undefined `$blocked_domains`.

## Design (approved: concern classes + view partials; inline-helper SVGs)

`includes/class-internet-melli-admin.php` becomes a **slim coordinator** that
keeps the public API and delegates to modules under `includes/admin/`:

- `class-...-menu.php` — menu/submenu registration (render callback injected)
- `class-...-settings.php` — Settings API registration
- `class-...-assets.php` — CSS/JS enqueue + localization
- `class-...-ajax.php` — `handle_toggle` (sw.js write) + `handle_test`
- `class-...-page.php` — sets up view data, includes `views/page.php`
- `class-...-svg.php` — inlines `assets/svg/<name>.svg` (preserves `currentColor`)
- `views/**` — one short partial per section (header, tabs, settings card, each
  sidebar card, active-plugins cards). PHP `if/endif` stays in the orchestrator
  (`views/page.php`) because control structures cannot span includes.

SVGs: `assets/svg/{bale,telegram,instagram}.svg`, inlined via
`Internet_Melli_Admin_Svg::render()` (the 6 inline blocks were 3 unique icons,
now de-duplicated and reused across both tabs).

## Verification
- Method bodies carved with `sed` → byte-identical to the original.
- An include-expander recomposed the view tree and matched the original
  `render_admin_page` body (whitespace-normalized): **output parity proven**.
- Brace/paren/bracket balance OK on all 22 files.
- `php -l` not run (no PHP runtime in this environment).
