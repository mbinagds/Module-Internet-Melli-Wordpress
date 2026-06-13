# Project Context Documentation

Full-context documentation for the **Internet Melli** WordPress plugin
(مسدودکننده سایت‌های خارجی) by Talashnet. These docs answer the four core
onboarding questions about the project.

| # | Document | Question answered |
|---|----------|-------------------|
| 1 | [01-overview.md](./01-overview.md) | What does this project do? |
| 2 | [02-technologies.md](./02-technologies.md) | What technologies does it use? |
| 3 | [03-entry-point.md](./03-entry-point.md) | Where is the main entry point? |
| 4 | [04-folder-structure.md](./04-folder-structure.md) | What is the folder structure? |

## TL;DR

A WordPress plugin (PHP + vanilla JS/CSS + a browser Service Worker) that blocks
requests to foreign domains so a site stays fast during Iran's National Internet
disruptions. Entry point: `internet-melli.php`. Logic lives in four classes
under `includes/`; admin UI assets under `assets/`; translations under
`languages/`. No build step, no dependencies, version 1.4.2, GPL-2.0+.
