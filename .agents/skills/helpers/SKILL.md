---
name: helpers
description: Catalog of OrangeHRM's framework-wide helper traits and helper services — the trait composition pattern (services, decorators, subscribers, commands, endpoints all `use` these), the `core helper services` (`DateTimeHelperService`, `NumberHelperService`, `TextHelperService`, `NormalizerService`, `MenuService`), the framework-level traits (`ServiceContainerTrait`, `EventDispatcherTrait`, `LoggerTrait`, `CacheTrait`, `ValidatorTrait`, `ClassHelperTrait`, `ETagHelperTrait`, `ModuleScreenHelperTrait`, `ControllerTrait`, `UserRoleManagerTrait`, `AuthUserTrait`, `EntityManagerHelperTrait`/`EntityManagerTrait`), and the `Helper/` plain-class helpers (`ClassHelper`, `VueControllerHelper`, `ModuleScreenHelper`, `LocalizedDateFormatter`). Use whenever the user is about to write a util function for something common (date formatting, string operations, number formatting, current-user lookup, cache access, etc.) — to find out if there's already a helper to `use` instead. Companion to `services` (the canonical trait consumer), `daos` (which uses `EntityManagerHelperTrait`), `authorization` (uses `UserRoleManagerTrait` / `AuthUserTrait`).
---

# Helpers — traits and helper services

OrangeHRM has **a lot of helpers** scattered across the core plugin. The naming convention is consistent: most are `use`-able traits, and each trait typically wraps one helper service. **Before writing a util function**, check this catalog — the answer is usually "already exists."

This skill is a reference catalog. Each entry shows what to `use`, what method(s) it exposes, and the common case. For deeper documentation on specific helpers, see the linked skill.

## How the trait pattern works (recap)

The codebase consistently uses traits as the DI access layer:

```php
class MyClass
{
    use DateTimeHelperTrait;        // ← gives $this->getDateTimeHelper()
    use TextHelperTrait;            // ← gives $this->getTextHelper()
    use UserRoleManagerTrait;       // ← gives $this->getUserRoleManager()

    public function doSomething(): void
    {
        $now = $this->getDateTimeHelper()->getNow();
        $clean = $this->getTextHelper()->strip(...);
    }
}
```

Traits typically delegate to the DI container or instantiate the helper lazily. Same pattern in services, decorators, subscribers, commands, endpoints, validators — everywhere. See `services` skill for the full pattern.

## Core helper services + their traits

These are the "everyday" helpers — formatters, normalizers, cross-cutting utilities. Each has a service class in `orangehrmCorePlugin/Service/` and a matching trait in `orangehrmCorePlugin/Traits/Service/`.

| Trait | Service | Exposes | Use for |
|---|---|---|---|
| `DateTimeHelperTrait` | `DateTimeHelperService` | `getNow()`, `formatDate()`, `formatDateTime()`, `getDateTimeHelper()` (returns the service), `TIMEZONE_UTC` constant | Anything date/time. **Always** use this rather than `new DateTime()` directly — it respects the user's timezone. |
| `TextHelperTrait` | `TextHelperService` | `strStartsWith()`, `strEndsWith()`, `strContains()`, `truncate()`, `stripTags()`, more string ops | Polyfills for older PHP `str_*` methods and shared text munging. |
| `NumberHelperTrait` | `NumberHelperService` | Number formatting, rounding | Currency-like display, fixed-decimal output. |
| `NormalizerServiceTrait` | `NormalizerService` | `normalize($entityOrCollection, $class)` | Convert a Doctrine entity to a Model's array output without going through `EndpointResult`. Used by services that need to emit Model-shaped data outside the REST flow. |
| `ConfigServiceTrait` | `ConfigService` | The full config getter/setter catalog | See `config` skill. |
| `MenuServiceTrait` | `MenuService` | Side panel + top menu rendering | Internal — used by the Twig layout. Rarely called from feature code. |
| `ReportGeneratorServiceTrait` | `ReportGeneratorService` | Report rendering | Internal to the reports system. |

### Plugin-specific service traits (representative)

Every plugin has its own `Traits/Service/<Name>ServiceTrait.php` for each of its services. The pattern is universal — see `services` skill. Most-frequently `use`d from outside their owning plugin:

| Trait | Use for |
|---|---|
| `UserServiceTrait` (Admin) | Current user lookups, user management |
| `EmployeeServiceTrait` (Pim) | Employee operations |
| `LeaveServiceTrait` (Leave) | Leave allocation, approval flows |
| `CompanyStructureServiceTrait` (Admin) | Subunit tree operations |
| `EmailServiceTrait` (Core, via Mail) | Sending emails — see `mail` skill |

When pulling in a cross-plugin service, you `use` *that plugin's* trait. The dependency between plugins is fine; the trait keeps consumers from instantiating the service directly.

