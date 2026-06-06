---
name: dev-environment
description: Reference for the OrangeHRM Docker-based local development environment — container layout, hostnames, common docker compose commands, how to shell into PHP containers, how to access the DB, and the LOCAL_SRC mounting convention. Use whenever the user asks about running OrangeHRM locally, which PHP version to use (lowest supported), switching PHP versions, accessing the app over HTTP or HTTPS (ports 80/443), accessing the dev database, rebuilding containers, or anything involving the `orangehrm-os-dev-environment` repo. Also covers troubleshooting a broken dev environment — containers that won't start or keep crashing, image build failures, the app being unreachable at `http://phpXX/`, host port 80/443 conflicts ("port is already allocated" / "address already in use"), "database connection refused", and mount/`LOCAL_SRC` problems — and checking this reference against the `orangehrm-os-dev-environment` companion repo for drift (renamed services, changed PHP/DB matrix, new ports or `.env` keys).
---

# OrangeHRM Docker dev environment

Local dev runs through a **separate companion repo**, [`orangehrm-os-dev-environment`](https://github.com/orangehrm/orangehrm-os-dev-environment), not the OrangeHRM repo itself. Developers do **not** install PHP / MySQL / Node directly on the host. Everything runs in containers. The host only needs Docker, Git, and a text editor (PHPStorm is the recommended IDE).

For the broader set of external OrangeHRM Starter repositories, release channels, docs, demo, mobile app listings, DockerHub image, and cloud package links, see the `ecosystem` skill.

For supported-version policy across Composer constraints, installer requirements, CI workflows, frontend/browser targets, and packaging, see the `compatibility` skill. For Composer/Yarn dependency changes, see the `dependencies` skill.

## Which PHP version to use

The dev-env offers many PHP versions (see "Available services"), but **don't default to the newest.** For installing dependencies and day-to-day work, use the **lowest PHP version the codebase supports** — Composer must resolve on the lowest supported runtime so the packages it selects stay compatible across the whole supported range. Derive that version from `src/composer.json` (`require.php` / `config.platform.php`) **before standing anything up**; the `dependencies` skill owns this rule and the `compatibility` skill covers version alignment. Don't hardcode a number — it changes across releases.

Throughout this skill, **`php-X.Y`** is the Compose service for the version you target (e.g. `php-7.4`) and **`phpXX`** its hostname / container suffix (e.g. `php74` → container `os_dev_phpXX`, URL `http://phpXX/`). Substitute the version you derived. You can run additional PHP versions alongside it later for compatibility testing.

## Repo layout convention

The team standard is:

```
<workspace>/                           ← any directory you keep code in
  orangehrm-os-dev-environment/        ← the dev-env repo
    .env                                ← copied from .env.dist, edited
    html/                               ← document root (created with `mkdir html`)
      <ohrm-checkout-dir>/              ← OrangeHRM source lives in here
        src/
        installer/
        …
    docker-compose.yml
    docker-compose-legacy-services.yml  ← PHP 5.6-7.3 + legacy DBs
```

`LOCAL_SRC` in `.env` points to the **`html/` directory**, which is mounted into every container as `/var/www`. Anything dropped under `html/` becomes accessible in all PHP containers at `/var/www/<subdir>`. Multiple OHRM checkouts can coexist under `html/` (e.g. `html/orangehrm-5x/`, `html/orangehrm-main/`).

If a developer already has OrangeHRM cloned elsewhere and doesn't want to move it, an alternative is to set `LOCAL_SRC` to that clone's parent directory — same effect.

## Recording where the dev environment lives

The companion-repo path, the chosen OHRM subpath, the PHP version in use, and any custom ports are **machine-specific and live nowhere in this OrangeHRM checkout** — this repo can't know where a given developer set things up. Persist them in whatever **per-developer memory / notes store your tool keeps across sessions** — it must be machine-local and **not committed** to this shared repo.

**Recall before re-asking.** At the start of any dev-env task, check whether this is already recorded; if it is, use it instead of making the developer re-derive their setup.

**Record after setup, with the developer's okay** — a quick "want me to remember this for next time?". Save a small, findable block such as:

```
OrangeHRM dev environment (machine-local):
- dev-env repo:  <path to the orangehrm-os-dev-environment checkout>
- OHRM subpath:  html/<dir>   → browse at http://<php-host>/<dir>/ [this can be derived from .env too]
- PHP / DB:      <php-X.Y> / <mariadbNNN> [this can be derived by running `docker ps` too]
- host ports:    80 / 443 (or the remapped ones) [this can be derived from .env too]
- updated:       <date>
```

Recalling this later lets you point the developer straight at their setup — restart the stack, `git pull` the latest companion-repo changes, or re-run an install — instead of re-deriving it every session. Update the block whenever something changes (new PHP version, moved checkout, remapped ports).

## /etc/hosts entry (required)

```
127.0.0.1   php56 php70 php71 php72 php73 php74 php80 php81 php82 php83
```

Each PHP container is reachable via its short name (e.g. `http://phpXX/`). Nginx binds host ports **80 (HTTP) and 443 (HTTPS)** and routes to the right PHP container by hostname.

## Container naming

Pattern: `os_dev_<service>`. E.g. `os_dev_php83`, `os_dev_mariadb103`, `os_dev_phpmyadmin`, `os_dev_nginx`.

## Available services

**PHP** (current compose file): `php-7.4`, `php-8.0`, `php-8.1`, `php-8.2`, `php-8.3`
**PHP legacy** (via `-f docker-compose-legacy-services.yml`): `php-5.6`, `php-7.0`, `php-7.1`, `php-7.2`, `php-7.3`
**MySQL**: `mysql55`, `mysql56`, `mysql57`, `mysql80`, `mysql81`, `mysql82`
**MariaDB**: `mariadb55`, `mariadb100`–`mariadb103`, `mariadb106`–`mariadb109`, `mariadb1010`, `mariadb1011`, `mariadb110`, `mariadb111`, `mariadb112`
**Other**: `nginx`, `phpmyadmin`

The current CI matrix is defined in the GitHub Actions workflows under `.github/workflows/`. Inspect those workflow files before claiming which PHP/database combination mirrors CI most closely.

## Common commands

All `docker compose` commands must run from the root of your `orangehrm-os-dev-environment` checkout. `php-X.Y` / `phpXX` are placeholders for the PHP version you derived in "Which PHP version to use" (`mariadb103` here is just an example DB) — substitute your own.

```bash
# Build images (only the ones you'll use)
docker compose build nginx php-X.Y

# Legacy images (PHP ≤ 7.3) need the second compose file
docker compose -f docker-compose-legacy-services.yml build php-5.6

# Start a stack (DB + chosen PHP + phpMyAdmin)
docker compose up -d phpmyadmin php-X.Y mariadb103

# See what's running
docker ps

# Stop everything
docker compose down

# Shell into a container
docker exec -it os_dev_phpXX bash

# Tail PHP/nginx logs
docker compose logs -f php-X.Y
docker compose logs -f nginx
```

## Running PHP/Composer/Yarn commands

**Always inside the relevant PHP container**, not on the host — and for dependency installs, that's the **lowest-supported-PHP container** (`os_dev_phpXX`; see "Which PHP version to use" and the `dependencies` skill). Example:

```bash
docker exec -it os_dev_phpXX bash
# now inside container, in /var/www
cd <ohrm-checkout-dir>
composer install -d src
composer install -d devTools/core
cd src/client && yarn install && yarn dev
```

The `i:`/`instance:` console commands (`php devTools/core/console.php instance:reinstall`, `instance:reset`, `instance:create-test-db`) and `bin/console` commands also run inside the container.

## DB access from PHP code and inside containers

From PHP code (and from CLI tools running in the PHP container), the DB hostname is the **container name** of the DB service — e.g. `mariadb103`, `mysql57` — **not** `127.0.0.1`. Default port 3306, default root password `root` (configurable via `MYSQL_ROOT_PW` in `.env`).

From the host, use **phpMyAdmin at http://localhost:9092** (login `root` / `root`, select target DB container from dropdown). Direct host-to-container DB connections work too if the DB service exposes a port in the compose file.

## Accessing the app in the browser

If the OHRM checkout is at `html/orangehrm-5x/`, then `http://phpXX/orangehrm-5x/` hits its `index.php` (substitute your PHP hostname). First visit redirects to `/installer/` if not yet installed, otherwise to `/web/index.php`.

The stack also serves the same app over **HTTPS on port 443** — `https://phpXX/orangehrm-5x/`. The dev certificate is self-signed, so browsers show a one-time warning that's safe to accept for local dev.

The dev-env can serve **the same OHRM checkout under multiple PHP versions simultaneously** — e.g. `http://php74/orangehrm-5x/` vs `http://php83/orangehrm-5x/` — useful for verifying PHP-version compatibility.

## Host ports 80 / 443 and conflicts

Nginx publishes host ports **80 and 443**. If another process already binds either — a host web server, another Docker stack, or a previous dev-env still running — `docker compose up` for nginx fails with "port is already allocated" / "bind: address already in use".

**Check before you start**, so the developer doesn't hit a confusing failure mid-setup:

```bash
# What (if anything) holds 80 / 443 on the host?
lsof -nP -iTCP:80  -sTCP:LISTEN          # macOS / Linux
lsof -nP -iTCP:443 -sTCP:LISTEN
# or: ss -ltnp 'sport = :80'              # Linux
# or: docker ps --filter publish=80 --filter publish=443
```

If a port is free, proceed. If it's taken, there are two ways forward — **never stop another container or process yourself; that's the developer's call:**

1. **Free the port (developer decides).** If the holder is something they're willing to stop, *ask them* to stop it, then retry. Do not run `docker stop` / `kill` on anything you didn't start.
2. **Remap the host port in `.env`.** Point the dev-env's HTTP/HTTPS host-port settings at free ports instead. Suggest ports you've **confirmed free** with the checks above (e.g. `8080` / `8443`), set them in the companion repo's `.env` (the exact keys live in its `.env.dist` — read them, don't guess), then `docker compose down && docker compose up -d …`. The app is then at `http://phpXX:8080/<subpath>/` (and `https://phpXX:8443/<subpath>/`). This edits the companion repo's `.env`, never the OrangeHRM tree.

