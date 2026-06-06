# AGENTS.md

Primary instruction document for AI coding agents working in this repository. Claude Code reads this via the root `CLAUDE.md → @AGENTS.md` import; AGENTS.md-aware tools (Cursor, Codex, others) discover it directly.

## Session start (do this first, every new session)

Before your first reply in a fresh session, run a quick skill-bridge check and greet the developer. Do this **once** at session start — don't repeat it on later turns. Run the check **silently first** — no "Welcome…" preamble before or alongside it — then emit the greeting exactly once, so the developer sees a single "👋 Welcome to OrangeHRM Starter".

1. **Check the bridge for your tool — by running a command, never from memory or assumption.** You have not done this check until a tool call's output proves it. Claude Code: run `ls -d .claude/skills/*/ 2>/dev/null | wc -l` (the trailing-slash glob counts only skill directories, excluding the `README.md` catalog) and read the count from the output. Cursor: list `.cursor/rules/`. Codex / other AGENTS.md tools: no bridge needed — you read `.agents/` directly, so treat skills as loaded.

   > **Hard gate:** Do **not** say "the bridge is loaded", "you're all set", or anything implying skills are active unless the command above actually ran *this turn* and returned a non-empty count. If you haven't run it, you don't know — treat the bridge as missing and run the check before greeting. Asserting the result without the command is a failure.
2. **Bridge missing** (while `.agents/skills/` has content) → the project's 28 skills + commands are NOT loaded in your tool. Greet, say so, and hold off on deep project work until it's fixed:
   > 👋 Welcome to OrangeHRM Starter. This repo ships 28 skills + slash commands to help with the codebase, but they aren't loaded in your tool yet. Run the one-time setup — prompt me: *"Please follow `.agents/SETUP.md` to set yourself up for this project."* — then start a new session.
