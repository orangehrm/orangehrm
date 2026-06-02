# Skills catalog

25 project-level skills covering the OrangeHRM codebase end-to-end. Each `SKILL.md` has YAML frontmatter (`name`, `description`) describing when it auto-loads — agents that don't auto-load by description should consult this catalog and read the relevant skill on demand.

Skills cross-reference each other liberally; the table below groups them by area to make discovery easier than the alphabetical filesystem order.

## Environment & onboarding

| Skill | Covers |
|---|---|
| [`dev-environment`](dev-environment/SKILL.md) | The Docker-based local dev environment (companion repo `orangehrm-os-dev-environment`), container naming, PHP/MySQL/MariaDB version matrix, `LOCAL_SRC` mount, common `docker compose` commands. |
| [`testing`](testing/SKILL.md) | PHPUnit per-plugin testsuites, the test-DB lifecycle (`instance:create-test-db`), test base classes (`TestCase` / `KernelTestCase` / `EntityTestCase` / `EndpointTestCase`), YAML fixtures + `TestDataService::populate()`, Jest for frontend, Cypress for E2E. |

## Persistence layer

| Skill | Covers |
|---|---|
| [`doctrine-bootstrap`](doctrine-bootstrap/SKILL.md) | Doctrine 2.20 wiring — single EntityManager singleton, multi-path entity discovery, dev/prod cache split, proxy strategy, `enum→string` platform mapping, custom DQL functions. |
| [`entities`](entities/SKILL.md) | Defining Doctrine entities — annotation conventions, table-name rules (`ohrm_` vs `hs_hr_`), columns, IDs, all relation types (M2O/O2M/M2M/O2O with `mappedBy`/`inversedBy`/`cascade`), `@ORM\EntityListeners` lifecycle, the project's Decorator pattern, NestedSet trees. |
| [`daos`](daos/SKILL.md) | DAOs extending `BaseDao` + `EntityManagerHelperTrait`, QueryBuilder patterns (joins by relation name, expression builder, conditional joins), `Paginator` for correct counts on joined queries, `QueryBuilderWrapper`, `FilterParams` binding, transactions. |
| [`migrations`](migrations/SKILL.md) | The `installer/Migration/V{x}/Migration.php` pattern, installer vs upgrader, `MIGRATIONS_MAP`, `SchemaHelper` / `LangStringHelper` / `ConfigHelper` / `DataGroupHelper`. **All 5.x migrations use Doctrine DBAL — raw SQL is legacy V3.3.3 only.** |

## REST API

| Skill | Covers |
|---|---|
| [`rest-endpoints`](rest-endpoints/SKILL.md) | Entry point for adding/editing REST endpoints. Request lifecycle, picking `Resource` / `Collection` / `Crud` interface, `routes.yaml`, `RequestParams`, `FilterParams`, exception classes + status code mapping, in-handler access enforcement. |
| [`rest-validation`](rest-validation/SKILL.md) | The `getValidationRuleFor*()` methods — `ParamRule`, `ParamRuleCollection`, `Rule`, the full `Rules::*` catalog (OHRM custom rules + Respect rules), `ValidationDecorator`, composites, writing custom rule classes. |
| [`rest-serialization`](rest-serialization/SKILL.md) | Response shaping — `Normalizable` + `ModelTrait`, the `filter` / `attributeNames` arrays (with nested-getter chains), `EndpointResourceResult` vs `EndpointCollectionResult`, `ParameterBag` meta, generic models, the `?model=default|detailed` `MODEL_MAP` pattern. |
| [`rest-openapi`](rest-openapi/SKILL.md) | OpenAPI v3 annotations via swagger-php — CI-enforced via `generate-open-api-doc --throw`. Method + model annotations, shared component refs, using class constants in annotations. |

## Authorization & auth

| Skill | Covers |
|---|---|
| [`authorization`](authorization/SKILL.md) | The role/data-group permission model — `PublicControllerInterface` for opt-out routes, three subscriber gates (Auth + Screen + API), `permission/api.yaml` + `permission/screens.yaml` seeding, dynamic role computation (Supervisor / HiringManager / etc.). |
| [`auth-providers`](auth-providers/SKILL.md) | `AuthProviderChain`, `LocalAuthProvider` (bcrypt), `LDAPAuthProvider` (with JIT sync via `LDAPService` / `LDAPSyncService`), the OAuth2 server (`league/oauth2-server`), OpenID Connect SSO. |
| [`security-primitives`](security-primitives/SKILL.md) | `Cryptographer` (AES-256-GCM with legacy AES-128-ECB fallback for backward compat), `KeyHandler` (file-based crypto key), `EncryptionHelperTrait` for EntityListener encryption, `PasswordHash` wrapping bcrypt, CSRF via Symfony. |

## Frontend (Vue 3)

