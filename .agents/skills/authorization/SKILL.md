---
name: authorization
description: Reference for OrangeHRM's authorization model — how REST endpoints and Vue/page controllers are gated by authentication, role-based screen/data-group permissions, and the marker interface that opts controllers out for pre-login routes. Use whenever the user is adding a new REST endpoint or page, making something public (login / forgot-password / version / captcha-style routes), debugging a 403 / "Unauthorized" / "Session expired" response, asking about user roles, data groups, screen permissions, the `self` flag, or `CapableViewController`. Covers both the runtime mechanism and the seeding patterns (the `permission/api.yaml` and `permission/screens.yaml` conventions). The actual seeding executes inside a database migration — migration mechanics are a separate concern (see the `migrations` skill), this skill includes only the minimal migration stub needed to land a permission change.
---

# Authorization in OrangeHRM

OrangeHRM has **one authorization mechanism applied at two layers**: REST endpoints (via *data groups*) and Vue/page controllers (via *screens*). Both layers use the same user-role table and the same marker-interface escape hatch for public routes. Get the shared model right first, then the path-specific details.

## Foundation (shared by both paths)

### The three-gate flow

Every request that reaches a controller passes through three Symfony event subscribers (all listening on `KernelEvents::CONTROLLER`):

| Priority | Subscriber | Question | On fail |
|---|---|---|---|
| 100000 | `AuthenticationSubscriber` | Logged in? User still active, not terminated, last-modified token still valid? | Pages: `SessionExpiredException` → 302 to `/auth/login`. REST: `UnauthorizedException` → 401 JSON. |
| 80000 | `ScreenAuthorizationSubscriber` | Does any of the user's effective roles have `can_read` on this module+screen? Then if controller is a `CapableViewController`, does `isCapable()` return true? | Forwards to `ForbiddenController` (a public Vue page rendering 403). |
| 80000 | `ApiAuthorizationSubscriber` | Does any of the user's effective roles have the CRUD bit matching the HTTP verb on this Endpoint's data group? | `ForbiddenException` → 403 JSON `{error:{status:403,message:"Unauthorized"}}`. |

The screen subscriber only acts when the controller is an `AbstractViewController` (i.e. a page). The API subscriber only acts when the controller is an `AbstractRestController`. The auth subscriber acts on all of them.

### The single switch: `PublicControllerInterface`

`OrangeHRM\Core\Controller\PublicControllerInterface` is an **empty marker interface**. Implementing it on a controller class makes all three subscribers `return` early. **This is the only mechanism for making a route public** — there is no per-route flag in `routes.yaml`, no config, no role called "anonymous". The class either implements it or doesn't.

### Effective user roles (computed per request)

`BasicUserRoleManager::computeUserRoles(User)` produces the role set authorization checks against:

```
roles = [user.userRole]                     // the static role from `users.user_role_id` (Admin or ESS)
roles += ESS                                // everyone is at least ESS
if isSupervisor(empNumber)         → +Supervisor
if isProjectAdmin(empNumber)       → +ProjectAdmin
if isHiringManager(empNumber)      → +HiringManager
if isInterviewer(empNumber)        → +Interviewer
if isTrackerReviewer(empNumber)    → +Reviewer
```

So an Admin who also supervises someone evaluates against `[Admin, ESS, Supervisor]`. Clients may add custom roles on top — those come from `users.user_role_id` like Admin/ESS, not from the dynamic checks.

### OR-merge semantics

When multiple roles match the same resource, their permissions are **OR-merged**: any role granting `read` = `read` granted. Most-permissive wins. There is no deny rule, no priority order — only union.

---

# Path A — REST endpoint authorization

## The model

```
Module ──┐
         ├── ApiPermission (api_name = Endpoint FQCN)
DataGroup┘                  table: ohrm_api_permission
  │                                                      ┌── UserRole
  └── DataGroupPermission (can_read/create/update/delete, self) ──┘
       table: ohrm_user_role_data_group
```

Entities (all in `src/plugins/orangehrmCorePlugin/entity/`):

