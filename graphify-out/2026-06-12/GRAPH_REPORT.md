# Graph Report - .  (2026-06-12)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 69 nodes · 87 edges · 7 communities (2 shown, 5 thin omitted)
- Extraction: 92% EXTRACTED · 8% INFERRED · 0% AMBIGUOUS · INFERRED: 7 edges (avg confidence: 0.71)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Community 0|Community 0]]
- [[_COMMUNITY_Community 1|Community 1]]
- [[_COMMUNITY_Community 2|Community 2]]
- [[_COMMUNITY_Community 3|Community 3]]
- [[_COMMUNITY_Community 4|Community 4]]
- [[_COMMUNITY_Community 5|Community 5]]
- [[_COMMUNITY_Community 6|Community 6]]

## God Nodes (most connected - your core abstractions)
1. `Internet_Melli_Admin` - 14 edges
2. `Internet_Melli_Blocker` - 13 edges
3. `Internet_Melli_Remover` - 12 edges
4. `Internet_Melli` - 12 edges
5. `Internet_Melli_Updater` - 10 edges
6. `Doc 01 — Overview` - 5 edges
7. `docs/README.md — Documentation Index` - 5 edges
8. `internet-melli.php (Entry Point)` - 5 edges
9. `Internet_Melli (Singleton)` - 5 edges
10. `Doc 02 — Technologies` - 3 edges

## Surprising Connections (you probably didn't know these)
- `README.md — Project Description` --semantically_similar_to--> `Doc 01 — Overview`  [INFERRED] [semantically similar]
  README.md → docs/01-overview.md
- `Doc 04 — Folder Structure` --references--> `Internet_Melli_Admin`  [EXTRACTED]
  docs/04-folder-structure.md → includes/class-internet-melli-admin.php
- `Doc 01 — Overview` --references--> `Internet_Melli_Remover`  [EXTRACTED]
  docs/01-overview.md → includes/class-internet-melli-remover.php
- `Doc 04 — Folder Structure` --references--> `Internet_Melli_Updater`  [EXTRACTED]
  docs/04-folder-structure.md → includes/class-internet-melli-updater.php
- `Doc 01 — Overview` --references--> `sw.js Service Worker (generated)`  [EXTRACTED]
  docs/01-overview.md → internet-melli.php

## Import Cycles
- None detected.

## Communities (7 total, 5 thin omitted)

### Community 0 - "Community 0"
Cohesion: 0.15
Nodes (7): Doc 03 — Entry Point, Internet_Melli_Blocker, internet-melli.php (Entry Point), Internet_Melli (Singleton), Doc 01 — Overview, README.md — Project Description, sw.js Service Worker (generated)

### Community 2 - "Community 2"
Cohesion: 0.18
Nodes (4): CLAUDE.md — Repository Guidance, docs/README.md — Documentation Index, Doc 04 — Folder Structure, Doc 02 — Technologies

## Knowledge Gaps
- **3 isolated node(s):** `CLAUDE.md — Repository Guidance`, `README.md — Project Description`, `readme.txt — WordPress Readme & Changelog`
  These have ≤1 connection - possible missing edges or undocumented components.
- **5 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Internet_Melli_Admin` connect `Community 3` to `Community 0`, `Community 1`, `Community 2`?**
  _High betweenness centrality (0.437) - this node is a cross-community bridge._
- **Why does `Internet_Melli` connect `Community 1` to `Community 4`?**
  _High betweenness centrality (0.344) - this node is a cross-community bridge._
- **Why does `Internet_Melli_Blocker` connect `Community 0` to `Community 4`?**
  _High betweenness centrality (0.267) - this node is a cross-community bridge._
- **Are the 2 inferred relationships involving `Internet_Melli_Admin` (e.g. with `admin-style.css` and `admin-settings.js`) actually correct?**
  _`Internet_Melli_Admin` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Internet_Melli_Blocker` (e.g. with `Internet_Melli_Remover` and `.__construct()`) actually correct?**
  _`Internet_Melli_Blocker` has 2 INFERRED edges - model-reasoned connections that need verification._
- **Are the 2 inferred relationships involving `Internet_Melli_Remover` (e.g. with `Internet_Melli_Blocker` and `.__construct()`) actually correct?**
  _`Internet_Melli_Remover` has 2 INFERRED edges - model-reasoned connections that need verification._
- **What connects `CLAUDE.md — Repository Guidance`, `README.md — Project Description`, `readme.txt — WordPress Readme & Changelog` to the rest of the system?**
  _3 weakly-connected nodes found - possible documentation gaps or missing edges._