## Framework-level traits (no separate service)

Most of these wrap framework-provided objects (event dispatcher, logger, cache, validator) rather than OHRM-specific services.

| Trait | Exposes | Use for |
|---|---|---|
| `ServiceContainerTrait` | `getContainer(): Container` | Raw DI container access — rare in feature code; mostly used by other traits internally. Avoid unless you specifically need it. |
| `EventDispatcherTrait` | `getEventDispatcher()` | Dispatch events. See `events` skill. |
| `LoggerTrait` | `getLogger()` | Monolog logger writing to `src/log/orangehrm.log`. Use for `error`, `warning`, `info`, `debug` log messages. |
| `CacheTrait` | `getCache($namespace)` | Symfony Cache adapter access. Per-namespace; common namespace is `orangehrm` for app-level caching. |
| `ValidatorTrait` | `validate($values, ParamRuleCollection)` | Programmatic invocation of the validator — same call the REST framework makes. Use for validating data outside the REST flow (e.g. in a CLI command, in a CSV importer). See `rest-validation`. |
| `ClassHelperTrait` | `getClassHelper(): ClassHelper` | Reflection + class name resolution. Used internally by the home-page enabler lookup, the menu configurator resolver, etc. |
| `ETagHelperTrait` | `generateETag($data)`, `setETag($response, $etag)` | ETag generation for HTTP caching headers — used by file controllers and the OXD logo endpoint. |

## Authentication / authorization traits

| Trait | Exposes | Use for |
|---|---|---|
| `AuthUserTrait` | `getAuthUser(): AuthUser` | Current authenticated user — session-bound. `getUserId()`, `getEmpNumber()`, `isAuthenticated()`, attribute storage. |
| `UserRoleManagerTrait` | `getUserRoleManager()` | The whole RBAC system — see `authorization` skill. `getAccessibleEntityIds()`, `getApiPermissions()`, `getScreenPermissions()`, etc. |

## Doctrine / ORM traits

Already covered in detail in `daos` skill:

| Trait | Exposes | Use for |
|---|---|---|
| `EntityManagerTrait` | `getEntityManager()` | Bare EM access. |
| `EntityManagerHelperTrait` | (extends EntityManagerTrait) `getRepository()`, `persist()`, `remove()`, `createQueryBuilder()`, `getPaginator()`, `getQueryBuilderWrapper()`, `fetchOne()`, `getReference()`, `beginTransaction()`/`commitTransaction()`/`rollBackTransaction()` | The DAO surface — but also usable in services, decorators, validator rules, listeners. |

## Controller / module traits

| Trait | Exposes | Use for |
|---|---|---|
| `ControllerTrait` | `forward($controllerSpec)` for sub-controller dispatch | Used by exception subscribers to render error pages via another controller (e.g. `ForbiddenController` from `ScreenAuthorizationSubscriber`). Rare in feature code. |
| `ModuleScreenHelperTrait` | `getCurrentModuleAndScreen()` | Resolves the active module + screen from the URL — used by `ScreenAuthorizationSubscriber`. Rare in feature code. |
| `Controller/VueComponentPermissionTrait` | Helpers for `AbstractVueController` to filter component props by permission | Inside `preRender()` to decide what data to pass to the Vue component. |

## Encryption / security traits

See the `security-primitives` skill for the full treatment. Quick references:

| Trait | Exposes | Use for |
|---|---|---|
| `EncryptionHelperTrait` (under `Utility/`) | `encryptionEnabled()`, `getCryptographer()` | Used inside `EntityListener` classes to encrypt/decrypt sensitive columns. |

## Plain `Helper/` classes (not traits)

`orangehrmCorePlugin/Helper/` has a few non-trait helpers — instantiate with `new` or accessed via a service.

| Class | Use for |
|---|---|
| `ClassHelper` | `classExists($className, $namespace = '')`, `getClass()`. Reflection-style class resolution with fallback namespace. Used by `HomePageService` and menu configurators. |
| `VueControllerHelper` | Builds the context passed to the Vue Twig template (baseUrl, user, permissions, etc.). Internal to `AbstractVueController`. |
| `ModuleScreenHelper` | Static utility for module/screen URL parsing. Used by `ModuleScreenHelperTrait`. |
| `LocalizedDateFormatter` | Locale-aware date formatting. Used inside `DateTimeHelperService`. |

## `Utility/` plain classes

`orangehrmCorePlugin/Utility/` has security and infrastructure utilities. Mostly covered in other skills:

| Class | Skill |
|---|---|
| `Cryptographer` | `security-primitives` |
| `KeyHandler` | `security-primitives` |
| `PasswordHash` | `security-primitives` |
| `Mailer`, `MailMessage`, `MailTransport` | `mail` |
| `Sanitizer` | HTML sanitization for user-generated content. Used in Buzz posts. |
| `Base64Url` | URL-safe base64 encoding (for tokens, query params). |

