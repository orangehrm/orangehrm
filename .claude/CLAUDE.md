# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repo is

OrangeHRM Starter — the open-source edition of OrangeHRM (HRMS). Server is PHP on top of Symfony components + Doctrine ORM; client is a Vue 3 Multi page application with Backend Symfony routing. Distributed via SourceForge + Docker; current version tracked in `build/build.xml` (`<property name="version" .../>`) and `CHANGELOG.TXT`.

PHP target: `^7.4|^8.0`. CI matrix runs against MySQL 5.7 and MariaDB 10.3 on PHP 8.3, and also validates install on PHP 8.4.

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

## Plugin architecture (read before adding a module)

A plugin is wired up by **one entry class** at `src/plugins/orangehrm{Name}Plugin/config/{Name}PluginConfiguration.php`:

- Implements `OrangeHRM\Framework\PluginConfigurationInterface`. Its `initialize(Request)` is called on every request by `Framework::configurePlugins()` and registers services into the DI container (`ServiceContainer`).
- Optionally implements `ConsoleConfigurationInterface` to register Symfony Console commands (picked up by `bin/console`).
- Routes are declared in the sibling `config/routes.yaml`. REST endpoints route to `OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle` with `_api: <FQCN of Endpoint>` in defaults; the controller dispatches `GET/POST/PUT/DELETE` to the endpoint's CRUD methods.
- Doctrine entities are autoloaded via the multi-path `OrangeHRM\Entity\` PSR-4 mapping in `src/composer.json` — **add new entity dirs there** when introducing them.

REST endpoints extend `OrangeHRM\Core\Api\V2\Endpoint` and implement `CrudEndpoint` / `CollectionEndpoint` / `ResourceEndpoint`. Param parsing uses `RequestParams` + `ParamRuleCollection` validators. Responses are `EndpointResourceResult` / `EndpointCollectionResult` wrapping a model class (see `Api/Model/*`).

Authorization is enforced via subscribers in `orangehrmCorePlugin/Subscriber/` (`ApiAuthorizationSubscriber`, `ScreenAuthorizationSubscriber`); role/data-group rules are seeded by migrations and by `devTools/core/console.php add-role-permission` / `add-data-group`.

## Local development is Docker-based

This repo is **not** intended to run against a host-installed PHP/MySQL/Node stack. The team standard is the companion repo [`orangehrm-os-dev-environment`](https://github.com/orangehrm/orangehrm-os-dev-environment) — Nginx + per-PHP-version containers (`php-7.4`…`php-8.3`) + a choice of MySQL/MariaDB containers, all defined via `docker compose`. The OHRM source tree is bind-mounted into the containers as `/var/www`, so edits show up live without a restart. PHP CLI, Composer, and Yarn commands all run **inside** the relevant PHP container (typically `os_dev_php83`).

Details — container layout, hostnames (`http://php83/<subpath>/`), service list, `LOCAL_SRC` mounting convention, common `docker compose` invocations — live in `.claude/skills/dev-environment/SKILL.md`. **Read that skill first** if a task involves running anything locally, switching PHP/DB versions, or rebuilding containers.

For a brand-new developer setting up from scratch, the interactive walkthrough is `/ohrm-onboard`. The command commands below are what to run *once you're shelled into the PHP container* (or, in rare cases, on a host that already has PHP/Composer/Node installed).

## Common commands

All from repo root unless noted. Where these run depends on context — see "Local development is Docker-based" above; the default assumption is "inside the PHP container, with the OHRM checkout mounted under `/var/www/<subpath>/`."

### Install / bootstrap
```bash
composer install -d src
composer install -d devTools/core
cd src/client && yarn install && cd -
cd src/test/functional && yarn install && cd -      # only if running Cypress
cd installer/client && yarn install && cd -         # only if touching installer UI

# Fresh install — interactive, current path:
php installer/console install:on-new-database
# Or against an already-created empty DB:
php installer/console install:on-existing-database
```

For initial install, the **web installer at `http://php83/<subpath>/installer/`** is the easiest path — same prompts as the CLI command, but in the browser.

### Backend tests (PHPUnit)
Tests require a populated test DB — create it first (DB host is the *container* name when run inside the PHP container, e.g. `-H mariadb103`; defaults to `127.0.0.1` otherwise):
```bash
php devTools/core/console.php instance:create-test-db -p root --dump-options=--ssl=0
./src/vendor/bin/phpunit                            # all suites
./src/vendor/bin/phpunit --testsuite Pim            # one plugin (names in phpunit.xml)
./src/vendor/bin/phpunit src/plugins/orangehrmPimPlugin/test/Dao/EmployeeDaoTest.php
./src/vendor/bin/phpunit --filter testGetEmployeeById <path-to-test>
```
`instance:` commands have an `i:` shorthand (e.g. `i:create-test-db`, `i:reset`, `i:reinstall`) — CI scripts use the short form.

### Frontend tests (Jest, in `src/client/`)
```bash
cd src/client
yarn test:unit                                      # all
yarn test:unit path/to/file.spec.ts                 # single file
```

### Linting
```bash
cd src/client && yarn lint                          # Vue/TS, --max-warnings=0
cd installer/client && yarn lint
cd src/test/functional && yarn lint                 # Cypress
php devTools/core/console.php php-cs-fix --php php8.3   # PHP-CS-Fixer (auto-fix); CI fails if it changes any file
```

### Build / run
```bash
cd src/client && yarn serve                         # dev server
cd src/client && yarn dev                           # build --watch into web/dist
cd src/client && yarn build                         # production build into web/dist
php devTools/core/console.php i:reset               # wipe & re-init DB
php devTools/core/console.php i:reinstall           # re-run installer against current config
php bin/console cache:clear
php bin/console orm:generate-proxies
php devTools/core/console.php generate-open-api-doc --throw   # rebuild build/orangehrm-v2.json
```

Cypress lives in `src/test/functional/` — run `yarn open` (interactive) or `yarn test` (headless) from there.

## Conventions to follow

- **Branch and commit naming.** Every branch and commit starts with a JIRA ticket key: e.g. branch `OHRM5X-1234`, commit `OHRM5X-1234: Add employee export endpoint`. Visible throughout `git log`. PRs are squash-merged by reviewers.
- **License header.** Every PHP and `.vue` source file starts with the GPL header block from existing files. PHP-CS-Fixer doesn't check it but reviewers do.
- **PHP style.** PSR-12 + the project's `.php-cs-fixer.dist.php` (short array syntax, no unused imports, etc.). Always run `php-cs-fix` before committing; CI hard-fails if it touches any file.
- **Adding a REST endpoint.** Create an `Api/{Name}API.php` extending `Endpoint` (+ relevant CRUD interface), register the path in the plugin's `config/routes.yaml` pointing at `GenericRestController::handle` with `_api` set to your FQCN. Don't add a per-endpoint controller.
- **Adding a Doctrine entity.** Place it in `plugins/orangehrm{X}Plugin/entity/`. If the plugin isn't already in the `OrangeHRM\Entity\` PSR-4 array in `src/composer.json`, add it there and run `composer dump-autoload -d src` so Doctrine can find it.
- **DB schema changes.** Always go through a new migration under `installer/Migration/V{next_version}/` — never edit an existing migration. Bump the version in `build/build.xml` and append a `CHANGELOG.TXT` entry when releasing.
- **Frontend plugin code** belongs under `src/client/src/orangehrm{X}Plugin/` (mirror the backend plugin name). Page entry points are registered via `src/client/src/pages.ts`.

## Things that bite

- `composer.json` is in `src/`, not the repo root — running `composer …` from the root does nothing useful.
- After autoload changes, the `post-autoload-dump` script runs `bin/console orm:generate-proxies` and `cache:clear`; if it fails the autoload still succeeded but Doctrine proxies are stale — re-run those commands manually.
- PHPUnit's bootstrap (`src/test/phpunit/Util/bootstrap.php`) refuses to run if `instance:create-test-db` hasn't been executed; the error message tells you the exact command.
- **DB hostname inside containers is the DB container's name** (e.g. `mariadb103`), not `127.0.0.1`. This trips up devs running CLI commands inside the PHP container.
- The lint job re-runs `php-cs-fix` and fails on **any** `git status --porcelain` output — leave no other uncommitted changes when running it locally if you want to mirror CI.
- `OrangeHRM\Entity\` is a multi-path PSR-4 namespace. Forgetting to add a new plugin's `entity/` dir there causes silent "class not found" failures in Doctrine mappings only — code may still autoload via other paths.
