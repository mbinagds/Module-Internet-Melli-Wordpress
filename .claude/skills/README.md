# Project Skills

These are **project-scoped Claude Code skills**. Claude Code auto-discovers any
`SKILL.md` under `.claude/skills/` by its `description`, so they become available
automatically when working in this repo — no plugin install or marketplace
registration required.

Three sets are installed:

1. **WordPress engineering skills** — from [WordPress/agent-skills](https://github.com/WordPress/agent-skills)
   (the **official WordPress** repo, commit `aa735ea`), all 17 `wp-*` skills. See below.
2. **Superpowers methodology skills** — from [obra/superpowers](https://github.com/obra/superpowers)
   (v5.1.0, commit `6fd4507`), 8 curated skills. See [the Superpowers section](#superpowers-methodology-skills-curated).
3. **graphify** (`graphify/`) — from [safishamsi/graphify](https://github.com/safishamsi/graphify),
   a knowledge-graph skill installed and managed by the `graphify` CLI (`uv tool install graphifyy`).
   Trigger `/graphify`. It is tool-managed — don't hand-edit it; re-run `graphify install --project`
   to refresh. The graph itself lives in `graphify-out/` and PreToolUse hooks live in
   `.claude/settings.json`. See the [graphify section in the root CLAUDE.md](../../CLAUDE.md#graphify).

---

## WordPress engineering skills (official, full set)

Installed from `WordPress/agent-skills`. These are a **coordinated set** — entry
skills (`wordpress-router`, `wp-project-triage`) route to the others and several
cross-reference each other, so the full set is installed to keep routing intact.

Most relevant to **this plugin** (a request-blocking plugin with an admin
settings UI): `wp-plugin-development`, `wp-performance`,
`wp-plugin-directory-guidelines`, `wp-phpstan`, `wp-wpcli-and-ops`,
`wp-rest-api`. The block/theme/abilities/interactivity skills
(`wp-block-development`, `wp-block-themes`, `wp-interactivity-api`,
`wp-abilities-*`, `wpds`, `blueprint`, `wp-playground`) are included for
completeness but are less central here.

| Skill | Covers |
|-------|--------|
| `wordpress-router` | Repository classification and workflow routing (entry point) |
| `wp-project-triage` | Project-type detection and tooling identification |
| `wp-plugin-development` | Plugin architecture, hooks, activation/uninstall, Settings API, security, packaging |
| `wp-performance` | Profiling, caching, query/optimization review |
| `wp-plugin-directory-guidelines` | WordPress.org Plugin Directory compliance |
| `wp-phpstan` | Static analysis config for WordPress |
| `wp-wpcli-and-ops` | WP-CLI commands and automation |
| `wp-rest-api` | REST API endpoints, schema, authentication |
| `wp-block-development` | Gutenberg blocks (block.json, attributes, deprecations) |
| `wp-block-themes` | Block themes (theme.json, templates, patterns) |
| `wp-interactivity-api` | Frontend interactivity (`data-wp-*` directives) |
| `wp-abilities-api` | Capability-based permissions / REST auth |
| `wp-abilities-audit` | REST surface auditing |
| `wp-abilities-verify` | Abilities API registration verification |
| `wpds` | WordPress Design System |
| `wp-playground` | Instant local WordPress environments |
| `blueprint` | Playground environment setup declarations |

> Note: the upstream repo's `shared/` folder (build scripts + version-map JSON)
> is **not** installed — it is tooling for the upstream build, not needed at
> runtime by the skills.

---

## Superpowers methodology skills (curated)

## What was installed and why

Superpowers ships 14 skills. This project is a **plain-PHP WordPress plugin** with
**no test framework, no build step, and no git repository**, so the selection was
curated to the methodology skills that fit — a coherent
**design → plan → execute → debug → verify → review** loop:

| Skill | Purpose | Why it fits here |
|-------|---------|------------------|
| `using-superpowers` | Meta/bootstrap: how to find and use skills | Teaches the skill-discovery protocol the others rely on |
| `brainstorming` | Explore intent & design before coding | Forces a spec before adding plugin features |
| `writing-plans` | Turn a spec into a step-by-step plan | Structures multi-step changes |
| `executing-plans` | Execute a plan with review checkpoints | Pairs with writing-plans |
| `systematic-debugging` | Root-cause a bug before fixing | Directly useful for blocker/remover/service-worker bugs |
| `verification-before-completion` | Prove work with evidence before claiming done | Critical — there is no test suite, so changes are verified manually |
| `requesting-code-review` | Review work before integrating | General quality gate |
| `receiving-code-review` | Handle review feedback rigorously | General quality gate |

## Deliberately NOT installed (and why)

These were skipped because they depend on infrastructure this project doesn't have.
Add any of them later with `cp -R` from the source repo if the project gains it.

| Skill | Reason skipped |
|-------|----------------|
| `test-driven-development` | No PHP test runner / harness configured in this repo |
| `using-git-worktrees` | Not a git repository |
| `finishing-a-development-branch` | Branch/PR workflow; not a git repository |
| `subagent-driven-development` | Heavy orchestration; overkill for a small single-plugin codebase |
| `dispatching-parallel-agents` | Parallel-agent orchestration not needed at this scale |
| `writing-skills` | Only needed when authoring new skills |

## Updating

**WordPress skills** — re-clone the official repo and copy all `wp-*` folders:

```bash
git clone --depth 1 https://github.com/WordPress/agent-skills /tmp/wp-agent-skills
for d in /tmp/wp-agent-skills/skills/*/; do
  n=$(basename "$d"); rm -rf ".claude/skills/$n" && cp -R "$d" ".claude/skills/$n"
done
```

**Superpowers skills** — re-clone and copy the curated folders:

```bash
git clone --depth 1 https://github.com/obra/superpowers /tmp/superpowers
for s in using-superpowers brainstorming writing-plans executing-plans \
         systematic-debugging verification-before-completion \
         requesting-code-review receiving-code-review; do
  rm -rf ".claude/skills/$s" && cp -R "/tmp/superpowers/skills/$s" ".claude/skills/$s"
done
```

## Licenses

- WordPress skills: see [WordPress/agent-skills](https://github.com/WordPress/agent-skills) `LICENSE`.
- Superpowers skills: MIT (Jesse Vincent). See the upstream repo for full terms.
