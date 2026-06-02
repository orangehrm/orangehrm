---
name: events
description: Reference for OrangeHRM's event system — the `EventDispatcher` (Symfony EventDispatcher with no project-level customization), `AbstractEventSubscriber` for class-based subscribers, the per-plugin `Subscriber/` and `Event/` directories, the `<Plugin>Events` constant-holder class convention, `Event`-extending event payload classes, plugin-level subscriber registration in `<Plugin>PluginConfiguration::initialize()` via `getEventDispatcher()->addSubscriber()`, listener priorities, propagation control, and the distinction between Symfony `KernelEvents` (used by core subscribers like `ApiAuthorizationSubscriber`, `AuthenticationSubscriber`) and OHRM custom events (used by feature plugins to react to business actions like `EmployeeSavedEvent`, `LeaveApply`). Use whenever the user is dispatching a new event, writing a subscriber, registering a subscriber on plugin boot, debugging "why didn't my listener fire", or asking about event order / priority. Companion to `services` (services dispatch events), `authorization` (its subscribers are the canonical examples of `KernelEvents` listeners), `mail` (subscribers are how emails get sent reactively).
---

# The event system

OrangeHRM uses **Symfony's EventDispatcher** for decoupled, in-process pub-sub. Services dispatch events; subscribers in any plugin can react. Two flavors of events flow through the same dispatcher:

1. **Framework events** (`Symfony\Component\HttpKernel\KernelEvents`) — request/controller/response/exception/terminate. Used by the auth + authorization + exception subscribers (see `authorization` skill).
2. **OHRM domain events** — `EmployeeSavedEvent`, `LeaveApply`, `ModuleStatusChange`, etc. Used by feature plugins to react to business actions. **This is what new code typically interacts with.**

This skill covers both. For the auth/authorization-specific subscribers, see `authorization`. For dispatching from services, see `services`.

## The wiring — `Framework\Event\EventDispatcher`

```php
namespace OrangeHRM\Framework\Event;

use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

class EventDispatcher extends BaseEventDispatcher {}
```

That's the whole class — a one-line subclass with zero customization. Registered as `Services::EVENT_DISPATCHER` in `Framework::configureContainer()` (see `doctrine-bootstrap` for the same DI container).

Two consumer-facing pieces:

```php
namespace OrangeHRM\Framework\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEventSubscriber implements EventSubscriberInterface {}
```

Also a thin wrapper — implements `EventSubscriberInterface` from Symfony, which requires the static `getSubscribedEvents()` method.

And on the consumer side, `EventDispatcherTrait` (from `OrangeHRM\Core\Traits`) gives `$this->getEventDispatcher()` to any class that needs to dispatch.

## Accessing the dispatcher

### To dispatch (in a service)

```php
use OrangeHRM\Core\Traits\EventDispatcherTrait;

class WidgetService
{
    use EventDispatcherTrait;

    public function saveWidget(Widget $widget): Widget
    {
        $widget = $this->getWidgetDao()->saveWidget($widget);
        $this->getEventDispatcher()->dispatch(
            new WidgetSavedEvent($widget),         // ← Event object (carries the payload)
            WidgetEvents::WIDGET_SAVED,             // ← Event name string
        );
        return $widget;
    }
}
```

Two-argument `dispatch($event, $eventName)`:
- `$event` is the Event-extending object that carries data to listeners.
- `$eventName` is the string key listeners subscribe to.

### To listen (in a subscriber class)

```php
use OrangeHRM\Framework\Event\AbstractEventSubscriber;

class WidgetAuditSubscriber extends AbstractEventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            WidgetEvents::WIDGET_SAVED => [['onSavedEvent', 0]],   // [methodName, priority]
        ];
    }

    public function onSavedEvent(WidgetSavedEvent $event): void
    {
        // react to the event
    }
}
```

`getSubscribedEvents()` returns `[eventName => [[methodName, priority], ...]]`. Same event can map to multiple methods at different priorities.

## Defining events for your feature

Convention: each plugin has an `Event/` directory with one `<Plugin>Events.php` constant-holder + one event class per event.

### The constant-holder class

`src/plugins/orangehrmPimPlugin/Event/EmployeeEvents.php`:

