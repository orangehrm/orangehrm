---
description: Interactive setup for a new OrangeHRM developer — sets up the Docker dev environment, mounts this repo into a PHP/DB container stack, installs the app, and verifies it loads in the browser.
---

You are guiding a new developer through standing up a local OrangeHRM development environment. They have just cloned this repo and invoked `/ohrm-onboard` from inside it. **The OrangeHRM team uses a Docker-based dev environment** — the developer does NOT install PHP, MySQL, or Node directly on their machine. They install Docker + Git, then everything else runs in containers from the companion repo [`orangehrm-os-dev-environment`](https://github.com/orangehrm/orangehrm-os-dev-environment).

Before starting, **read `.claude/skills/dev-environment/SKILL.md`** — it has the container naming, services list, and conventions you'll keep referring to. The steps below are the onboarding sequence; the skill is the reference you cross-check against when answering follow-ups or troubleshooting.

## How to run this

- **One step at a time.** Don't dump the full plan up front. State the goal of the current step, run or have the dev run each command, check it actually worked, then proceed.
- **Verify after each step.** Don't trust "it printed something" — run a small check. After `docker compose build`, list images. After `docker compose up`, `docker ps`. After `composer install`, check `src/vendor` exists. Catch failures before they cascade.
- **Adapt to their environment.** Linux distro / macOS / Windows+WSL. Docker Desktop vs. Docker Engine. Whether the user is in the `docker` group (Linux) — if not, every `docker` call needs `sudo` and that's a friction point worth fixing once with `sudo usermod -aG docker $USER` + relog.
- **Confirm before destructive or system-level actions.** Editing `/etc/hosts`, `usermod`, anything `sudo`. Show the change you'd make first.
- **Use TaskCreate** to track the steps so progress is visible. The list below maps roughly 1:1 to tasks.

If they come back partway through, ask what they finished last and resume — don't restart.

## Step 0 — Greet and orient

Briefly tell them: you'll set up a Docker-based dev environment in which this repo will be served by Nginx + PHP-FPM, backed by MariaDB, and reachable in their browser. Time: ~30–60 min, mostly the first docker image build. Confirm they want to proceed.

Ask:
- What OS are they on? (Linux distro / macOS / Windows+WSL2)
- Where is this repo currently cloned? Record the absolute path — you'll need it for `LOCAL_SRC`. Get it with `pwd`.

## Step 1 — Verify prerequisites

Only three things are needed on the host:

| Tool | Required | Check |
|---|---|---|
| Docker engine | 19.03+ (Docker Desktop is fine on macOS/Windows) | `docker --version`, then `docker info` to confirm the daemon is reachable |
| Docker Compose | v2 plugin (`docker compose`) **or** legacy `docker-compose` 1.25+ — see below | run *both* checks |
| Git | any recent version | `git --version` |

If Docker isn't installed, point them at https://docs.docker.com/get-docker/ and stop until it's done — don't try to install Docker via a one-liner; the right path differs significantly by OS.

### Detect which Compose command this dev has

Run **both** checks and record which one succeeds — you'll use this throughout the rest of the walkthrough:

```bash
docker compose version       # v2 plugin form
docker-compose --version     # legacy standalone binary
```

- If `docker compose version` succeeds → use `docker compose ...` (v2 plugin form) in every later step.
- If only `docker-compose --version` succeeds → use `docker-compose ...` (hyphenated, legacy form). **Substitute this form in every later command in this walkthrough.** The two are command-compatible; only the invocation differs.
- If both succeed → prefer `docker compose` (v2) — it's the actively maintained form.
- If neither succeeds → they have Docker but no Compose. On Linux: `sudo apt install docker-compose-plugin` (Debian/Ubuntu) installs the v2 plugin. On Docker Desktop installs, Compose ships in the box — re-check with the right path.

From here on in this walkthrough, write commands in whichever form they have, and call out the choice once at the top of each step that uses Compose so they don't get confused.

**On Linux specifically**, check whether they're in the `docker` group with `groups | grep docker`. If not, recommend `sudo usermod -aG docker $USER` followed by logout/login (or `newgrp docker` for the current shell) so they don't need `sudo` for every command. Don't do this without asking — it modifies their user.

Also recommend (but don't push) **PHPStorm** as the IDE the team standardizes on. They can install it later.

## Step 2 — Configure Git for OrangeHRM contributions

OrangeHRM commits must be authored with the developer's **OrangeHRM email** (`@orangehrm.com`), not a personal one. Inspect the global config first, then configure repo-locally only as needed.

### Inspect the current global git identity

```bash
git config --global user.name
git config --global user.email
```

Three cases:

1. **Global email already ends with `@orangehrm.com`** → no email change needed. Their everyday git identity matches what OHRM wants. Move on to the `core.filemode` step below.

2. **Global email is a personal/other email** (e.g. `@gmail.com`, `@outlook.com`, anything not `@orangehrm.com`) → **do not change the global config** (they likely use it for personal projects). Instead, set their OrangeHRM identity at the **project level** (inside this OHRM clone) so commits here use the right address. Ask them for their OrangeHRM name and email, then from inside the OHRM repo:

   ```bash
   git config user.name "Their Name"
   git config user.email "their.email@orangehrm.com"
   ```

   No `--global` flag — this writes to `.git/config` and applies only to this repo. Show them the diff (or `cat .git/config`) so they see the scope is local.

3. **No global email set at all** → ask whether they prefer their OrangeHRM email globally (fine if this machine is work-only) or only at the project level for this repo. Default to the **project level** for safety — same commands as case 2.

### Set `core.filemode false` (project level)

This is a per-repo setting — it tells git to ignore Unix file-mode bits when detecting changes, which avoids spurious diffs when files are touched by Docker volume mounts or by collaborators on different OSes. Set it inside this OHRM clone:

```bash
git config core.filemode false
```

Again no `--global` — applies only to this repo. If they'll work on multiple OHRM clones in the future they should run this in each, or set `git config --global core.filemode false` once if they always want this behavior on every repo they touch.

### Verify

From inside the OHRM repo:

```bash
git config --list --local | grep -E '^user\.|^core\.filemode'
```

Confirm `user.email` ends with `@orangehrm.com` (whether it's coming from global in case 1 or local in cases 2–3) and `core.filemode=false` is present.

## Step 3 — Clone the Docker dev-environment repo

The team convention is to place it under `~/Documents/` next to OrangeHRM clones:

```bash
git clone https://github.com/orangehrm/orangehrm-os-dev-environment ~/Documents/orangehrm-os-dev-environment
cd ~/Documents/orangehrm-os-dev-environment
mkdir -p html
cp .env.dist .env
```

If `~/Documents` doesn't exist or they prefer elsewhere, swap the path — remember it, you'll use it repeatedly.

## Step 4 — Decide where the OrangeHRM source lives, and set `LOCAL_SRC`

`LOCAL_SRC` in `.env` is the host directory mounted into every container as `/var/www`. Two options:

**Option A (team standard) — move/clone OHRM under `html/`.** Multiple OHRM versions can coexist there, each accessible at `http://php83/<dir-name>/`. If this is what they want, suggest moving the current clone:
```bash
mv <current OHRM path> ~/Documents/orangehrm-os-dev-environment/html/
```
Then `LOCAL_SRC` should be the absolute path of `html/`:
```
LOCAL_SRC=/home/<user>/Documents/orangehrm-os-dev-environment/html
```

**Option B — keep OHRM where it is, point `LOCAL_SRC` at its parent.** No moving required. Equivalent result, slightly less conventional. Use this if they have a reason to keep the clone in place (e.g. existing IDE project setup).

Either way, edit `~/Documents/orangehrm-os-dev-environment/.env` so `LOCAL_SRC=…` is set to the chosen directory. Leave `MYSQL_ROOT_PW=root` and the rest at defaults for now. Show the dev the diff before saving.

Remember the **subpath** the OHRM repo ends up at under `LOCAL_SRC` — they'll access it as `http://php83/<subpath>/`. Record it; you'll need it in Step 8.

## Step 5 — Add the PHP hostnames to /etc/hosts

The Nginx container routes by hostname, so each PHP container has its own short hostname mapped to localhost:

```
127.0.0.1   php56 php70 php71 php72 php73 php74 php80 php81 php82 php83
```

This requires sudo. Show them the exact line and confirm before running:
```bash
echo '127.0.0.1   php56 php70 php71 php72 php73 php74 php80 php81 php82 php83' | sudo tee -a /etc/hosts
```

Verify: `getent hosts php83` should return `127.0.0.1 php83`.

(On macOS the file is the same path. On Windows+WSL2, edit `C:\Windows\System32\drivers\etc\hosts` from an Administrator Notepad — WSL inherits host resolution, but only the Windows file has effect.)

## Step 6 — Build the Docker images

CI runs PHP 8.3 + MariaDB 10.3, so build at least those. The first build pulls base images and compiles PHP extensions — expect 5–15 minutes on a typical machine, longer on first run with cold caches.

```bash
cd ~/Documents/orangehrm-os-dev-environment
docker compose build nginx php-8.3
```

Add other PHP versions later only if they need to test compatibility (e.g. `php-7.4 php-8.2`). Legacy versions (PHP ≤ 7.3) need `-f docker-compose-legacy-services.yml`.

Verify: `docker images | grep -E 'php-8.3|nginx'` should show recent images.

## Step 7 — Start the stack

```bash
docker compose up -d phpmyadmin php-8.3 mariadb103
```

Verify all three started:
```bash
docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}' | grep os_dev
```
Expect to see `os_dev_php83`, `os_dev_mariadb103`, `os_dev_phpmyadmin`, and `os_dev_nginx` (nginx auto-starts as a dependency).

If a container is missing or restarting, check its logs: `docker compose logs <service>`.

Also sanity-check phpMyAdmin loads: open **http://localhost:9092** in the browser — login `root` / `root`, pick `mariadb103` from the server dropdown. If the page loads and connects, the DB container is healthy.

## Step 8 — Install backend and frontend dependencies (inside the container)

From here on, commands run **inside the PHP container**, not on the host:

```bash
docker exec -it os_dev_php83 bash
```

Inside the container, navigate to where the OHRM repo is mounted. If `LOCAL_SRC=…/html` and the repo is in `html/orangehrm-5x/`, then inside the container it's at `/var/www/orangehrm-5x/`. Cd there, then:

```bash
composer install -d src
composer install -d devTools/core
cd src/client && yarn install && yarn dev
```

`yarn dev` runs a webpack build in watch mode against `web/dist`. For an initial setup, a one-off `yarn build` is also fine — switch to `yarn dev` later when actively working on Vue.

Optional, only if relevant:
- `cd installer/client && yarn install && yarn dev` — only if they'll change the installer UI
- `cd src/test/functional && yarn install` — only if they'll run Cypress E2E tests (Cypress itself usually runs on the host, not in the container)

Exit the container with `exit` when done; the install state persists because everything went into the mounted volume.

## Step 9 — Install OrangeHRM (web installer)

In the browser, open `http://php83/<subpath>/` (the subpath you recorded in Step 4). The root `index.php` detects no install yet and redirects to `/installer/index.php`. Walk them through the wizard — recommended local-dev answers:

- **Database host**: `mariadb103` ← this is the *container* hostname; from inside the PHP container it resolves to the DB container. Not `127.0.0.1`.
- **Port**: `3306`
- **Privileged DB user / password**: `root` / `root` (matches `MYSQL_ROOT_PW` in `.env`)
- **Use existing DB**: No (the installer will create one)
- **Database name**: suggest `orangehrm` (any name works; remember it for phpMyAdmin)
- **Use same DB user for OrangeHRM at runtime**: Yes (simpler for dev; in production you'd say No)
- **Enable data encryption**: No (simpler — they can rebuild with encryption later)
- **Organization name / country**: anything
- **Admin user**: pick a username + strong-enough password they'll remember; this is what they'll log in with

Submit. The installer runs migrations and writes `src/config/Conf.php`. When it finishes, they should see a success page.

If they prefer CLI, the equivalent (run inside the container, from the OHRM dir):
```bash
php installer/console install:on-new-database
```
Same prompts, terminal-based.

## Step 10 — Smoke test the login

Open `http://php83/<subpath>/` again — now it redirects to `/web/index.php/auth/login`. Log in with the admin credentials from Step 9. If the dashboard renders, the install is good. Walk them around the menu briefly so they see what a working instance looks like.

If the page is blank or styles are broken, the most common cause is that `yarn dev` / `yarn build` in Step 8 hasn't produced `web/dist/` yet — check inside the container.

## Step 11 — (Optional) Set up the PHPUnit test DB

Skip if they're not running backend tests yet — they can come back later.

Inside the container, from the OHRM directory:
```bash
php devTools/core/console.php instance:create-test-db -p root
./src/vendor/bin/phpunit --testsuite Core
```

If the Core suite passes, the test environment is healthy.

(The `i:` prefix in CI scripts — `i:create-test-db`, `i:reset`, `i:reinstall` — is just a shorthand alias for `instance:`. Either works.)

## Step 12 — Quick tour of the codebase

Briefly walk them through what they're looking at — short, not a lecture:
- **Plugin-per-module backend** under `src/plugins/orangehrm{X}Plugin/`. Show one plugin (e.g. `orangehrmPimPlugin`): `Api/`, `Dao/`, `Service/`, `entity/`, `config/`. Each plugin is self-contained.
- **Plugin entry class**: `{Name}PluginConfiguration.php` + sibling `routes.yaml`. Routes point at `GenericRestController::handle` with `_api: <FQCN>` and the controller dispatches CRUD methods on the endpoint.
- **Vue side of each plugin** lives in `src/client/src/orangehrm{X}Plugin/`.
- **Two console entry points**: `bin/console` (prod) vs. `devTools/core/console.php` (dev tools: `php-cs-fix`, `instance:reset/reinstall`, `generate-open-api-doc`, etc.).
- **The repo's `.claude/CLAUDE.md`** is what guides Claude when working in this codebase — the contribution conventions live there.

If they already know what module they're starting on, point at its files specifically (Pim → `src/plugins/orangehrmPimPlugin/Api/` + `src/client/src/orangehrmPimPlugin/pages/`).

## Step 13 — Contribution workflow primer

The team uses **JIRA-ticket-prefixed branches and commits** (visible in `git log`: e.g. `OHRM5X-2640: Commit trial code partially`). Tell them:

- Branch off the versioned target branch (currently `5.x` for active development, `main` for trunk) using their ticket: `git checkout -b OHRM5X-NNNN`.
- Commit messages: `OHRM5X-NNNN: <short imperative description>`.
- For OSS contributions: fork `orangehrm/orangehrm` to their own GitHub account, clone the fork (not the upstream), push branches to the fork, open PRs against the upstream's versioned branch.
- PRs are squash-merged by reviewers — no need to rebase before merge.

If they're an OrangeHRM employee with push access to the org repo, the fork step is optional; otherwise it's required.

## Step 14 — Wrap up

Summarize what they have running, and the day-to-day commands they'll use most:

```bash
# Start the stack (from the dev-env repo)
cd ~/Documents/orangehrm-os-dev-environment
docker compose up -d phpmyadmin php-8.3 mariadb103

# Stop the stack
docker compose down

# Shell into the PHP container to run composer/yarn/console
docker exec -it os_dev_php83 bash

# Tail logs
docker compose logs -f php-8.3
docker compose logs -f nginx

# Browse the app                              http://php83/<subpath>/
# Browse the DB (phpMyAdmin, root/root)       http://localhost:9092
```

Tell them: the codebase is mounted, not baked in — edits show up live. Restarting containers is only needed if they change PHP/Nginx config.

Ask if they want help finding a first task to work on, or want a deeper dive into a specific plugin. Otherwise end here.

---

**Reminder to Claude:**
- The developer may be new to Docker, Symfony, Doctrine, or Vue — keep explanations short but not skipped. One sentence of "why" per step. When they hit an error, troubleshoot it before moving on; don't paper over it.
- Cross-check details (container names, hostnames, ports, available services) against `.claude/skills/dev-environment/SKILL.md` rather than recalling from memory.
- **Compose command:** after Step 1, you know whether the dev has `docker compose` (v2) or `docker-compose` (legacy). **Every Compose command you write in subsequent steps must use that form** — substitute, don't blindly copy from the example blocks above. If the dev's form is the legacy one, every `docker compose <args>` in this file becomes `docker-compose <args>` when you show it to them.
- **Git scope:** when fixing/changing git identity in Step 2, default to **project-level** (`git config ...`, no `--global`) unless the dev explicitly asked for a global change. Don't silently modify their global git identity.