## "Is there a helper for…" cheat sheet

| If you need… | `use` this |
|---|---|
| Current date/time | `DateTimeHelperTrait` → `getDateTimeHelper()->getNow()` |
| Format a date for the user | `DateTimeHelperTrait` → `getDateTimeHelper()->formatDate($date)` |
| Check if a string starts with X | `TextHelperTrait` → `getTextHelper()->strStartsWith($str, $prefix)` |
| Truncate a string | `TextHelperTrait` → `getTextHelper()->truncate($str, $max)` |
| Strip HTML tags | `TextHelperTrait` → `getTextHelper()->stripTags()` |
| Format a number with currency | `NumberHelperTrait` → `getNumberHelper()->...` |
| Log an error | `LoggerTrait` → `getLogger()->error('msg', $context)` |
| Log info during a long operation | `LoggerTrait` → `getLogger()->info('progress: 50%')` |
| Get the current user | `AuthUserTrait` → `getAuthUser()->getEmpNumber()` / `getUserId()` |
| Check if user has a permission | `UserRoleManagerTrait` → `getUserRoleManager()->getApiPermissions(...)` (see `authorization`) |
| Get accessible entity IDs (e.g. employees this user can see) | `UserRoleManagerTrait` → `getUserRoleManager()->getAccessibleEntityIds(Employee::class)` |
| Read a config value | `ConfigServiceTrait` → `getConfigService()->getX()` (see `config`) |
| Dispatch an event | `EventDispatcherTrait` → `getEventDispatcher()->dispatch(...)` (see `events`) |
| Run the validator on arbitrary data | `ValidatorTrait` → `validate($values, $rules)` (see `rest-validation`) |
| Cache something | `CacheTrait` → `getCache('orangehrm')->get(key, callback)` |
| Generate an ETag for a response | `ETagHelperTrait` → `generateETag($data)`, `setETag($response, $etag)` |
| Encrypt a sensitive field on save | (in an EntityListener) `EncryptionHelperTrait` → `getCryptographer()->encrypt(...)` (see `security-primitives`) |
| Create a database query | `EntityManagerHelperTrait` → `createQueryBuilder(Entity::class, 'e')` (see `daos`) |
| Persist an entity | `EntityManagerHelperTrait` → `persist($entity)` (see `daos`) |
| Start a transaction | `EntityManagerHelperTrait` → `beginTransaction()` (see `daos`) |

If your need isn't in the table: search `src/plugins/orangehrmCorePlugin/Traits/` and `Traits/Service/` for matching trait names. The naming is predictable — `XHelperTrait`, `XServiceTrait`. **If nothing exists for the operation you need, the right answer is to add a helper service + trait, not to inline the logic in a feature.**

## When to add a new helper

A new helper is justified when:

1. **The same logic appears in two places already** (3rd time = definitely extract). One-off logic stays inline.
2. **The logic is non-trivial** — formatting, parsing, computation worth more than 5 lines.
3. **It needs DI** — accessing config, the EM, the dispatcher, etc. Goes in a service; trait wraps it.
4. **It's pure** — no I/O, no state. Goes in a static utility class or a plain helper.

### Adding a new helper service + trait

Mirror an existing example like `TextHelperService` / `TextHelperTrait`:

```php
// src/plugins/orangehrmCorePlugin/Service/SlugHelperService.php
namespace OrangeHRM\Core\Service;

class SlugHelperService
{
    public function slugify(string $input): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($input)));
    }
}
```

```php
// src/plugins/orangehrmCorePlugin/Traits/Service/SlugHelperTrait.php
namespace OrangeHRM\Core\Traits\Service;

use OrangeHRM\Core\Service\SlugHelperService;

trait SlugHelperTrait
{
    protected ?SlugHelperService $slugHelper = null;

    public function getSlugHelper(): SlugHelperService
    {
        if (!$this->slugHelper instanceof SlugHelperService) {
            $this->slugHelper = new SlugHelperService();
        }
        return $this->slugHelper;
    }
}
```

For pure helpers like this, no container registration is needed — the trait instantiates on demand. For helpers that need DI (config, EM, dispatcher), follow the `services` skill's full registration pattern.

---

# Recipes

## Recipe 1 — Service that uses several common traits

```php
namespace OrangeHRM\X\Service;

use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class WidgetService
{
    use EventDispatcherTrait;
    use LoggerTrait;
    use AuthUserTrait;
    use ConfigServiceTrait;
    use DateTimeHelperTrait;
    use TextHelperTrait;
    use UserRoleManagerTrait;

    public function buildSlug(string $name): string
    {
        $clean = $this->getTextHelper()->stripTags($name);
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', $clean));
    }

    public function logOperation(string $operation): void
    {
        $this->getLogger()->info(sprintf(
            '[%s] User %d: %s',
            $this->getDateTimeHelper()->getNow()->format('Y-m-d H:i:s'),
            $this->getAuthUser()->getUserId(),
            $operation,
        ));
    }

    public function getAccessibleWidgets(): array
    {
        if (!$this->getConfigService()->isWidgetFancyModeEnabled()) {
            return [];
        }
        return $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
    }
}
```