- **`DataGroup`** (`ohrm_data_group`) — a permission *scope* (typical name `apiv2_<thing>`). Its own CRUD flags are the **capability ceiling** for the scope, not what any user gets.
- **`ApiPermission`** (`ohrm_api_permission`) — binds a `DataGroup` to a specific Endpoint by FQCN (`api_name`) within a `Module`. The route's `_api` attribute is the lookup key into this table.
- **`DataGroupPermission`** (`ohrm_user_role_data_group`) — the actual grant row: `(user_role_id, data_group_id, can_read, can_create, can_update, can_delete, self)`. **This is where the yes/no decision lives.**
- **`UserRole`** (`ohrm_user_role`) — Admin, ESS, Supervisor, ProjectAdmin, HiringManager, Interviewer, Reviewer, plus custom client roles.

## Runtime resolution

1. Symfony route resolves; `_api` attribute holds the Endpoint FQCN (set in `routes.yaml`).
2. `ApiAuthorizationSubscriber::onControllerEvent` reads `_api`.
3. `UserRoleManager::getApiPermissions($apiClass)` → `DataGroupService::getApiPermissions($apiClass, $userRoles)`.
4. SQL joins `ohrm_api_permission` → `ohrm_data_group` → `ohrm_user_role_data_group` filtered by the effective role IDs.
5. Every matching row is OR-merged into a single `ResourcePermission`.
6. HTTP verb → CRUD bit: `GET→canRead`, `POST→canCreate`, `PUT→canUpdate`, `DELETE→canDelete`.
7. Bit false → `ForbiddenException` → 403.

## The `self` flag

`self: true` does **not** restrict by itself. It means "this grant is conditional on the row belonging to the current user." **The Endpoint code must enforce the ownership check.** Typical pattern: read the data group permission with `getDataGroupPermissions(..., $selfPermission = true)`, and gate the operation on `$resourcePermission->isSelf() && $entityOwnedByCurrentUser`.

## Recipes

### Add an authenticated REST endpoint

1. **Endpoint class** in `src/plugins/orangehrm{X}Plugin/Api/`:
   ```php
   namespace OrangeHRM\X\Api;
   class WidgetAPI extends Endpoint implements CrudEndpoint { /* … */ }
   ```
2. **Route** in `src/plugins/orangehrm{X}Plugin/config/routes.yaml` pointing at the gated controller:
   ```yaml
   apiv2_x_widgets:
     path: /api/v2/x/widgets
     controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
     methods: [ GET, POST ]
     defaults:
       _api: OrangeHRM\X\Api\WidgetAPI
   ```
3. **Seed permissions** via a migration (see "Minimum viable migration stub" below). Drop a `permission/api.yaml`:
   ```yaml
   apiv2_x_widgets:
     description: 'X - Widgets'
     api: OrangeHRM\X\Api\WidgetAPI
     module: x                              # must match an ohrm_module.name
     allowed:                               # data-group capability ceiling
       read: true
       create: true
       update: true
       delete: true
     permissions:
       - { role: Admin,      permission: { read: true, create: true, update: true, delete: true } }
       - { role: ESS,        permission: { read: true, create: false, update: false, delete: false } }
       - { role: Supervisor, permission: { read: true, create: true, update: false, delete: false, self: true } }
   ```
   And in `Migration.php::up()`:
   ```php
   $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
   ```

That single helper call writes the `ohrm_data_group` row, the `ohrm_api_permission` row, and one `ohrm_user_role_data_group` row per `permissions:` entry.

### Add a public REST endpoint

The Endpoint class itself stays a normal `Endpoint` — **the marker goes on the front controller**, not on the Endpoint. Convention: put it in a `PublicApi/` directory so it's visually obvious.