3. **Bridge loaded** (or you're Codex) → greet and point new developers at onboarding:
   > 👋 Welcome to OrangeHRM Starter. First time here? Run `/onboard` to stand up the Docker dev environment. Otherwise, ask away — 28 skills auto-load by topic (`.agents/skills/README.md` is the catalog). And if you need links to the project's external resources — source repos, docs, downloads, the demo site, mobile apps — just ask; I'll pull them up.

## Skills and commands

This repository ships **28 project-level skills** + **slash commands** under `.agents/`:

- **Skills** — architecture/convention/recipe documents that auto-load by task description. See [`.agents/skills/README.md`](.agents/skills/README.md) for the catalog.
- **Slash commands** — `.agents/commands/<name>.md` files invoked as `/<name>` in Claude Code (or the equivalent in other tools). Current commands: `/onboard` (new-dev setup walkthrough), `/agent-sync` (re-sync the generated `.claude/` and `.cursor/` bridges after editing).

> **If skills or commands aren't loading in your tool**, run the one-time setup: prompt the agent with *"Please follow `.agents/SETUP.md` to set yourself up for this project."*. Once loaded you may have to start a new session. For **Claude Code** it creates symlinks (Linux/macOS/WSL2) or copies (Windows) from `.claude/skills/` and `.claude/commands/` → the matching `.agents/` paths, since Claude Code only auto-discovers under `.claude/`. For **Cursor** it generates thin pointer rules under `.cursor/rules/` (one `.mdc` per skill, Agent-Requested by description) plus `.cursor/commands/` copies. Codex and other AGENTS.md-aware tools need no setup — they read `.agents/` in place.

**Before generating or updating any skill or command — or anything else under `.agents/` — read [`.agents/AUTHORING.md`](.agents/AUTHORING.md).** It is the single source of truth for authoring conventions: where files live (always `.agents/`, never the generated `.claude/` or `.cursor/` bridges), tool-neutral bodies, trigger-rich `description`s, bare kebab-case naming, frontmatter, and file structure — the rules that keep skills working across all coding agents (Claude Code, Cursor, Codex, Antigravity, Copilot, and other `AGENTS.md`-aware tools).

## What this repo is

OrangeHRM Starter — the open-source edition of OrangeHRM (HRMS). Server is PHP on top of Symfony components + Doctrine ORM; client is a Vue 3 Multi page application with Backend Symfony routing. Distributed via SourceForge + Docker; current version tracked in `build/build.xml` (`<property name="version" .../>`) and `CHANGELOG.TXT`.

External OrangeHRM Starter ecosystem references — related repos, public docs, release/download channels, mobile apps, demo, DockerHub, and AWS Marketplace links — live in [`.agents/skills/ecosystem/SKILL.md`](.agents/skills/ecosystem/SKILL.md).

PHP support is defined by the Composer projects themselves (for the main app, read `src/composer.json`; for dev tooling, read `devTools/core/composer.json`). Installer runtime checks live in `installer/config/system_requirements.php`. CI support and database/PHP matrices are defined by the GitHub Actions workflows under `.github/workflows/`; inspect the current source files instead of relying on hardcoded versions in agent docs. See the `compatibility` and `dependencies` skills before changing supported versions or package metadata.

## Layout — the parts that matter

- `src/` — the deployed app. `composer.json` lives here, not at the root, so all composer/phpunit commands run from `src/`.
- `src/plugins/orangehrm{Name}Plugin/` — every business module (Pim, Leave, Time, Admin, Auth, …) is a self-contained plugin. **Almost all backend work happens inside one of these.** Typical subfolders: `Api/` (REST endpoints), `Controller/` (page controllers), `Dao/`, `Service/`, `entity/` (Doctrine entities), `Dto/`, `config/` (`{Name}PluginConfiguration.php` + `routes.yaml`), `Vue/` (server-side Vue page wiring), `Menu/`, `test/`.
- `src/client/` — Vue 3 SPA (Vue CLI + TS + SCSS). Per-plugin Vue source lives in `src/client/src/orangehrm{Name}Plugin/` (`components/`, `pages/`). Built artifacts are emitted to `src/../web/dist`.
- `src/lib/` — framework glue not specific to any plugin: `framework/` (HttpKernel subclass, DI container, routing, console), `orm/` (Doctrine setup), `config/` (`Config` constants + helpers).
- `installer/` — web + CLI installer and **all DB migrations** (`installer/Migration/V{x_y_z}/`). Run via `php installer/console install:on-new-database` or the web installer at `/installer/`.
- `devTools/core/` — developer-only Symfony Console app with its own `composer.json`. Entry point: `php devTools/core/console.php`. Hosts code-style fix, test-DB creation, OpenAPI doc generation, role/permission seeders, etc.
- `bin/console` — production console (cache:clear, orm:generate-proxies, plus commands registered by plugins).
- `src/test/phpunit/` — shared PHPUnit fixtures & helpers. Per-plugin tests live in `src/plugins/*/test/`.
- `src/test/functional/` — Cypress E2E tests (separate `yarn` workspace).
- `web/` — public document root (`index.php` bootstraps `src/lib/framework/Framework`).

## Plugin architecture

Every backend module (Pim, Leave, Time, Admin, Auth, …) is a self-contained plugin under `src/plugins/orangehrm{Name}Plugin/`, wired up by **one entry class** — `config/{Name}PluginConfiguration.php` (implements `PluginConfigurationInterface`, registers services on each request) — with routes in the sibling `config/routes.yaml`.

The mechanics are documented in the skills, which are the source of truth — read the relevant one before working in that layer:

- REST endpoints, `routes.yaml`, request/response shaping → `rest-endpoints` (+ `rest-validation`, `rest-serialization`, `rest-openapi`)
- Service layer & DI registration → `services`; single-entity persistence → `daos`
- Doctrine entities & the `OrangeHRM\Entity\` PSR-4 mapping → `entities` (+ `doctrine-bootstrap`)
- Role/data-group authorization → `authorization`
- Plugin-registered console commands → `console-commands`

## Local development is Docker-based

This repo is **not** intended to run against a host-installed PHP/MySQL/Node stack. The team standard is the companion repo [`orangehrm-os-dev-environment`](https://github.com/orangehrm/orangehrm-os-dev-environment) — Nginx + per-PHP-version + MySQL/MariaDB containers via `docker compose`, with the OHRM source tree bind-mounted into the PHP container. So PHP CLI, Composer, and Yarn commands all run **inside** that container.

Container layout, hostnames, the PHP/DB version matrix, `LOCAL_SRC`, and the `docker compose` invocations are documented in the **`dev-environment`** skill — **read it first** for anything involving running locally, switching PHP/DB versions, or rebuilding containers. Brand-new setup from scratch: run **`/onboard`**. The commands below assume you're shelled into the PHP container.

## Common commands

A minimal always-on reference. The owning skills carry the full set with current flags/versions — don't reproduce those from memory here.

```bash
composer install -d src && composer install -d devTools/core   # backend deps
cd src/client && yarn install && cd -                           # frontend deps
php installer/console install:on-new-database                   # fresh install (or web installer at /installer/)
php devTools/core/console.php php-cs-fix                         # PHP style — run before every commit; CI fails if it changes a file
php bin/console cache:clear
```

For everything else, go to the skill that owns it — it is the source of truth:

- **Run tests** — PHPUnit / Jest / Cypress, plus the required `instance:create-test-db` step → `testing`
- **Composer / Yarn dependency commands** → `dependencies`
- **Serve / build, Docker, DB reset & reinstall** → `dev-environment`
- **Which console** (`bin/console` vs `devTools/core/console.php`) and how commands register → `console-commands`
- **Regenerate the OpenAPI doc** → `rest-openapi`

## Conventions to follow

Project-wide conventions no single skill owns:

- **Branch and commit naming.** Every branch and commit starts with a JIRA ticket key: e.g. branch `OHRM5X-1234`, commit `OHRM5X-1234: Add employee export endpoint`. Visible throughout `git log`. PRs are squash-merged by reviewers.
- **License header.** Every PHP and `.vue` source file starts with the GPL header block from existing files. PHP-CS-Fixer doesn't check it but reviewers do.
- **PHP style.** PSR-12 + the project's `.php-cs-fixer.dist.php` (short array syntax, no unused imports, etc.). Always run `php-cs-fix` before committing; CI hard-fails if it touches any file.

Layer-specific recipes — adding a REST endpoint, a Doctrine entity, a DB migration, or frontend plugin code — live in the matching skills (`rest-endpoints`, `entities`, `migrations`, `frontend-pages`), which are the source of truth for those steps.

## Things that bite

- `composer.json` is in `src/`, not the repo root — running `composer …` from the root does nothing useful.
- After autoload changes, the `post-autoload-dump` script runs `bin/console orm:generate-proxies` and `cache:clear`; if it fails the autoload still succeeded but Doctrine proxies are stale — re-run those commands manually.
- PHPUnit's bootstrap (`src/test/phpunit/Util/bootstrap.php`) refuses to run if `instance:create-test-db` hasn't been executed; the error message tells you the exact command.
- **DB hostname inside containers is the DB container's name** (e.g. `mariadb103`), not `127.0.0.1`. This trips up devs running CLI commands inside the PHP container.
- The lint job re-runs `php-cs-fix` and fails on **any** `git status --porcelain` output — leave no other uncommitted changes when running it locally if you want to mirror CI.
- `OrangeHRM\Entity\` is a multi-path PSR-4 namespace. Forgetting to add a new plugin's `entity/` dir there causes silent "class not found" failures in Doctrine mappings only — code may still autoload via other paths.
