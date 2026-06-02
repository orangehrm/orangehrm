---
name: dev-environment
description: Reference for the OrangeHRM Docker-based local development environment — container layout, hostnames, common docker compose commands, how to shell into PHP containers, how to access the DB, and the LOCAL_SRC mounting convention. Use whenever the user asks about running OrangeHRM locally, switching PHP versions, accessing the dev database, rebuilding containers, or anything involving the `orangehrm-os-dev-environment` repo.
---

# OrangeHRM Docker dev environment

Local dev runs through a **separate companion repo**, [`orangehrm-os-dev-environment`](https://github.com/orangehrm/orangehrm-os-dev-environment), not the OrangeHRM repo itself. Developers do **not** install PHP / MySQL / Node directly on the host. Everything runs in containers. The host only needs Docker, Git, and a text editor (PHPStorm is the recommended IDE).

## Repo layout convention

The team standard is:

```
~/Documents/
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

## /etc/hosts entry (required)

```
127.0.0.1   php56 php70 php71 php72 php73 php74 php80 php81 php82 php83
```

Each PHP container is reachable via its short name (e.g. `http://php83/`). Nginx (port 80 on host) routes based on hostname.

## Container naming

Pattern: `os_dev_<service>`. E.g. `os_dev_php83`, `os_dev_mariadb103`, `os_dev_phpmyadmin`, `os_dev_nginx`.

## Available services

**PHP** (current compose file): `php-7.4`, `php-8.0`, `php-8.1`, `php-8.2`, `php-8.3`
**PHP legacy** (via `-f docker-compose-legacy-services.yml`): `php-5.6`, `php-7.0`, `php-7.1`, `php-7.2`, `php-7.3`
**MySQL**: `mysql55`, `mysql56`, `mysql57`, `mysql80`, `mysql81`, `mysql82`
**MariaDB**: `mariadb55`, `mariadb100`–`mariadb103`, `mariadb106`–`mariadb109`, `mariadb1010`, `mariadb1011`, `mariadb110`, `mariadb111`, `mariadb112`
**Other**: `nginx`, `phpmyadmin`

CI matrix runs against MySQL 5.7 and MariaDB 10.3 on PHP 8.3, with secondary install validation on PHP 8.4. For development, **PHP 8.3 + MariaDB 10.3** mirrors CI most closely.

## Common commands

All `docker compose` commands must run from `~/Documents/orangehrm-os-dev-environment/`.

```bash
# Build images (only the ones you'll use)
docker compose build nginx php-8.3

# Legacy images need the second compose file
docker compose -f docker-compose-legacy-services.yml build php-5.6 php-7.0

# Start a stack (DB + chosen PHP + phpMyAdmin)
docker compose up -d phpmyadmin php-8.3 mariadb103

# See what's running
docker ps

# Stop everything
docker compose down

# Shell into a container
docker exec -it os_dev_php83 bash

# Tail PHP/nginx logs
docker compose logs -f php-8.3
docker compose logs -f nginx
```

## Running PHP/Composer/Yarn commands

**Always inside the relevant PHP container**, not on the host. Example:

```bash
docker exec -it os_dev_php83 bash
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

If the OHRM checkout is at `html/orangehrm-5x/`, then `http://php83/orangehrm-5x/` hits its `index.php`. First visit redirects to `/installer/` if not yet installed, otherwise to `/web/index.php/auth/login`.

The dev-env can serve **the same OHRM checkout under multiple PHP versions simultaneously** — e.g. `http://php74/orangehrm-5x/` vs `http://php83/orangehrm-5x/` — useful for verifying PHP-version compatibility.

## Switching the OHRM checkout to a different PHP/DB combo

Just stop the current stack and bring up a different one — no rebuild needed:

```bash
docker compose down
docker compose up -d phpmyadmin php-8.2 mariadb107
```

The codebase is mounted (not baked into the image), so source changes show up live without restarting containers.

## When the dev-env itself doesn't fit

The companion repo is `git clone`-able and editable. If a developer needs a config that isn't there (a newer PHP version, a custom extension, a non-standard MySQL setting), edits go into the dev-env repo locally, not the OrangeHRM repo. Don't pollute the OHRM tree with dev-env workarounds.
