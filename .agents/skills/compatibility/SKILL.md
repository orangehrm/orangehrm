---
name: compatibility
description: Reference for OrangeHRM supported-version alignment across PHP, Composer, installer system requirements, MySQL/MariaDB, webservers, browser/frontend targets, Node/Yarn, Docker dev-environment services, and GitHub Actions matrices. Use whenever the user asks about supported versions, changes PHP/database/browser/Node/webserver support, edits composer constraints, installer/config/system_requirements.php, package-manager metadata, CI workflow matrices, Docker dev-env versions, or release/runtime compatibility.
---

# Compatibility policy

Use this skill when changing or answering questions about supported environments. Compatibility is spread across several files; the job is to inspect the current sources of truth and keep them aligned, not to copy version numbers into agent docs.

## Compatibility surfaces

| Surface | Source files to inspect | Why it matters |
|---|---|---|
| PHP dependency support | `src/composer.json`, `devTools/core/composer.json` | Composer constraints and platform settings control dependency resolution. |
| Installer runtime checks | `installer/config/system_requirements.php`, `installer/Util/SystemCheck.php`, `installer/index.php` | Web/CLI installer blocks unsupported PHP, DB, and webserver environments. |
| CI validation | `.github/workflows/*.yml` | GitHub Actions define the tested PHP, DB, install, upgrade, lint, build, and scheduled-test matrix. |
| Local dev availability | `dev-environment` skill and the `orangehrm-os-dev-environment` repo | Developers need containers for supported/tested PHP and DB versions. |
| Frontend/browser/Node support | `src/client/package.json`, `installer/client/package.json`, `src/test/functional/package.json`, browserlist/Babel/Vue/Cypress config if present | Frontend build targets and test runners define practical browser/Node compatibility. |
| Distribution/runtime packaging | `ecosystem` skill, Docker/cloud packaging repos, release workflows | Published packages should match supported runtime claims. |

## Source-of-truth rule

Do not hardcode current PHP, DB, Node, browser, or package-manager versions in this skill. Always inspect the owning file. If a user asks for the "current" supported version, read the files above before answering.

## When changing PHP support

- Read `src/composer.json` and `devTools/core/composer.json`.
- Read `installer/config/system_requirements.php`.
- Inspect GitHub Actions workflows for PHP matrix entries and install/upgrade validation.
- Check whether the Docker dev environment provides the needed PHP container.
- Use the `dependencies` skill for Composer lockfile updates; resolve Composer dependencies on the lowest supported PHP version for the relevant Composer project.
- Validate installer behavior for both web and CLI paths if installer requirements changed.

## When changing database support

- Read `installer/config/system_requirements.php`.
- Inspect CI workflow database matrices.
- Check Docker dev-environment database services.
- Review migrations and raw SQL for platform-specific assumptions; see `migrations`.
- Validate fresh install, upgrade, and relevant PHPUnit coverage against the affected DB engines.

## When changing browser or Node support

- Read each relevant `package.json` for `packageManager`, `engines` (if present), scripts, and frontend dependencies.
- Look for browser-target files or fields such as `.browserslistrc`, `browserslist`, Babel config, Vue CLI config, and Cypress config.
- Inspect CI workflows for frontend build, Jest, Cypress, or browser-specific jobs.
- Check whether the Docker dev environment supplies the required Node/Yarn tooling.
- Use `dependencies` for package-manager changes and lockfile handling.

## When changing webserver or packaging support

- Read `installer/config/system_requirements.php` for installer-visible webserver requirements.
- Inspect Docker/cloud packaging sources from the `ecosystem` skill if the support claim affects distributed images or AMIs.
- Update public docs or release notes only after verifying the runtime checks, CI, and packaging story line up.

## Review checklist

- [ ] Did you read the owning source files instead of relying on memory or agent docs?
- [ ] Do Composer constraints, installer checks, CI matrices, and dev-env availability agree?
- [ ] If frontend support changed, do package metadata, build config, and CI agree?
- [ ] If support claims changed, are user-facing docs, release notes, or packaging docs affected?
- [ ] Are examples clearly labeled as examples rather than current truth?