| Skill | Covers |
|---|---|
| [`frontend-pages`](frontend-pages/SKILL.md) | Per-page-mini-SPA architecture (no vue-router), four-step page registration, plugin frontend layout, import aliases (`@/`, `@ohrm/core`, `@ohrm/components`), OXD design system catalog, custom `@ohrm/components` layer, navigation via `navigate()`. |
| [`frontend-data`](frontend-data/SKILL.md) | `APIService` axios wrapper mirroring `/api/v2/...` routes, 401/422 interceptors + ETag caching, data composables (`usePaginate`, `useSort`, `useInfiniteScroll`), form composables (`useForm`, `useServerValidation`), client validation rules catalog. |
| [`frontend-platform`](frontend-platform/SKILL.md) | Vue app plugins — i18n (`$t` + module-grouped keys), ACL (`$can` matching backend data groups), toaster (semantic shortcuts), loader, navigation helpers, `useDateFormat`. |

## Backend architecture

| Skill | Covers |
|---|---|
| [`services`](services/SKILL.md) | Service layer between Endpoints and DAOs. `*ServiceTrait` DI access, lazy-getter composition, plugin-level registration in `PluginConfigurationInterface::initialize()`, what belongs in service vs DAO vs Decorator. |
| [`events`](events/SKILL.md) | EventDispatcher, `AbstractEventSubscriber`, `<Plugin>Events` constant-holder convention, plugin-level `addSubscriber()` registration, priorities, propagation. Symfony `KernelEvents` + OHRM domain events. |
| [`console-commands`](console-commands/SKILL.md) | The two Symfony Console entry points — `bin/console` (production) vs `devTools/core/console.php` (dev-only). `OrangeHRM\Framework\Console\Command` base, `ConsoleConfigurationInterface::registerCommands()`, SymfonyStyle IO. |
| [`config`](config/SKILL.md) | The `hs_hr_config` key/value table, `ConfigService` with typed `KEY_*` accessors, `ConfigServiceTrait`, `ConfigHelper` for migrations, naming convention, when to use config vs an entity-backed setting. |
| [`helpers`](helpers/SKILL.md) | Catalog of framework-wide traits and helper services — `DateTimeHelperTrait`, `TextHelperTrait`, `NumberHelperTrait`, `LoggerTrait`, `CacheTrait`, `EntityManagerHelperTrait`, `EventDispatcherTrait`, `AuthUserTrait`, `UserRoleManagerTrait`, plus the `Helper/` and `Utility/` classes. |

## Cross-cutting features

| Skill | Covers |
|---|---|
| [`mail`](mail/SKILL.md) | `EmailService` queue-and-defer pattern (drained on `KernelEvents::TERMINATE`), `EmailConfiguration` entity with encrypted `smtp_password`, per-plugin `Mail/templates/<locale>/<event>/` Twig templates, `queueEmailNotifications()` for event-driven flows. |
| [`scheduled-jobs`](scheduled-jobs/SKILL.md) | The `orangehrm:run-schedule` runner (one host cron entry every minute), `SchedulerConfigurationInterface::schedule()` plugin hook, `Schedule`/`Task`/`CommandInfo` (Task extends `Crunz\Event`), UTC-by-default evaluation, `ohrm_task_scheduler_log` audit. |
| [`workflow`](workflow/SKILL.md) | `WorkflowStateMachine` `(workflow, state, role, action) → resultingState` transitions, the eight `FLOW_*` constants (Leave/Recruitment/Timesheet/Attendance/Employee/Review/Self-Review/Claim), `AccessFlowStateMachineService`, the two-layer authorization model. |
| [`menus`](menus/SKILL.md) | The `MenuItem` entity, side panel + top menu rendering (server-computed in `MenuService::getMenuItems()` → Twig → `<oxd-layout>`), `MenuConfigurator` interface for runtime active-menu customization, permission gating, seeding via migrations. |

---

## Cross-reference graph (high-level)

The skills cross-link liberally. Some of the most-traveled paths:

- **Adding a new screen end-to-end**: `frontend-pages` ↔ `rest-endpoints` ↔ `authorization` ↔ `migrations` ↔ `menus`
- **Adding a new feature with persistence**: `entities` ↔ `migrations` ↔ `daos` ↔ `services` ↔ `rest-endpoints`
- **Adding an event-triggered notification**: `events` ↔ `mail` ↔ `services`
- **Encrypting a new sensitive field**: `security-primitives` ↔ `entities` (EntityListener) ↔ `migrations` (column sizing)
- **Setting up a fresh checkout**: `dev-environment` ↔ the project's `/ohrm-onboard` slash command (Claude Code only) ↔ `testing` (`instance:create-test-db`)

---

## Convention for skill files

Each `<name>/SKILL.md` is structured:

1. YAML frontmatter — `name` (the skill ID) + `description` (the trigger blurb — what tasks should load this skill)
2. Substantive sections covering the topic, with code examples
3. **Recipes** — concrete copy-and-adapt patterns for common tasks
4. **Checklists** — what to verify when doing common tasks
5. **Things that bite** — the gotchas worth flagging

When editing a skill, keep this shape. When adding a new skill, mirror an existing one of similar size.

The source of truth for these files is `.agents/skills/<name>/SKILL.md`. Don't edit them under `.claude/skills/` (which may be a symlink or a stale copy — see `.agents/SETUP.md`).