1. **Endpoint** in `src/plugins/orangehrm{X}Plugin/PublicApi/` (just convention, the namespace doesn't matter to the framework).
2. **Route** points at the **public** generic controller:
   ```yaml
   apiv2_x_public_thing:
     path: /api/v2/x/public/thing
     controller: OrangeHRM\Core\Controller\Rest\V2\GenericPublicRestController::handle
     methods: [ POST ]
     defaults:
       _api: OrangeHRM\X\PublicApi\ThingAPI
   ```
3. **No permission rows.** `GenericPublicRestController extends GenericRestController implements PublicControllerInterface` — that's the entire file. All three gates skip it.

Examples in tree: `Authentication\PublicApi\PasswordStrengthValidationAPI`, the core version endpoint.

### Make an existing authenticated endpoint public

You can't just change the Endpoint class — every Endpoint that uses `GenericRestController` is authenticated. To make an endpoint public:
1. Change `controller:` in `routes.yaml` from `GenericRestController::handle` to `GenericPublicRestController::handle`.
2. Move the Endpoint into a `PublicApi/` subdir for consistency (optional but conventional).
3. In a migration, delete the `ohrm_api_permission` row (and any orphaned `ohrm_data_group` / `ohrm_user_role_data_group` rows). They're harmless if left behind — they just stop having any effect — but cleaning them up keeps the permission tables honest.

## Debugging a 403 on a REST call

Work through this list in order:

1. **Is the route hitting the right controller?** `grep -n '<path>' src/plugins/*/config/routes.yaml`. If you intended public, controller should be `GenericPublicRestController::handle`.
2. **Is `_api` set on the route?** `ApiAuthorizationSubscriber` throws immediately if not. Missing `_api` always returns 403 with body `_api parameter not defined in API routes`.
3. **Does `ohrm_api_permission` have a row for this exact FQCN?** Run `SELECT * FROM ohrm_api_permission WHERE api_name = 'OrangeHRM\\X\\Api\\WidgetAPI'`. (Note the doubled backslashes in SQL string literal.)
4. **Does `ohrm_user_role_data_group` have a row for the user's role × the data group?** Join: `SELECT ur.name, dgp.* FROM ohrm_user_role_data_group dgp JOIN ohrm_user_role ur ON ur.id = dgp.user_role_id JOIN ohrm_data_group dg ON dg.id = dgp.data_group_id WHERE dg.name = 'apiv2_x_widgets'`.
5. **Is the user's effective role what you think it is?** Don't just look at `users.user_role_id` — Supervisor / ProjectAdmin / HiringManager / Interviewer / Reviewer are *computed*, not stored. If the grant is on Supervisor and the user doesn't supervise anyone, they won't get it.
6. **Verb mismatch?** A row with `can_read=1, can_create=0` answers GET but not POST. The 403 looks identical either way.
7. **Self-scoped?** If `self=1`, the Endpoint must affirmatively check ownership. Forgetting that check returns 403 to the row's owner too.

---

# Path B — Screen / Vue page authorization

## The model

```
Module ──┐
         ├── Screen (action_url, e.g. "viewEmployeeList")
         │   table: ohrm_screen
         │   ├── ScreenPermission (can_read/create/update/delete) ── UserRole
         │   │   table: ohrm_user_role_screen
         │   └── (optionally) menu_configurator class for nav rendering
```

Entities:

- **`Screen`** (`ohrm_screen`) — one row per page. Identified by `(module_id, action_url)`. The `action_url` is the URL path segment (without the module prefix), e.g. `viewEmployeeList`, `viewSystemUsers`.
- **`ScreenPermission`** (`ohrm_user_role_screen`) — role × screen × CRUD. The CRUD bits exist on screens too, but practically only `can_read` is checked by `ScreenAuthorizationSubscriber` for page access; the others can be used by templates to conditionally render edit buttons.

## Runtime resolution

1. Symfony route resolves to an `AbstractViewController` (typically a subclass of `AbstractVueController`).
2. `ScreenAuthorizationSubscriber::onControllerEvent` reads the current module + screen from the request (via `ModuleScreenHelperTrait::getCurrentModuleAndScreen()`).
3. If `module == 'auth' && screen == 'logout'` → bypass (hardcoded; logout must always be reachable).
4. `UserRoleManager::getScreenPermissions($module, $screen)` → joins `ohrm_screen` → `ohrm_user_role_screen` for the user's effective roles → OR-merged `ResourcePermission`.
5. If `!canRead()` → `ForbiddenException` → forwards to `ForbiddenController` (a public Vue page).
6. If the controller implements `CapableViewController`, `isCapable($request)` is called *after* the screen check passes. Returning false → `ForbiddenException`.

## `CapableViewController` — runtime gating on top of static permissions

```php
namespace OrangeHRM\Core\Authorization\Controller;
interface CapableViewController { public function isCapable(Request $request): bool; }
```

Use when the static role-screen permission isn't expressive enough — for example:
- A screen that's only meaningful when a feature flag is on.
- A screen that depends on a specific employee state (terminated, on probation).
- A screen behind module-availability config (`ModuleNotAvailableSubscriber` handles module-level on/off, but `CapableViewController` is for finer conditions).

Return false → user sees 403 even though their role nominally has access.

There is **no equivalent on the REST side** — for APIs, throw `BadRequestException` / `ForbiddenException` from inside the Endpoint method when the row-level / runtime check fails.

## Recipes

### Add an authenticated page

1. **Controller** in `src/plugins/orangehrm{X}Plugin/Controller/`:
   ```php
   namespace OrangeHRM\X\Controller;
   use OrangeHRM\Core\Controller\AbstractVueController;
   use OrangeHRM\Core\Vue\Component;

   class WidgetListController extends AbstractVueController
   {
       public function preRender(Request $request): void
       {
           $component = new Component('widget-list');
           // optionally: $component->addProp(new Prop('foo', Prop::TYPE_NUMBER, 1));
           $this->setComponent($component);
       }
   }
   ```
2. **Route** in `routes.yaml`:
   ```yaml
   x_view_widget_list:
     path: /x/viewWidgetList
     controller: OrangeHRM\X\Controller\WidgetListController::handle
   ```
3. **Vue page component** at `src/client/src/orangehrm{X}Plugin/pages/widget-list/WidgetList.vue` and register it in `src/client/src/pages.ts`.
4. **Seed the screen + permissions** via a migration. Drop a `permission/screens.yaml`:
   ```yaml
   viewWidgetList:
     name: 'View Widget List'
     module: x
     url: viewWidgetList                # matches the action_url portion
     # menu_configurator: OrangeHRM\X\Menu\WidgetListConfigurator   # optional
     permissions:
       - { role: Admin, permission: { read: true, create: true, update: true, delete: true } }
       - { role: ESS,   permission: { read: true, create: false, update: false, delete: false } }
   ```
   And in `Migration.php::up()`:
   ```php
   $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screens.yaml');
   ```

### Add a public page (pre-login)

Just add the marker interface:

```php
class ForgotSomethingController extends AbstractVueController implements PublicControllerInterface
{
    public function preRender(Request $request): void { /* … */ }
}
```

**No `ohrm_screen` or `ohrm_user_role_screen` rows needed.** The route stays normal. Existing examples to copy from: `LoginController`, `RequestPasswordController`, `ResetPasswordController`, `WeakPasswordResetController`, `ForbiddenController`, `RootController`.

### Add a conditional page (role permission + runtime check)

Same as authenticated page, plus:

```php
use OrangeHRM\Core\Authorization\Controller\CapableViewController;

class TerminatedEmployeeReportController extends AbstractVueController implements CapableViewController
{
    public function isCapable(Request $request): bool
    {
        return $this->getConfigService()->isTerminationReportingEnabled();
    }
}
```

### Make an existing page public

1. Add `implements PublicControllerInterface` to the controller.
2. In a migration, delete the `ohrm_screen` row (and dependent `ohrm_user_role_screen` rows). Again — harmless if left, cleaner if removed.

## Debugging a 403 on a page

1. **Marker interface intended?** If the page should be reachable pre-login and isn't, check whether `implements PublicControllerInterface` is actually there.
2. **`ohrm_screen` row?** `SELECT s.*, m.name AS module FROM ohrm_screen s JOIN ohrm_module m ON m.id = s.module_id WHERE s.action_url = '<screen>'`.
3. **`ohrm_user_role_screen` row for the user's role × screen?** Join through.
4. **Effective role?** Same caveat as Path A — dynamic roles (Supervisor etc.) aren't in `users.user_role_id`.
5. **`CapableViewController::isCapable()` returning false?** Trace the implementation; common cause is a missing config row.
6. **Module disabled?** `ModuleNotAvailableSubscriber` short-circuits when the module is turned off in `ohrm_module.status`. This returns a different page (the disabled-module screen), not a 403, but the symptom of "page won't load" overlaps.
7. **Wrong subclass?** Only `AbstractViewController` subclasses get screen checks. A controller extending something else (e.g. `AbstractFileController`) bypasses screen authorization and is gated only by `AuthenticationSubscriber`.

---

# Where permission seeding actually runs

Both `permission/api.yaml` and `permission/screens.yaml` are consumed by `DataGroupHelper` methods called from **a migration's `up()`**. The migration mechanics — `AbstractMigration` base class, the `MIGRATIONS_MAP` registry, version range execution, the `migration:up` dev command for iterating — belong to the **`migrations` skill**. This skill includes only the minimum stub a permission-only change needs.

## Minimum viable migration stub for a permission-only change

When the next version is `5.9.0`:

```php
<?php
// installer/Migration/V5_9_0/Migration.php
namespace OrangeHRM\Installer\Migration\V5_9_0;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    public function up(): void
    {
        $this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screens.yaml');
    }

    public function getVersion(): string
    {
        return '5.9.0';
    }
}
```

Plus the version must be registered in `installer/Util/AppSetupUtility.php::MIGRATIONS_MAP`:

```php
'5.9' => \OrangeHRM\Installer\Migration\V5_9_0\Migration::class,
```

That's the entire migration footprint for a permission change. **For iterating during development**, run it directly without a full reinstall:

```bash
php devTools/core/console.php migration:up "\OrangeHRM\Installer\Migration\V5_9_0\Migration"
```

For anything beyond this — schema changes, conditional column edits, lang strings, multi-step versions, recovering from a half-applied migration — see the `migrations` skill.

## During development without a migration yet

If you're prototyping and don't want to write a migration immediately, two dev commands persist directly to the local DB and print the equivalent SQL for later promotion to a migration:

```bash
php devTools/core/console.php add-data-group        # data group + (optional) ApiPermission row
php devTools/core/console.php add-role-permission   # DataGroupPermission row
```

Don't ship a feature this way — always land the YAML + migration. These commands exist for fast local iteration.

---

# Quick reference — common tasks

## Add a new authenticated REST endpoint
- [ ] Create `Api/<Name>API.php` extending `Endpoint` + relevant CRUD interface
- [ ] Add route to plugin's `config/routes.yaml` pointing at `GenericRestController::handle` with `_api` set to FQCN
- [ ] Create `permission/api.yaml` entry: `api`, `module`, `allowed` (capability ceiling), `permissions` (role × CRUD, `self` if row-scoped)
- [ ] In a migration `up()`: `$this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml');`
- [ ] Register migration version in `AppSetupUtility::MIGRATIONS_MAP`
- [ ] If `self: true` on any role, enforce ownership check inside the Endpoint method
- [ ] Locally: `php devTools/core/console.php migration:up "\…\Migration"` to apply without reinstall

## Add a new public REST endpoint
- [ ] Create `PublicApi/<Name>API.php` extending `Endpoint`
- [ ] Add route pointing at `GenericPublicRestController::handle` with `_api` set to FQCN
- [ ] **No permission rows, no migration needed**

## Add a new authenticated page
- [ ] Create controller extending `AbstractVueController`; set `Component` in `preRender()`
- [ ] Add route to `routes.yaml`
- [ ] Add the Vue component and register it in `src/client/src/pages.ts`
- [ ] Create `permission/screens.yaml` entry: `name`, `module`, `url`, optionally `menu_configurator`, plus per-role `permissions`
- [ ] In a migration `up()`: `$this->getDataGroupHelper()->insertScreenPermissions(…);`
- [ ] Register migration version in `MIGRATIONS_MAP`

## Add a new public page (pre-login)
- [ ] Controller extends `AbstractVueController` **and** `implements PublicControllerInterface`
- [ ] Normal route
- [ ] Vue component + `pages.ts` registration
- [ ] **No `ohrm_screen` row, no migration needed**

## Make an existing endpoint or page public
- [ ] **API**: swap `controller:` in route to `GenericPublicRestController::handle`; move Endpoint to `PublicApi/`; (optional) migration to drop the old permission rows
- [ ] **Page**: add `implements PublicControllerInterface` to the controller; (optional) migration to drop the old `ohrm_screen` row

## Add a runtime condition on top of role permissions (pages only)
- [ ] Controller `implements CapableViewController`; implement `isCapable(Request): bool`
- [ ] No DB changes — purely code-level gating

## Debug an unexpected 403 (REST)
- [ ] Route uses `GenericRestController` (gated) — confirm intent
- [ ] `_api` attribute set in route defaults
- [ ] `ohrm_api_permission` row exists for the Endpoint FQCN
- [ ] `ohrm_user_role_data_group` row for user's effective role × the data group, with the CRUD bit for the HTTP verb
- [ ] If `self=1`, ownership check enforced inside the Endpoint
- [ ] Effective role includes dynamically-derived roles (Supervisor / ProjectAdmin / HiringManager / Interviewer / Reviewer) — not just `users.user_role_id`

## Debug an unexpected 403 (page)
- [ ] Controller implements `PublicControllerInterface` if it should be public
- [ ] `ohrm_screen` row exists for the module + action_url
- [ ] `ohrm_user_role_screen` row for the user's effective role, with `can_read=1`
- [ ] If `CapableViewController` — `isCapable()` returns true
- [ ] Effective role caveat applies (same as REST)
- [ ] Module is not disabled in `ohrm_module.status`