The bigger the service, the more traits it composes — that's fine. The trait list is the service's dependency declaration. Reading from the top quickly tells you what it depends on.

## Recipe 2 — Add a new helper

Goal: a `MoneyHelperService` for formatting currency strings consistently.

```php
namespace OrangeHRM\Core\Service;

class MoneyHelperService
{
    public function format(float $amount, string $currencyCode = 'USD'): string
    {
        return $currencyCode . ' ' . number_format($amount, 2, '.', ',');
    }
}
```

```php
namespace OrangeHRM\Core\Traits\Service;

use OrangeHRM\Core\Service\MoneyHelperService;

trait MoneyHelperTrait
{
    protected ?MoneyHelperService $moneyHelper = null;

    public function getMoneyHelper(): MoneyHelperService
    {
        return $this->moneyHelper ??= new MoneyHelperService();
    }
}
```

Now any class can `use MoneyHelperTrait` and call `$this->getMoneyHelper()->format($amount, $currency)`. No further registration needed — pure helper, no DI dependencies.

## Recipe 3 — Pick the right trait by responsibility

The mental model: **think about what you're doing, then pick the matching trait.**

- "Format a date for the UI" → date formatting → `DateTimeHelperTrait`
- "Log that this happened" → logging → `LoggerTrait`
- "Send an event so other plugins can react" → events → `EventDispatcherTrait`
- "Check if the user is allowed to do this" → permissions → `UserRoleManagerTrait` + see `authorization`
- "Encrypt this before saving" → encryption (entity listener) → `EncryptionHelperTrait` + see `security-primitives`
- "Read a feature flag" → config → `ConfigServiceTrait` + see `config`
- "Run a DQL query" → ORM → `EntityManagerHelperTrait` + see `daos`

If you find yourself doing two of these at once, you `use` two traits. The trait list grows with the class's responsibilities. That's normal — services routinely `use` 4-8 traits.

---

# Checklists

## Before writing a new helper

- [ ] Search `Traits/` and `Traits/Service/` in `orangehrmCorePlugin` for matching trait names
- [ ] Search the "Is there a helper for…" cheat sheet above
- [ ] Check other plugins' `Traits/Service/` — sometimes a plugin owns a helper that's broadly useful
- [ ] Only if nothing exists, add a new helper service + trait — mirror existing pattern from `TextHelperService` or similar

## Compose helpers into a class

- [ ] `use <Helper>Trait;` at the top of the class
- [ ] Call `$this->get<Helper>()` to access the helper service
- [ ] Need multiple? List them all — no upper limit, services routinely use 4-8 traits

## Add a new helper service + trait

- [ ] Service class in `src/plugins/orangehrm{X}Plugin/Service/<Name>HelperService.php`
- [ ] Matching trait in `Traits/Service/<Name>HelperTrait.php` with `?<Name>HelperService $field` and lazy `get<Name>Helper()` method
- [ ] If the helper needs DI (config, EM, dispatcher) — register in plugin's `PluginConfiguration::initialize()` and have the trait fetch from the container instead of `new`-ing
- [ ] If pure (no DI), the lazy `new` is fine — no container registration needed
- [ ] Add a row to your project's "Is there a helper for…" table so future devs find it

## Things that bite

- **Don't bypass the trait** and instantiate helpers directly with `new ConfigService()`. Some traits cache the service per-class; bypassing means an extra instantiation per call. More importantly, it breaks the convention — other devs look for `use ConfigServiceTrait` and don't find it.
- **Don't put business logic in a helper** — helpers are *utilities* (formatters, normalizers, lookups). Business logic that involves the domain belongs in a Service (see `services` skill).
- **`AuthUserTrait` only works inside a request lifecycle.** In console commands or migrations, the session isn't bound — `getAuthUser()->getUserId()` returns null. Either pass a user ID as a command argument or work without one.
- **`getEntityManager()` is fine in any context** that's run after `Framework` is bootstrapped (which is everywhere except very early install bootstrap). Use it freely.
- **`use`-ing two traits with the same method name is a fatal error in PHP**. Rare here because OHRM trait method names are distinctive, but if you ever see `Trait method getX has not been applied`, you've got a name collision — rename one of the methods.
- **Helpers are stateless.** Don't store request-specific data on a helper service — different callers might share the same instance. If you need request state, use the service itself, not a helper.
- **`ServiceContainerTrait` is internal plumbing** — don't `use` it directly from feature code. Use the higher-level traits that wrap container access.