```php
namespace OrangeHRM\Pim\Event;

class EmployeeEvents
{
    /** @see \OrangeHRM\Pim\Event\EmployeeAddedEvent */
    public const EMPLOYEE_ADDED = 'pim.employee_added';

    /** @see \OrangeHRM\Pim\Event\EmployeeSavedEvent */
    public const EMPLOYEE_SAVED = 'pim.employee_saved';

    /** @see \OrangeHRM\Pim\Event\EmployeeDeletedEvent */
    public const EMPLOYEES_DELETED = 'pim.employees_deleted';

    /** @see \OrangeHRM\Pim\Event\EmployeeJoinedDateChangedEvent */
    public const JOINED_DATE_CHANGED = 'pim.employee_join_date_changed';
}
```

Naming:
- File: `<Plugin>Events.php`, class `<Plugin>Events`
- Constants: `UPPER_SNAKE_CASE`, value `<plugin>.<event_name>` (lowercase, dotted)
- `@see` PHPDoc pointing at the matching Event class — devs grep the constant to find the payload type

### The event class

`src/plugins/orangehrmPimPlugin/Event/EmployeeSavedEvent.php`:

```php
namespace OrangeHRM\Pim\Event;

use OrangeHRM\Entity\Employee;
use OrangeHRM\Framework\Event\Event;       // ← OHRM's Event base, thin wrapper around Symfony's

class EmployeeSavedEvent extends Event
{
    private Employee $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
```

