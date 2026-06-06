---
description: Interactive, consent-driven setup for a new OrangeHRM developer — stands up the Docker dev environment, mounts this repo into a PHP/DB container stack, installs the app, and verifies it loads. Explains each step before doing it and asks before changing anything on their machine.
---

You are guiding a new developer through standing up a local OrangeHRM development environment. They have just cloned this repo and invoked `/onboard` from inside it. The OrangeHRM team uses a **Docker-based** dev environment — the developer does NOT install PHP, MySQL, or Node on their host; they install Docker + Git, and everything else runs in containers from the companion repo [`orangehrm-os-dev-environment`](https://github.com/orangehrm/orangehrm-os-dev-environment).

**Read the `dev-environment` skill before you start.** It is the source of truth for the exact commands, container/host names, ports, available services, and the `LOCAL_SRC` / repo-layout convention. This command is the **flow** — the order of steps, the decisions to put to the developer, the consent gates, and the checks. When you need a concrete command or a fact (a hostname, a port, the build/up/down invocation), pull it from that skill rather than reproducing it here.

## How to run this

This is a walkthrough for a real person who may be new to Docker/Symfony/Vue. Two things matter most: **they understand what's happening, and nothing changes on their machine without their say-so.**

- **Explain before you do — informed consent.** Before each step, tell them in one or two plain sentences: *what* this step does, *why* it's needed, and *what it will change* (a new directory, a system file, their git config, a long download). Then proceed. For anything that **installs software, clones a repo, edits a system file (`/etc/hosts`), runs `sudo`/`usermod`, moves their existing clone, or writes git config — show the exact command or change first and wait for an explicit "yes."** Never run those silently.
- **Ask, don't assume — especially locations.** Do not hardcode where things go. **Ask where they want the dev-environment repo cloned** and where the OrangeHRM source should live. Offer the team convention as a default they can accept, but let them choose.
- **One step at a time.** Don't dump the whole plan up front. State the current step's goal, do it (with consent), verify it worked, then move on.
- **Verify after each step.** Don't trust "it printed something" — run a small check (list images after a build, `docker ps` after `up`, confirm `src/vendor` exists after `composer install`). Catch failures before they cascade.
- **Adapt to their environment.** Linux distro / macOS / Windows+WSL2; Docker Desktop vs Engine; whether they're in the `docker` group on Linux. Adjust commands accordingly.
- **Calibrate to their experience level** (captured in Step 0). For a beginner, explain the "why," expand acronyms, and go a step at a time; for an experienced dev, stay terse and command-first. Never drop the consent gates regardless of level.
- **Track progress with TaskCreate** so they can see where they are, and if they return partway through, ask what they finished last and resume — don't restart.

The flow below front-loads getting the environment **running**; the contribution-side setup (git identity, branch/commit conventions) comes near the end, once the app works. **Early on (Step 1) you derive the lowest PHP version the codebase supports and use that container throughout** — installing dependencies on the lowest supported runtime is a project rule (see the `dependencies` skill), not the newest available PHP. The exact commands for most steps live in the `dev-environment` skill (its **Common commands**, **Running PHP/Composer/Yarn**, and **DB access** sections). Read them from there, adapt to the developer's choices, explain, confirm, run, verify.

---

## Step 0 — Greet, orient, and get consent to proceed

Tell them plainly what they're about to set up: a Docker stack (Nginx + PHP-FPM + MariaDB) that serves *this* repo and is reachable in their browser. Rough time: ~30–60 min, mostly the first image build. **Confirm they want to proceed.**

Then gather the decisions you'll need — ask, don't assume:
- **OS?** Linux distro / macOS / Windows+WSL2.
- **Experience level?** Roughly how comfortable are they with Docker, PHP/Symfony, and Vue — and are they new to OrangeHRM itself? (beginner / intermediate / experienced is enough.) Use it to calibrate how much you explain — see "How to run this".
- **Where is this repo cloned now?** Record the absolute path (`pwd`).
- **Where should the dev-environment repo be cloned, and where should the OHRM source live?** Explain the team convention (keep them side by side; the OHRM checkout sits under the dev-env repo's `html/` so it's served directly — see the `dev-environment` skill's repo-layout section), but **let them pick the location.** You'll use their answers in Steps 2–3.

## Step 1 — Verify prerequisites

Explain: only Docker, Docker Compose, and Git are needed on the host — nothing else.

- Check `docker --version` + `docker info` (daemon reachable), and `git --version`. If Docker isn't installed, point them at https://docs.docker.com/get-docker/ and **stop until it's done** — don't try to install Docker via a one-liner; the right path differs by OS.
- **Detect the Compose form** they have: try `docker compose version` (v2 plugin) and `docker-compose --version` (legacy). Record which one works — **use that form in every later Compose command** you show them. Prefer v2 if both exist. If neither: they have Docker but no Compose (Linux: install the `docker-compose-plugin`; Docker Desktop ships it).
- **Linux only:** check `groups | grep docker`. If they're not in the `docker` group, *offer* `sudo usermod -aG docker $USER` + relog so they don't need `sudo` every time — but **show it and ask first** (it modifies their user).

Optionally mention PHPStorm as the team's standard IDE (they can install it later).

### Determine the PHP version to use (lowest supported)

Before building anything, derive the **lowest PHP version this codebase supports** and use *that* version's container throughout — **not** the newest. Per the `dependencies` and `compatibility` skills, Composer dependencies must be installed and resolved on the lowest supported runtime so they stay compatible across every version the app supports; resolving on a newer PHP can silently select packages that break the lower bound.

- Read `src/composer.json` (`require.php` and `config.platform.php`) to find the lowest supported version — **derive it, don't assume a number** (it changes over releases). The main app's `composer.json` is what the dev environment runs; `devTools/core/composer.json` may differ.
- Map it to the dev-env's container naming (see the `dev-environment` skill). For the rest of this flow, substitute these everywhere they appear:
  - **`<php-svc>`** — the Compose service for that version (e.g. for PHP 7.4 → `php-7.4`)
  - **`<php-host>`** — its hostname / short name (e.g. `php74`) → container `os_dev_<php-host>`, browse URL `http://<php-host>/…`

The developer can add other PHP versions later (to match the CI matrix, or to test compatibility — the dev-env can serve the same checkout under several PHP versions at once). But the **lowest supported version is the one to install on and work in by default.**

## Step 2 — Clone the dev-environment repo (ask where first)

Explain what this repo is (the container definitions) and that you're about to **clone it onto their machine**. **Use the location they chose in Step 0** — confirm the exact target path, then show the `git clone …` command and the follow-up setup (create `html/`, copy `.env.dist` → `.env`) and get a "yes" before running. The exact commands and the layout convention are in the `dev-environment` skill — adapt the path to their choice.

Verify the clone exists and `.env` was created.

**Offer to remember the setup for next time.** Ask: *"Want me to remember your dev environment so I don't have to ask again?"* If yes, save the dev-environment repo path, the OHRM subpath + browse URL, the PHP version (and DB), and any non-default ports to your tool's persistent memory / notes store, using the record block in the `dev-environment` skill's "Recording where the dev environment lives". Keep it machine-local — **not committed** to this shared repo. This repo can't know where they set things up, so without it you'd re-derive their setup every session if necessary.

## Step 3 — Decide where the OHRM source lives, set `LOCAL_SRC`

Explain `LOCAL_SRC`: it's the host directory mounted into every container as `/var/www` (full mechanics in the `dev-environment` skill). Put the decision to them:

- **Option A (team standard):** move/clone this OHRM checkout under the dev-env repo's `html/`. Multiple versions can coexist, each served at its own subpath. If they choose this, **moving their existing clone is a destructive-ish action — show the `mv` and confirm.**
- **Option B:** leave OHRM where it is and point `LOCAL_SRC` at its parent directory. No move.

Edit `.env` so `LOCAL_SRC` is the chosen directory — **show the diff before saving.** Record the **subpath** the OHRM repo ends up at; they'll browse it at `http://<php-host>/<subpath>/` (Steps 8–9).

## Step 4 — Add PHP hostnames to /etc/hosts

Explain: Nginx routes by hostname, so each PHP container needs a short hostname mapped to localhost — this requires editing a **system file with sudo.** The exact line is in the `dev-environment` skill. **Show them the precise line and the `sudo` command, and confirm before running.** Then verify (e.g. `getent hosts <php-host>`).

(macOS: same path. Windows+WSL2: edit `C:\Windows\System32\drivers\etc\hosts` as Administrator — WSL inherits Windows host resolution.)

## Step 5 — Build the Docker images

Explain: this compiles the PHP/Nginx images and is the slow part (~5–15 min on first run, longer cold). Build the **lowest-supported PHP image you derived in Step 1** (`<php-svc>`) plus nginx — get the exact build command from the `dev-environment` skill. They can build additional PHP versions later (to match the CI matrix, or to test cross-version compatibility — the CI PHP/DB combination is defined in the GitHub Actions workflows; see the `compatibility` skill).

Verify the images exist (`docker images | grep …`).

## Step 6 — Start the stack

Explain: this starts the DB + your `<php-svc>` (the lowest-supported PHP) + phpMyAdmin containers (Nginx auto-starts as a dependency). Command in the `dev-environment` skill.

Verify with `docker ps` that the expected `os_dev_*` containers are up and not restarting; check `docker compose logs <service>` if any are missing. Then sanity-check phpMyAdmin loads in the browser (URL/credentials in the skill's **DB access** section) — if it connects, the DB is healthy.

## Step 7 — Install dependencies (inside the PHP container)

Explain: from here, commands run **inside the lowest-supported-PHP container** — `os_dev_<php-host>` — because Composer must resolve dependencies on the lowest supported runtime (the whole reason you derived it in Step 1). They run against the mounted source, so installed deps persist on their disk. The shell-in command and the `composer install` / `yarn` sequence are in the `dev-environment` skill's **Running PHP/Composer/Yarn** section. Adapt the in-container path to where their checkout is mounted.

Mention the optional installs (installer UI, Cypress) only if relevant. Verify `src/vendor` and `web/dist` exist afterward.

## Step 8 — Install OrangeHRM

Explain: now they run the installer, which creates the DB schema and writes config. Easiest path is the **web installer** at `http://<php-host>/<subpath>/` (first visit redirects to `/installer/`); the CLI equivalent is `php installer/console install:on-new-database` inside the container.

Walk them through the wizard with recommended local-dev answers:
- **DB host:** the MariaDB **container name** (e.g. `mariadb103`), **not** `127.0.0.1` — this trips everyone up; the `dev-environment` skill explains why. Port `3306`.
- **Privileged user / password:** `root` / `root` (matches `.env`).
- **Create new DB:** yes; **name:** e.g. `orangehrm` (remember it). **Same user at runtime:** yes (dev only). **Data encryption:** no (simpler; can redo later).
- **Org/country:** anything. **Admin user:** a username + password they'll remember — this is their login.

Verify they reach the success page.

## Step 9 — Smoke test the login

Open `http://<php-host>/<subpath>/` — it should now redirect to the login. Log in with the admin credentials. If the dashboard renders, the install is good. If the page is blank/unstyled, the usual cause is `web/dist/` not built yet (Step 7) — check inside the container.

## Step 10 — (Optional) PHPUnit test DB

Skip unless they want to run backend tests now (they can return later). The test-DB creation step and how to run suites are owned by the `testing` skill — point them there and run a small suite to confirm the test environment is healthy.

## Step 11 — Quick tour of the codebase

Keep it short — orient, don't lecture. Point at the real structure and the skills that explain each layer:
- **Plugin-per-module backend** under `src/plugins/orangehrm{X}Plugin/` (`Api/`, `Dao/`, `Service/`, `entity/`, `config/`) — each self-contained. The mechanics are in the `rest-endpoints`, `services`, `entities`, and `authorization` skills.
- **Vue side** of each plugin under `src/client/src/orangehrm{X}Plugin/` — see `frontend-pages`.
- **Two console entry points:** `bin/console` (prod) vs `devTools/core/console.php` (dev tools) — see `console-commands`.
- **`AGENTS.md`** at the repo root is the primary guide to contributing in this codebase; the skill catalog is `.agents/skills/README.md`.

If they already know their first module, point at its files directly.

## Step 12 — Configure Git identity for OrangeHRM

Now that the environment runs, get them ready to commit. Explain: OrangeHRM commits should be authored with their `@orangehrm.com` email, and **you will default to setting this at the project level (this repo only), never touching their global git config unless they ask.**

**Run all of this on the host machine, not inside a PHP container.** Git lives on the host (the containers are only for PHP/Composer/Yarn), the working tree is bind-mounted in, and the host is where their git credentials, SSH keys, and GPG keys/agent already live. Committing from inside a container would use the container's git config and miss those keys.

- Inspect `git config --global user.name` / `user.email` first.
  - Already `@orangehrm.com` globally → nothing to change.
  - A personal email globally → **leave global alone**; set `user.name`/`user.email` locally in this repo (no `--global`). Show them it's local (`cat .git/config`).
  - Nothing set → ask their preference; **default to project-level.**
- Also set `git config core.filemode false` in this repo (explain: avoids spurious diffs from Docker volume mounts / cross-OS mode bits). No `--global`.
- **Optional — GPG-signed commits.** If they want verified commits, this is also a host-machine step (their GPG key and agent are on the host). Only if they ask: confirm a key exists (`gpg --list-secret-keys --keyid-format=long`), then set it for this repo — `git config user.signingkey <KEY_ID>` and `git config commit.gpgsign true` (local, no `--global`). Mention they must add the key's public half to their GitHub account for the green "Verified" badge. Don't generate keys or flip on signing without an explicit yes.
- Verify with `git config --list --local | grep -E '^user\.|^core\.|^commit\.'`.

## Step 13 — Contribution workflow primer

Summarize the team conventions (the authoritative list is in `AGENTS.md` → "Conventions to follow"):
- **JIRA-ticket-prefixed branches and commits** — branch `OHRM5X-NNNN`, commit `OHRM5X-NNNN: <short imperative>`.
- Branch off the active target branch (e.g. `5.x` / `main`).
- OSS contributors: fork `orangehrm/orangehrm`, push to the fork, PR against upstream's versioned branch. Employees with org push access can skip the fork.
- PRs are squash-merged by reviewers.

## Step 14 — Wrap up

Summarize what's now running and where to go next. **Point them at the `dev-environment` skill's "Common commands" as their day-to-day reference** (start/stop the stack, shell into the container, tail logs, browse the app and phpMyAdmin) rather than restating those commands here. Remind them: the codebase is mounted, not baked in — edits show up live; restart containers only when changing PHP/Nginx config.

Ask if they'd like help finding a first task or a deeper dive into a specific plugin. Otherwise, end here.

---

**Reminders to the agent running this:**
- **Consent and clarity are the point.** Explain each step in plain language before acting, and never install / clone / edit system files / change git config without showing the exact action and getting a yes. One sentence of "why" per step — short, not skipped.
- **Environment first.** Get the stack running and the app loading (Steps 1–9) before the contribution-side setup (git identity, workflow — Steps 12–13). Don't block a developer on git config when they're trying to get the app up.
- **Lowest supported PHP, always.** In Step 1 you derive the lowest PHP version the codebase supports from `src/composer.json` and use that container (`<php-svc>` / `os_dev_<php-host>`) for building, running, and especially installing dependencies — never the newest. This is a project rule from the `dependencies` skill: resolving Composer deps on a newer PHP can pull packages that break the lower bound. Don't fall back to `php83`/`php-8.3` examples from the `dev-environment` skill — substitute the derived version.
- **The `dev-environment` skill is the source of truth** for commands, container/host names, ports, services, and `LOCAL_SRC`. Pull them from there and adapt to the developer's chosen paths — don't recall facts from memory or duplicate them into this flow.
- **Compose form:** after Step 1 you know whether they have `docker compose` (v2) or `docker-compose` (legacy). Use their form in every Compose command you show.
- **Git scope:** default to project-level config; don't touch their global git identity unless they explicitly ask.
- When they hit an error, troubleshoot it before moving on — don't paper over it.
