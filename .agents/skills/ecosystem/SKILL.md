---
name: ecosystem
description: Reference for the wider OrangeHRM Starter ecosystem — upstream/source repositories, mobile app repositories and store listings, DockerHub image, cloud package and AWS Marketplace distribution, SourceForge and GitHub releases, Starter API docs, end-user help, demo site, and product page. Use whenever the user asks about external OrangeHRM Starter resources, release/download channels, public docs, mobile app links, cloud packages, demo access, or where an agent should look for project-adjacent context outside this repository.
---

# OrangeHRM Starter ecosystem references

Use this skill when a task needs project-adjacent context outside the current OrangeHRM Starter source tree. These links are stable entry points, but the details behind them can change; for versions, release dates, package contents, marketplace status, pricing, ratings, or current screenshots, open the live page and verify.

## Source repositories

| Resource | Link | Notes |
|---|---|---|
| Main OrangeHRM Starter repo | https://github.com/orangehrm/orangehrm | Primary open-source application repository. |
| Mobile app repo | https://github.com/orangehrm/orangehrm-os-mobile | OrangeHRM open-source mobile application source. |
| Development environment repo | https://github.com/orangehrm/orangehrm-os-dev-environment | Docker-based local development environment used with this repo. |
| Cloud packages repo | https://github.com/orangehrm/orangehrm-os-cloud-packages | OrangeHRM Starter cloud packaging for AWS and Docker distribution. |

## Documentation and help

| Resource | Link | Notes |
|---|---|---|
| Starter API docs | https://api-starter.orangehrm.com | Public API documentation for OrangeHRM Starter. |
| End-user help articles | https://starterhelp.orangehrm.com | User-facing help center articles for Starter. |

## Releases and distribution

| Resource | Link | Notes |
|---|---|---|
| SourceForge releases | https://sourceforge.net/projects/orangehrm | Historical and current release downloads from the beginning of the project. |
| GitHub releases | https://github.com/orangehrm/orangehrm/releases | GitHub release page for the main repository. |
| DockerHub image | https://hub.docker.com/r/orangehrm/orangehrm | Published Docker image for OrangeHRM Starter. |
| AWS Marketplace free AMI | https://aws.amazon.com/marketplace/pp/prodview-umpqfltctklee | AWS Marketplace listing for the free OrangeHRM Starter AMI. |

## Product, demo, and mobile listings

| Resource | Link | Notes |
|---|---|---|
| OrangeHRM Starter product page | https://orangehrm.com/orangehrm-starter-open-source-software | Public website page for the open-source Starter edition. |
| OrangeHRM Starter demo | https://opensource-demo.orangehrmlive.com/ | Public demo instance for exploring the product. |
| Google Play listing | https://play.google.com/store/apps/details?id=com.orangehrm.opensource | Android store listing for the open-source mobile app. |
| App Store listing | https://apps.apple.com/us/app/orangehrm-open-source/id1527247547 | iOS store listing for the open-source mobile app. |

## Usage guidance

- Prefer this skill over scattering external links through feature-specific skills.
- Use `dev-environment` for Docker container workflows; use this skill only to find the companion repo or distribution references.
- Use `rest-openapi` when generating or fixing in-repo OpenAPI annotations; use the public Starter API docs link here when comparing against published API documentation.
- For anything described as "latest", "current", "available", "released", or "published", verify against the live page before answering or editing docs.
