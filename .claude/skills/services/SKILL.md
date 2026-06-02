---
name: services
description: Reference for OrangeHRM's service layer — the `*/Service/<Name>Service.php` classes that sit between Endpoints/Controllers and DAOs and hold business logic, the `*ServiceTrait` DI access pattern, the lazy-getter convention for composing services, where to register services in `PluginConfigurationInterface::initialize()`, and what belongs in a service vs in a DAO vs on an entity Decorator. Use whenever the user is adding a service method, deciding where business logic should live, composing one service from another, or asking "is there already a service for X?" Companion to `daos` (the layer services call into), `rest-endpoints` (the layer that calls services), `entities` (the Decorator alternative for entity-bound logic).
---

# The service layer

OrangeHRM has a three-tier backend:

```
API Endpoint / Page Controller
   ↓ calls
Service (business logic; orchestrates multiple DAOs + events + config)
   ↓ calls
DAO (single-entity persistence + queries)
   ↓ talks to
EntityManager → DB
```

Services are the **middle tier** — they live in `src/plugins/orangehrm{X}Plugin/Service/<Name>Service.php`. Every feature in the codebase touches at least one. This skill covers how they're structured, how to access them, and what belongs in them.

For the layers around services: see `rest-endpoints` (the caller side), `daos` (the data side), `entities` (for entity-bound logic that doesn't need a service).

## The shape of a service

```php
<?php
namespace OrangeHRM\X\Service;

use OrangeHRM\X\Dao\WidgetDao;
use OrangeHRM\X\Dto\WidgetSearchFilterParams;
use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class WidgetService
{
    use EventDispatcherTrait;
    use ConfigServiceTrait;

    protected ?WidgetDao $widgetDao = null;

    public function getWidgetDao(): WidgetDao
    {
        if (is_null($this->widgetDao)) {
            $this->widgetDao = new WidgetDao();
        }
        return $this->widgetDao;
    }

    public function setWidgetDao(WidgetDao $dao): void  // for tests
    {
        $this->widgetDao = $dao;
    }

    public function getWidgetList(WidgetSearchFilterParams $params): array
    {
        return $this->getWidgetDao()->getWidgetList($params);
    }

    public function saveWidget(Widget $widget): Widget
    {
        $widget = $this->getWidgetDao()->saveWidget($widget);
        $this->getEventDispatcher()->dispatch(
            new WidgetSavedEvent($widget),
            WidgetEvents::WIDGET_SAVED,
        );
        return $widget;
    }
}
```

Six things to notice:

1. **Plain PHP class.** No interface to implement, no abstract base. The service shape is convention, not framework-enforced.
2. **Traits for cross-cutting concerns** — `EventDispatcherTrait`, `ConfigServiceTrait`, `UserRoleManagerTrait`, etc. Mix in what the service needs.
3. **`?Dao $dao = null` property + lazy-getter** — the universal pattern for the service's own DAO and any sub-services it composes.
4. **Public `setDao()` for test injection** — every lazy-getter is paired with a setter so unit tests can inject a mock.
5. **Methods delegate to DAO for data**, then layer business logic (events, validation, side effects) on top.
6. **No `__construct` (typically).** Services are instantiated bare; the DI container hands one out per request.

## How services are registered and accessed

### Registration in `PluginConfigurationInterface::initialize()`

Each plugin's `config/<Name>PluginConfiguration.php` registers its services with the DI container:

```php
namespace OrangeHRM\X;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use OrangeHRM\X\Service\WidgetService;

class XPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::WIDGET_SERVICE,
            WidgetService::class,
        );
    }
}
```

Two things:
- The service container is a Symfony DI container (see `doctrine-bootstrap` skill's "Accessing the EM" section for context on the same container).
- `Services::WIDGET_SERVICE` is a string constant added to `src/lib/framework/Services.php` (e.g. `public const WIDGET_SERVICE = 'x.widget_service';`). Service IDs are `<plugin>.<service_name>` lowercase.

### Access via the `*ServiceTrait`

Every service has a matching trait at `src/plugins/orangehrm{X}Plugin/Traits/Service/<Name>ServiceTrait.php`:

```php
namespace OrangeHRM\X\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\X\Service\WidgetService;

trait WidgetServiceTrait
{
    use ServiceContainerTrait;

    public function getWidgetService(): WidgetService
    {
        return $this->getContainer()->get(Services::WIDGET_SERVICE);
    }
}
```

Then any consumer (Endpoint, Controller, another Service, Subscriber) just `use`s the trait:

```php
class WidgetAPI extends Endpoint implements CrudEndpoint
{
    use WidgetServiceTrait;

    public function getOne(): EndpointResourceResult
    {
        $widget = $this->getWidgetService()->getWidgetById($id);
        // …
    }
}
```

**This is the canonical access pattern** — almost every cross-plugin service access goes through a Trait, not via `new WidgetService()` or direct container calls. The Trait is the public API of the service.

When you create a new service, **create the matching Trait at the same time** and put both in the plugin's namespace.

## The lazy-getter pattern for composing services

Inside a service, when you need to call another service or a DAO:

```php
protected ?EmployeeEventService $employeeEventService = null;

public function getEmployeeEventService(): EmployeeEventService
{
    if (!$this->employeeEventService instanceof EmployeeEventService) {
        $this->employeeEventService = new EmployeeEventService();
    }
    return $this->employeeEventService;
}
```

**Pattern rules**:
- `?Type $field = null` property
- `getXxx()` checks-and-instantiates with `new` (not via the container)
- `setXxx()` paired setter for test injection
- Never inject in constructor — services have no constructor

**Wait — why `new` instead of `getContainer()->get()` for sub-services?**

Most services compose with `new`. It's the dominant pattern. Side effect: services aren't singletons within a single request — calling `getEmployeeService()` from two different places creates two `EmployeeService` instances. That's fine because services are stateless (no per-instance cache, no per-instance state).

The container-registered service IDs (`Services::*`) are mainly the entry-point services that **outside-plugin** code reaches via the Trait. Internal composition between services in the same plugin can go either way.

This is a quirk worth flagging. If you find yourself debugging "I set a value on `EmployeeService` and another call doesn't see it," the answer is: those were two different instances. Services should be stateless.

## What belongs in a service vs. elsewhere

### Service — business logic that orchestrates

- Multi-step operations: "save employee" = generate ID + persist + dispatch event + send notification + audit log
- Cross-DAO operations: queries that join across logical boundaries
- Anything that fires an event after a persistence operation
- Anything that reads/writes config + persists
- Permission-conditional logic that's bigger than a single check (small checks belong on the API endpoint or in a validator rule)

### DAO — pure persistence

- "Get me rows matching these criteria" → DAO
- "Save this entity" → DAO (one method on a service typically wraps it)
- "Delete by ID" → DAO

Anything that's only a SQL query lives in the DAO. The service is the thin layer that knows the *meaning* of that query.

### Entity Decorator — convenience methods bound to a single entity instance

- `setLocationById($id)` — fetches related entity via `getReference()` and calls the setter
- `getFullName()` — composes scalar fields
- Formatters that need DI (`getJoinedDate()` calls `DateTimeHelperService`)

If the logic is "operations on this one entity, may need a service to assist," it's a Decorator method. See `entities` skill.

### Where it gets murky

- **"Send an email when an employee is created"** — service (`EmployeeService::saveEmployee` dispatches `EmployeeAddedEvent`, a subscriber in the mail plugin reacts). The service doesn't know about email; the event/subscriber decoupling is intentional.
- **"Validate that an employee number doesn't already exist"** — validator rule (`Rules::ENTITY_UNIQUE_PROPERTY`), not a service method. See `rest-validation`.
- **"Format an employee's name for display"** — Decorator on the entity. Pure presentation, no service needed.
- **"Get all employees the current user can see"** — service (calls `UserRoleManager::getAccessibleEntityIds` + DAO list method).

## Common service trait imports

The DI container exposes a lot through traits. The ones services typically pull in:

| Trait | Gives `$this->…` |
|---|---|
| `EventDispatcherTrait` | `getEventDispatcher()` → for dispatching events |
| `ConfigServiceTrait` | `getConfigService()` → for `hs_hr_config` reads/writes (see `config` skill) |
| `NormalizerServiceTrait` | `getNormalizerService()` → for normalizing entities |
| `UserRoleManagerTrait` | `getUserRoleManager()` → for permission checks |
| `DateTimeHelperTrait` | `getDateTimeHelper()` → for timezones / formats |
| `TextHelperTrait` | `getTextHelper()` → for string operations |
| `NumberHelperTrait` | `getNumberHelper()` → for numeric ops |
| `LoggerTrait` | `getLogger()` → for log writes |
| `AuthUserTrait` | `getAuthUser()` → for current-user info |

See the `helpers` skill for the full helper trait catalog.

When pulling in a service from **another plugin**, use that plugin's `<Name>ServiceTrait`. Cross-plugin dependencies are fine and common — `EmployeeService::use UserServiceTrait` (from Admin plugin) pulls in `UserService` for user-related operations.

## How services are invoked from elsewhere

| Caller | Pattern |
|---|---|
| API Endpoint | `use WidgetServiceTrait; … $this->getWidgetService()->saveWidget(...)` |
| Page Controller | Same — `use WidgetServiceTrait` |
| Event Subscriber | Same — `use WidgetServiceTrait` |
| Another Service | Lazy-getter composing via `new WidgetService()`, OR `use WidgetServiceTrait` for cross-plugin |
| Decorator | Same as service — usually `EntityManagerHelperTrait` + lookups by ID, but can `use *ServiceTrait` if needed |
| Console command | `use WidgetServiceTrait` |
| Test | Inject a mock via the service's `setXxx()` setter |

The Trait abstraction means the consumer doesn't know whether the service is container-registered or freshly instantiated — same call shape either way.

---

# Recipes

## Recipe 1 — A new service for a new resource

```php
<?php
// src/plugins/orangehrmXPlugin/Service/WidgetService.php
namespace OrangeHRM\X\Service;

use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Entity\Widget;
use OrangeHRM\X\Dao\WidgetDao;
use OrangeHRM\X\Dto\WidgetSearchFilterParams;
use OrangeHRM\X\Event\WidgetEvents;
use OrangeHRM\X\Event\WidgetSavedEvent;

class WidgetService
{
    use EventDispatcherTrait;

    protected ?WidgetDao $widgetDao = null;

    public function getWidgetDao(): WidgetDao
    {
        if (is_null($this->widgetDao)) {
            $this->widgetDao = new WidgetDao();
        }
        return $this->widgetDao;
    }

    public function setWidgetDao(WidgetDao $dao): void
    {
        $this->widgetDao = $dao;
    }

    public function getWidgetById(int $id): ?Widget
    {
        return $this->getWidgetDao()->getWidgetById($id);
    }

    public function getWidgetList(WidgetSearchFilterParams $params): array
    {
        return $this->getWidgetDao()->getWidgetList($params);
    }

    public function getWidgetCount(WidgetSearchFilterParams $params): int
    {
        return $this->getWidgetDao()->getWidgetCount($params);
    }

    public function saveWidget(Widget $widget): Widget
    {
        $isNew = !$widget->getId();
        $widget = $this->getWidgetDao()->saveWidget($widget);

        $this->getEventDispatcher()->dispatch(
            new WidgetSavedEvent($widget, $isNew),
            WidgetEvents::WIDGET_SAVED,
        );

        return $widget;
    }

    public function deleteWidgets(array $ids): int
    {
        return $this->getWidgetDao()->deleteWidgets($ids);
    }
}
```

Matching trait:

```php
<?php
// src/plugins/orangehrmXPlugin/Traits/Service/WidgetServiceTrait.php
namespace OrangeHRM\X\Traits\Service;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\X\Service\WidgetService;

trait WidgetServiceTrait
{
    use ServiceContainerTrait;

    public function getWidgetService(): WidgetService
    {
        return $this->getContainer()->get(Services::WIDGET_SERVICE);
    }
}
```

Service ID constant in `src/lib/framework/Services.php`:

```php
public const WIDGET_SERVICE = 'x.widget_service';
```

Register in `config/XPluginConfiguration.php`:

```php
public function initialize(Request $request): void
{
    $this->getContainer()->register(Services::WIDGET_SERVICE, WidgetService::class);
}
```

Consumer (API endpoint):

```php
use OrangeHRM\X\Traits\Service\WidgetServiceTrait;

class WidgetAPI extends Endpoint implements CrudEndpoint
{
    use WidgetServiceTrait;

    public function create(): EndpointResourceResult
    {
        $widget = new Widget();
        // … populate from request body …
        $widget = $this->getWidgetService()->saveWidget($widget);
        return new EndpointResourceResult(WidgetModel::class, $widget);
    }
}
```

## Recipe 2 — Composing a service from another service

When `WidgetService::saveWidget` needs to also create an audit log entry:

```php
class WidgetService
{
    use EventDispatcherTrait;
    use UserServiceTrait;                            // ← bring in UserService for current user lookup

    protected ?WidgetDao $widgetDao = null;
    protected ?WidgetAuditService $widgetAuditService = null;

    public function getWidgetAuditService(): WidgetAuditService
    {
        if (is_null($this->widgetAuditService)) {
            $this->widgetAuditService = new WidgetAuditService();   // ← in-plugin: new
        }
        return $this->widgetAuditService;
    }

    public function saveWidget(Widget $widget): Widget
    {
        $widget = $this->getWidgetDao()->saveWidget($widget);
        $this->getWidgetAuditService()->logChange($widget, $this->getUserService()->getCurrentUser());
        return $widget;
    }
}
```

In-plugin sub-service via `new` + lazy getter. Cross-plugin via `*ServiceTrait` (here, `UserServiceTrait` from Admin).

## Recipe 3 — Service that wraps a transaction

When the operation must be atomic:

```php
class WidgetService
{
    use EventDispatcherTrait;

    public function deleteWidgetWithDependents(int $widgetId): void
    {
        $this->getWidgetDao()->beginTransaction();
        try {
            $this->getWidgetDao()->deleteDependents($widgetId);
            $this->getWidgetDao()->deleteWidget($widgetId);
            $this->getWidgetDao()->commitTransaction();

            $this->getEventDispatcher()->dispatch(/* … */);
        } catch (Throwable $e) {
            $this->getWidgetDao()->rollBackTransaction();
            throw $e;
        }
    }
}
```

Transaction boundaries can live on the DAO (when scoped to one DAO's operations) OR the service (when spanning multiple). See `daos` skill — same pattern, just one level up.

---

# Checklists

## Add a new service

- [ ] Create `src/plugins/orangehrm{X}Plugin/Service/<Name>Service.php`
- [ ] Plain class (no `__construct`, no interface)
- [ ] `protected ?<Dep> $dep = null;` for the DAO and each composed sub-service
- [ ] `getDep()` lazy-getter + `setDep()` test-injection setter for each
- [ ] Pull in traits for cross-cutting concerns (`EventDispatcherTrait`, `ConfigServiceTrait`, etc.)
- [ ] Create the matching `Traits/Service/<Name>ServiceTrait.php` with `get<Name>Service()`
- [ ] Add `<Name>_SERVICE = '<plugin>.<resource>_service'` constant to `src/lib/framework/Services.php`
- [ ] Register in plugin's `<Name>PluginConfiguration::initialize()` via `$this->getContainer()->register(Services::*, <Name>Service::class)`

## Decide where to put a piece of logic

- [ ] **One SQL query, no other logic** → DAO method
- [ ] **Multi-step persistence + side effects** → Service method
- [ ] **Operations on a single entity that need DI** → Decorator method (see `entities` skill)
- [ ] **Input validation** → ParamRule in the API endpoint (see `rest-validation`)
- [ ] **Authorization (role-based)** → permission seeding + `ApiAuthorizationSubscriber` (see `authorization`)
- [ ] **Row-level access (self / ownership)** → enforced inside the service or endpoint method, not via a validator

## Add a sub-service dependency

- [ ] In-plugin sub-service → `protected ?X = null;` + lazy getter using `new X()`
- [ ] Cross-plugin service → `use <Plugin>\Traits\Service\<Name>ServiceTrait;`
- [ ] **Don't** inject in a constructor — services don't take constructor args by convention

## Things that bite

- **Services are not request-scoped singletons** when composed via `new`. Each `getXxxService()` call inside another service creates a fresh instance. Don't store per-instance state (caches, accumulated values) on a service — store it elsewhere or pass it through.
- **Forgetting to register a service** in `<Plugin>Configuration::initialize()` produces a "service not found in container" error when the Trait's `get…Service()` runs. The class autoloads fine; the container lookup fails.
- **Adding to `Services::*` without using the constant** — the registration line silently registers under a literal string and the Trait's `get(Services::FOO)` returns `null` (or throws). Always use the constant in both places.
- **Lazy getters via `new` skip the container** — they bypass any container-level configuration (interceptors, lazy services). Fine for the project's needs but be aware if you're trying to do something clever.
- **Putting validation in a service** instead of a ParamRule pushes the failure mode from a clean 422 to a 400/500 with a custom message. Validation belongs in the rule layer (see `rest-validation`).
- **A service that takes a constructor argument** breaks the container's `register(ServiceId, ClassName::class)` pattern, which uses zero-arg construction. If you genuinely need construction parameters, register via `register(ServiceId)->setFactory([…, 'method'])` instead — but the convention is to avoid this by using lazy-getters for dependencies.