## Switching the OHRM checkout to a different PHP/DB combo

Just stop the current stack and bring up a different one — no rebuild needed:

```bash
docker compose down
docker compose up -d phpmyadmin php-X.Y mariadbNNN   # example: any other PHP/DB combo from "Available services"
```

The codebase is mounted (not baked into the image), so source changes show up live without restarting containers.

## Troubleshooting a broken dev environment

The dev-env internals (`docker-compose*.yml`, `.env`, image definitions) live in the **companion repo, not this checkout**, so diagnose from **live state** — never from what this document claims should be true. Localize the failure to one layer first, then act.

Two rules that always hold:

- **Read real output before concluding.** `docker ps -a`, `docker compose logs -f <service>`, and shelling into the container are the ground truth. If the host's Docker state or the companion repo isn't visible from where you're running, say so and ask for the relevant command's output instead of guessing a cause.
- **Never fix a dev-env failure by editing the OrangeHRM tree.** Fixes to compose files, `.env`, images, or extensions belong in the companion repo locally (same rule as config gaps below).

Localize by symptom (all `docker compose` commands run from the `orangehrm-os-dev-environment` checkout root):

| Symptom | Likely layer | First check |
|---|---|---|
| `docker compose` errors immediately | Docker daemon down, or malformed `.env` | `docker info`; `docker compose config` (validates `.env` + yml) |
| Image build fails | Image definition; legacy services need the second compose file | re-run `docker compose build <service>` (add `-f docker-compose-legacy-services.yml` for PHP ≤ 7.3) and read the error |
| Container exits or keeps restarting | Service config / startup | `docker ps -a`; `docker compose logs -f <service>` |
| App unreachable at `http://phpXX/` | missing `/etc/hosts` entry, or nginx not up | confirm the `/etc/hosts` line; `docker compose logs -f nginx` |
| `up` fails: "port is already allocated" / "address already in use" | host port 80 or 443 already taken | `lsof -nP -iTCP:80 -sTCP:LISTEN` (and `:443`); free it (developer's call) or remap host ports in `.env` — see "Host ports 80 / 443 and conflicts" |
| 404 or wrong app path | mount / `LOCAL_SRC` misconfigured | `docker exec os_dev_phpXX ls /var/www/<checkout-dir>` |
| "database connection refused" | using `127.0.0.1` instead of the DB container name, or DB not up | confirm host is the container name (e.g. `mariadb103`); `docker ps` for the DB service |
| composer / yarn fails | running on the host, or wrong PHP version | confirm you are **inside** the PHP container, not the host |
| install / test-db errors | app layer, not dev-env | hand off to the `testing` skill and the installer |

When the live state contradicts what's written here, trust the live state — and see the next section.

## Keeping this reference current with the companion repo

This document mirrors `orangehrm-os-dev-environment`, which evolves independently of the OrangeHRM repo: service names, the PHP/DB matrix, compose file names, `.env` keys, and ports can change there with no corresponding change here. Treat the companion repo — its `README`, `docker-compose*.yml`, and `.env.dist` — as the source of truth, the same way version facts are read from their owning files rather than hardcoded.

When troubleshooting (or any time you consult the companion repo) you notice it has diverged from what's written here — a renamed or added/removed service, a changed PHP or DB version, a different port, a new or renamed `.env` key — **point out the specific divergence and recommend updating this document** so the next developer isn't misled by stale guidance. Don't silently work around the drift.

## When the dev-env itself doesn't fit

The companion repo is `git clone`-able and editable. If a developer needs a config that isn't there (a newer PHP version, a custom extension, a non-standard MySQL setting), edits go into the dev-env repo locally, not the OrangeHRM repo. Don't pollute the OHRM tree with dev-env workarounds.
