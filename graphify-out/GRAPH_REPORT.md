# Graph Report - Module-Internet-Melli-Wordpress-main  (2026-06-12)

## Corpus Check
- 35 files · ~10,829 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 152 nodes · 142 edges · 33 communities (22 shown, 11 thin omitted)
- Extraction: 94% EXTRACTED · 6% INFERRED · 0% AMBIGUOUS · INFERRED: 8 edges (avg confidence: 0.72)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Community 0|Community 0]]
- [[_COMMUNITY_Community 1|Community 1]]
- [[_COMMUNITY_Community 2|Community 2]]
- [[_COMMUNITY_Community 3|Community 3]]
- [[_COMMUNITY_Community 4|Community 4]]
- [[_COMMUNITY_Community 5|Community 5]]
- [[_COMMUNITY_Community 6|Community 6]]
- [[_COMMUNITY_Community 7|Community 7]]
- [[_COMMUNITY_Community 8|Community 8]]
- [[_COMMUNITY_Community 9|Community 9]]
- [[_COMMUNITY_Community 10|Community 10]]
- [[_COMMUNITY_Community 11|Community 11]]
- [[_COMMUNITY_Community 12|Community 12]]
- [[_COMMUNITY_Community 13|Community 13]]
- [[_COMMUNITY_Community 14|Community 14]]
- [[_COMMUNITY_Community 15|Community 15]]
- [[_COMMUNITY_Community 16|Community 16]]
- [[_COMMUNITY_Community 17|Community 17]]

## God Nodes (most connected - your core abstractions)
1. `Internet_Melli_Admin` - 14 edges
2. `Internet_Melli_Blocker` - 13 edges
3. `Internet_Melli` - 13 edges
4. `Internet_Melli_Remover` - 12 edges
5. `Internet_Melli_Updater` - 10 edges
6. `2. What technologies does this project use?` - 5 edges
7. `Role of each part` - 5 edges
8. `Spec — Modularize `class-internet-melli-admin.php` + extract SVGs` - 5 edges
9. `Doc 01 — Overview` - 5 edges
10. `docs/README.md — Documentation Index` - 5 edges

## Surprising Connections (you probably didn't know these)
- `README.md — Project Description` --semantically_similar_to--> `Doc 01 — Overview`  [INFERRED] [semantically similar]
  README.md → docs/01-overview.md
- `Doc 04 — Folder Structure` --references--> `Internet_Melli_Admin`  [EXTRACTED]
  docs/04-folder-structure.md → includes/class-internet-melli-admin.php
- `Doc 04 — Folder Structure` --references--> `Internet_Melli_Updater`  [EXTRACTED]
  docs/04-folder-structure.md → includes/class-internet-melli-updater.php
- `Doc 01 — Overview` --references--> `sw.js Service Worker (generated)`  [EXTRACTED]
  docs/01-overview.md → internet-melli.php
- `Doc 01 — Overview` --references--> `Internet_Melli_Blocker`  [EXTRACTED]
  docs/01-overview.md → includes/class-internet-melli-blocker.php

## Import Cycles
- None detected.

## Communities (33 total, 11 thin omitted)

### Community 0 - "Community 0"
Cohesion: 0.09
Nodes (11): CLAUDE.md — Repository Guidance, docs/README.md — Documentation Index, Doc 03 — Entry Point, Doc 04 — Folder Structure, Internet_Melli_Blocker, Internet_Melli_Remover, internet-melli.php (Entry Point), Internet_Melli (Singleton) (+3 more)

### Community 2 - "Community 2"
Cohesion: 0.18
Nodes (9): Architecture (three blocking layers), Conventions, External services (vendor-hosted, `mirror.talashnet.ir`), graphify, Layout, Options (WP options table — the only storage), Skills, What this is (+1 more)

### Community 4 - "Community 4"
Cohesion: 0.25
Nodes (7): 4. Folder structure, `assets/` — the admin UI front end, `includes/` — the PHP brains, `languages/` — internationalization, Notable runtime-generated file (not in repo), Role of each part, Root metadata files

### Community 7 - "Community 7"
Cohesion: 0.33
Nodes (5): 2. What technologies does this project use?, Core stack, External services (vendor-hosted), What it does NOT use, WordPress hooks / APIs relied on

### Community 8 - "Community 8"
Cohesion: 0.33
Nodes (5): Constraints discovered (via graphify), Design (approved: concern classes + view partials; inline-helper SVGs), Goal, Spec — Modularize `class-internet-melli-admin.php` + extract SVGs, Verification

### Community 10 - "Community 10"
Cohesion: 0.40
Nodes (4): 1. What does this project do?, How it blocks (three independent layers), Key features, Vendor

### Community 11 - "Community 11"
Cohesion: 0.40
Nodes (4): 3. Where is the main entry point?, Execution flow summary, `internet-melli.php` (repository root), What it does, in order

## Knowledge Gaps
- **32 isolated node(s):** `What this is`, `Skills`, `Layout`, `Architecture (three blocking layers)`, `Conventions` (+27 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **11 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Internet_Melli_Admin` connect `Community 3` to `Community 0`?**
  _High betweenness centrality (0.096) - this node is a cross-community bridge._
- **Why does `Internet_Melli` connect `Community 1` to `Community 0`, `Community 3`?**
  _High betweenness centrality (0.093) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `Internet_Melli_Admin` (e.g. with `admin-style.css` and `admin-settings.js`) actually correct?**
  _`Internet_Melli_Admin` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Internet_Melli_Blocker` (e.g. with `Internet_Melli_Remover` and `.__construct()`) actually correct?**
  _`Internet_Melli_Blocker` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Internet_Melli` (e.g. with `.handle_toggle()` and `.handle_toggle()`) actually correct?**
  _`Internet_Melli` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Internet_Melli_Remover` (e.g. with `Internet_Melli_Blocker` and `.__construct()`) actually correct?**
  _`Internet_Melli_Remover` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `What this is`, `Skills`, `Layout` to the rest of the system?**
  _32 weakly-connected nodes found - possible documentation gaps or missing edges._