Five-step shape:
1. Extends `OrangeHRM\Framework\Event\Event` (which extends Symfony's `Event`).
2. Constructor takes the payload data — typically an entity or a few primitives.
3. Read-only getters for whatever listeners need.
4. **Don't** put behavior on the event itself — events are immutable carriers.
5. **Don't** make the event class abstract unless multiple subclasses share the payload shape. For most events, a single concrete class is fine.

### Event inheritance for "this is a special case of that"

`EmployeeAddedEvent` extends `EmployeeSavedEvent`:

```php
class EmployeeAddedEvent extends EmployeeSavedEvent {}
```

Use this when you want listeners to be able to react to "any save" via the parent class **and** to "only the create-time save" via the subclass. Then dispatch the most-specific class:

```php
$event = $isNew ? new EmployeeAddedEvent($employee) : new EmployeeSavedEvent($employee);
$this->getEventDispatcher()->dispatch($event, WhicheverEventName);
```

This is a real pattern in PIM but not common — most events stand alone.

## Writing a subscriber

Two common locations:

| Location | Use for |
|---|---|
| `src/plugins/orangehrm{X}Plugin/Subscriber/<Name>Subscriber.php` | Plugin-owned subscribers that react to events from this plugin or others. |
| `src/plugins/orangehrmCorePlugin/Subscriber/<Name>Subscriber.php` | Cross-cutting framework-level subscribers (the auth + authorization + exception + mailer subscribers all live here). |

### Class shape

```php
namespace OrangeHRM\X\Subscriber;

use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Pim\Event\EmployeeEvents;
use OrangeHRM\Pim\Event\EmployeeSavedEvent;

class WidgetSyncOnEmployeeSaveSubscriber extends AbstractEventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            EmployeeEvents::EMPLOYEE_SAVED => [['onEmployeeSaved', 0]],
        ];
    }

    public function onEmployeeSaved(EmployeeSavedEvent $event): void
    {
        $employee = $event->getEmployee();
        // … your reaction
    }
}
```

Or with multiple events + priorities (the LeaveEventSubscriber pattern):

```php
public static function getSubscribedEvents(): array
{
    return [
        LeaveEvent::APPLY   => [['onAllocateEvent', 0]],
        LeaveEvent::ASSIGN  => [['onAllocateEvent', 0]],
        LeaveEvent::APPROVE => [['onStatusChangeEvent', 0]],
        LeaveEvent::CANCEL  => [['onStatusChangeEvent', 0]],
        LeaveEvent::REJECT  => [['onStatusChangeEvent', 0]],
    ];
}
```

One subscriber class can listen to multiple events and dispatch to different handler methods.

### Method signature

The handler method receives the event object as its only argument (typed to the most specific class it expects):

```php
public function onEmployeeSaved(EmployeeSavedEvent $event): void { … }
```

For Symfony `KernelEvents`, the argument is one of `RequestEvent`, `ControllerEvent`, `ResponseEvent`, `ExceptionEvent`, `TerminateEvent`. Examples in `authorization` skill.

### Composing helpers into a subscriber

Subscribers are plain classes. Pull in traits the same way services do:

```php
class WidgetAuditSubscriber extends AbstractEventSubscriber
{
    use LoggerTrait;
    use ConfigServiceTrait;
    use WidgetServiceTrait;

    public function onSavedEvent(WidgetSavedEvent $event): void
    {
        if ($this->getConfigService()->getAuditEnabled()) {
            $this->getLogger()->info('Widget saved: ' . $event->getWidget()->getId());
            $this->getWidgetService()->logAudit($event->getWidget());
        }
    }
}
```

Same DI access patterns as services (see `services` skill).

## Registering subscribers — plugin boot

Subscribers don't auto-register from filesystem scans. **Every subscriber must be explicitly added in some plugin's `<Plugin>PluginConfiguration::initialize()`** to start receiving events.

```php
namespace OrangeHRM\X;

use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\X\Subscriber\WidgetAuditSubscriber;

class XPluginConfiguration implements PluginConfigurationInterface
{
    use EventDispatcherTrait;

    public function initialize(Request $request): void
    {
        $this->getEventDispatcher()->addSubscriber(new WidgetAuditSubscriber());
        // … other initialization
    }
}
```

**`addSubscriber()` takes an instance**, not a class name. Don't worry about request-time vs boot-time instantiation — `initialize()` runs once per request before the dispatch happens.

Convention: register all of a plugin's own subscribers in that plugin's `initialize()`. The core plugin's `CorePluginConfiguration` has ~10 subscriber registrations covering the framework-wide subscribers (`ExceptionSubscriber`, `ApiAuthorizationSubscriber`, `MailerSubscriber`, etc.).

### Conditional registration

Some subscribers only matter in certain contexts. Wrap in an `if`:

```php
if (Config::isInstalled()) {
    $this->getEventDispatcher()->addSubscriber(new RegistrationEventPersistSubscriber());
}
```

The `RegistrationEventPersistSubscriber` (in CorePluginConfiguration) only registers post-install because it writes to a table that doesn't exist during initial install.

## Priority and ordering

`[methodName, priority]` — higher priority runs first. Default `0`.

Real priorities from the codebase:
- `AuthenticationSubscriber::onControllerEvent` — priority `100000` (runs first on every controller event)
- `ApiAuthorizationSubscriber::onControllerEvent` — priority `80000` (runs second)
- `ScreenAuthorizationSubscriber::onControllerEvent` — priority `80000` (same tier, order undefined between them)
- `RouterListener::onKernelRequest` — priority `99500` (routing happens early)
- `RouterListener::onKernelException` — priority `-64` (lowest, fallback)

**The numbers are large gaps on purpose** — there's room to insert subscribers between `80000` and `100000` without re-numbering existing ones.

For OHRM domain events, priority is usually `0` because there's typically only one or two subscribers per event. Use larger priorities if you need to guarantee one subscriber sees the event before another.

## Propagation control

A subscriber can stop further subscribers from receiving an event by calling `$event->stopPropagation()`:

```php
public function onExceptionEvent(ExceptionEvent $event): void
{
    if ($exception instanceof ForbiddenException) {
        $response = /* … build forbidden response … */;
        $event->setResponse($response);
        $event->stopPropagation();        // ← later subscribers won't see this exception
    }
}
```

Used heavily in the exception subscribers — once one of them has produced a response, the others shouldn't override it.

For domain events, propagation stopping is rare and usually wrong — multiple plugins might want to react to "employee saved" independently. Reach for it only when one subscriber's reaction makes others' invalid.

## Symfony `KernelEvents` — the framework-level events

These fire for every HTTP request. Listed in priority of typical order of importance:

| KernelEvent | When it fires | Common listeners |
|---|---|---|
| `KernelEvents::REQUEST` | Right after the request is parsed, before routing | `RouterListener` (routing), `AuthenticationSubscriber::onRequestEvent` (early bail-out for unauthenticated public routes) |
| `KernelEvents::CONTROLLER` | After routing resolved the controller, before invoking it | `AuthenticationSubscriber::onControllerEvent` (priority 100000), `ApiAuthorizationSubscriber`, `ScreenAuthorizationSubscriber` (priority 80000), `RequestBodySubscriber` (parses JSON body), `ModuleNotAvailableSubscriber` |
| `KernelEvents::RESPONSE` | After the controller produced a Response, before sending | Headers, logging, debugging |
| `KernelEvents::EXCEPTION` | When anything throws | `RouterListener::onKernelException` (404 handling), `ApiAuthorizationSubscriber::onExceptionEvent` (turns `ForbiddenException` into JSON 403), `AuthenticationSubscriber::onExceptionEvent` (turns `SessionExpiredException` into redirect), `ExceptionSubscriber`, `RequestForwardableExceptionSubscriber` |
| `KernelEvents::TERMINATE` | After the response is sent | Cleanup work that shouldn't delay the user |
| `KernelEvents::FINISH_REQUEST` | When a (sub-)request finishes | `RouterListener::onKernelFinishRequest` |

If you find yourself needing to hook into one of these, look at the existing subscribers as templates — they're all in `orangehrmCorePlugin/Subscriber/` and the auth plugin.

## Where existing OHRM domain events live

Quick map (use this to figure out what events already exist before inventing new ones):

| Plugin | Events file | Examples |
|---|---|---|
| Pim | `Event/EmployeeEvents` | `EMPLOYEE_ADDED`, `EMPLOYEE_SAVED`, `EMPLOYEES_DELETED`, `JOINED_DATE_CHANGED` |
| Leave | `Event/LeaveEvent` | `APPLY`, `ASSIGN`, `APPROVE`, `CANCEL`, `REJECT`, `ALLOCATE` |
| Maintenance | `Event/MaintenanceEvent` | `PurgeEmployee` |
| Core | `Event/ModuleEvent` | `ModuleStatusChange` (used by Dashboard plugin's three module-status subscribers) |
| Buzz | per-event classes | various |

**Before defining a new event**, check if an existing one carries enough information. E.g., "I need to react when employee email changes" → just listen to `EmployeeSavedEvent` and compare against the prior state.

---

# Recipes

## Recipe 1 — Define and dispatch a new domain event

Constants:

```php
// src/plugins/orangehrmXPlugin/Event/WidgetEvents.php
namespace OrangeHRM\X\Event;

class WidgetEvents
{
    /** @see \OrangeHRM\X\Event\WidgetSavedEvent */
    public const WIDGET_SAVED = 'x.widget_saved';

    /** @see \OrangeHRM\X\Event\WidgetDeletedEvent */
    public const WIDGET_DELETED = 'x.widget_deleted';
}
```

Event class:

```php
// src/plugins/orangehrmXPlugin/Event/WidgetSavedEvent.php
namespace OrangeHRM\X\Event;

use OrangeHRM\Entity\Widget;
use OrangeHRM\Framework\Event\Event;

class WidgetSavedEvent extends Event
{
    public function __construct(private Widget $widget, private bool $isNew) {}
    public function getWidget(): Widget { return $this->widget; }
    public function isNew(): bool       { return $this->isNew; }
}
```

Dispatch from the service:

```php
class WidgetService
{
    use EventDispatcherTrait;

    public function saveWidget(Widget $widget): Widget
    {
        $isNew  = !$widget->getId();
        $widget = $this->getWidgetDao()->saveWidget($widget);

        $this->getEventDispatcher()->dispatch(
            new WidgetSavedEvent($widget, $isNew),
            WidgetEvents::WIDGET_SAVED,
        );

        return $widget;
    }
}
```

## Recipe 2 — React to an event with a subscriber

```php
// src/plugins/orangehrmXPlugin/Subscriber/WidgetAuditSubscriber.php
namespace OrangeHRM\X\Subscriber;

use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\X\Event\WidgetEvents;
use OrangeHRM\X\Event\WidgetSavedEvent;

class WidgetAuditSubscriber extends AbstractEventSubscriber
{
    use LoggerTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            WidgetEvents::WIDGET_SAVED => [['onSavedEvent', 0]],
        ];
    }

    public function onSavedEvent(WidgetSavedEvent $event): void
    {
        $action = $event->isNew() ? 'created' : 'updated';
        $this->getLogger()->info("Widget {$event->getWidget()->getId()} $action");
    }
}
```

Register in the plugin's configuration:

```php
public function initialize(Request $request): void
{
    $this->getEventDispatcher()->addSubscriber(new WidgetAuditSubscriber());
}
```

## Recipe 3 — Cross-plugin subscriber (react to another plugin's event)

The dashboard plugin reacts to module status changes from core:

```php
namespace OrangeHRM\Dashboard\Subscriber;

use OrangeHRM\Core\Event\ModuleEvent;
use OrangeHRM\Core\Event\ModuleStatusChange;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;

class BuzzModuleStatusChangeSubscriber extends AbstractEventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            ModuleEvent::MODULE_STATUS_CHANGE => [['onStatusChange', 0]],
        ];
    }

    public function onStatusChange(ModuleStatusChange $event): void
    {
        if ($event->getModule() === 'buzz') {
            // toggle buzz-related dashboard widgets
        }
    }
}
```

Register in the *consuming* plugin's `<Plugin>PluginConfiguration::initialize()` (dashboard, not core). The producer doesn't need to know who's listening.

## Recipe 4 — Listen to a Symfony `KernelEvents` event

```php
namespace OrangeHRM\X\Subscriber;

use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestTimingSubscriber extends AbstractEventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['onResponse', -100]],          // run after most listeners
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('X-Render-Time', $this->renderTime());
    }
}
```

Negative priority runs late (after most other RESPONSE listeners). Positive runs early.

## Recipe 5 — Stop propagation when handling an exception

```php
class WidgetExceptionSubscriber extends AbstractEventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => [['onException', 10]]];
    }

    public function onException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof WidgetSpecificException) {
            $event->setResponse(new Response('Widget error', 400));
            $event->stopPropagation();
        }
    }
}
```

Only stop propagation when you've handled the case completely. Generic logging subscribers should never stop propagation.

---

# Checklists

## Define a new event

- [ ] Create or extend `<Plugin>Events.php` constant-holder; constants `UPPER_SNAKE_CASE`, values `<plugin>.<name>`
- [ ] Create event class in `<Plugin>Plugin/Event/<Name>Event.php` extending `OrangeHRM\Framework\Event\Event`
- [ ] Constructor takes the payload; getters expose it; no behavior on the event class itself
- [ ] PHPDoc `@see` from the constant to the event class
- [ ] Dispatch via `$this->getEventDispatcher()->dispatch(new <Name>Event(...), <Plugin>Events::<NAME>)` from a service
- [ ] If subclassing for "special case": child's constructor calls parent's; listeners typing the parent will still match

## Write a new subscriber

- [ ] Class in plugin's `Subscriber/` directory extending `AbstractEventSubscriber`
- [ ] `getSubscribedEvents()` returns `[eventName => [[method, priority]]]` shape
- [ ] Handler method signature typed to the most specific event class
- [ ] Trait composition for any helpers/services needed
- [ ] **Register in the plugin's `<Plugin>PluginConfiguration::initialize()`** via `$this->getEventDispatcher()->addSubscriber(new <Name>Subscriber())`

## Debug "my subscriber isn't firing"

- [ ] **Is the subscriber registered?** Search for `addSubscriber(new MyClass(` in `PluginConfiguration` files. Missing registration is the most common cause.
- [ ] **Does the event name match exactly?** Compare what you dispatched (`<Plugin>Events::X`) with what you subscribed to. Typo here is silent — Symfony's dispatcher has no "unknown event" warning.
- [ ] **Is the subscriber's plugin configuration loaded?** Plugins are loaded based on `ohrm_plugin_configs` (see `doctrine-bootstrap`); a plugin that isn't registered won't have its subscribers wired up.
- [ ] **Is propagation stopped earlier?** Higher-priority subscribers can call `stopPropagation()` and you wouldn't see it. Add a temporary `error_log` in your handler to confirm the priority comparison.
- [ ] **For `KernelEvents`** — is your priority placing you before the response was set? Some event handlers exit early if `$event->hasResponse()` is true.

## Things that bite

- **No "magic" subscriber discovery.** Subscribers don't auto-register based on filesystem location. The explicit `addSubscriber()` call in `<Plugin>PluginConfiguration::initialize()` is the **only** thing that wires them up. Drop a `.php` file in `Subscriber/` and walk away → it never fires.
- **Event name strings are case-sensitive and unvalidated.** `'pim.employee_saved'` ≠ `'pim.employee_Saved'`. Always use the constant from `<Plugin>Events::` on both sides.
- **`$event->stopPropagation()` is sticky for the rest of the dispatch.** Subsequent subscribers for the same event won't run. Use sparingly — generally the producer shouldn't decide who else gets to react.
- **Multiple subscribers at the same priority have undefined order.** If order matters, use different priorities. Don't rely on file alphabetical order or registration order.
- **Dispatching an event with no listeners is silent and cheap.** Don't add events "just in case someone might want to listen later" — they're free, but each one is a class file someone has to maintain.
- **Listeners run synchronously and in-process.** A slow listener slows the request. For heavy work, fire the event but have the subscriber queue a job (see `mail` skill's queue pattern + `scheduled-jobs`).
- **Domain events fire only when the service dispatches them.** A direct DAO call (`$this->getEmployeeDao()->saveEmployee(...)` from somewhere other than `EmployeeService`) bypasses the event. Keep persistence-with-event-side-effects in the service layer.
