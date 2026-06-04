---
name: rest-endpoints
description: Entry-point reference for adding or editing OrangeHRM REST API v2 endpoints — the request lifecycle (route → GenericRestController → Endpoint → response), picking the right Endpoint interface (Resource / Collection / Crud), routes.yaml conventions, reading params via RequestParams, sorting/paging/filtering via FilterParams, exception classes + their HTTP status mapping, and access enforcement inside the handler. Use whenever the user is creating a new REST endpoint, editing an existing one, working on routes.yaml for an API, debugging a 4xx/5xx response, or generally asking how OrangeHRM's REST layer is wired. Cross-references three sibling skills for depth: `rest-validation` (request validation rules), `rest-serialization` (response models & shape), `rest-openapi` (swagger-php annotations CI requires).
---

# REST endpoints — the spine

All OrangeHRM REST APIs live under `/api/v2/` and follow a single dispatch pattern: a route in `routes.yaml` points at `GenericRestController::handle`, the controller looks up the FQCN in the route's `_api` attribute, instantiates that Endpoint class, validates the incoming params against the verb-specific rule collection, then dispatches to the matching CRUD method.

**This skill covers the spine.** For details, see:
- **Request validation rules** → `rest-validation` skill
- **Response shaping (Models, results)** → `rest-serialization` skill
- **OpenAPI annotations CI requires** → `rest-openapi` skill
- **Permission gating + public endpoints** → `authorization` skill

## Request lifecycle

```
HTTP request
  ↓ Symfony routing
routes.yaml entry  →  GenericRestController::handle (or GenericPublicRestController for public APIs)
  ↓ KernelEvents::CONTROLLER subscribers
AuthenticationSubscriber       (priority 100000)  — gate 1: logged in?
ApiAuthorizationSubscriber     (priority 80000)   — gate 2: role has CRUD bit for this Endpoint's data group?
  ↓
GenericRestController::handle
  ↓
  1. Read `_api` route attribute → instantiate <YourAPI> extends Endpoint
  2. Look up validation rule for HTTP verb (getValidationRuleForGetAll, ForCreate, ForGetOne, ForUpdate, ForDelete)
  3. Validator::validate($allParams, $rules)  → throws InvalidParamException on failure
  4. Dispatch to handler:
     GET  + no `_key` → getAll()
     GET  + `_key`    → getOne()
     POST             → create()
     PUT              → update()
     DELETE           → delete()
  5. Handler returns EndpointResult (Resource or Collection)
  6. Result → Response::formatData() → JSON `{data:..., meta:..., rels:...}`
```

Auth gates are covered in the `authorization` skill. Steps 1–6 are what this skill (and the sibling skills) document.

## Pick the right Endpoint interface

Endpoints all extend `OrangeHRM\Core\Api\V2\Endpoint` and implement **one** of three interfaces depending on which operations they support:

| Interface | Methods required | When to use |
|---|---|---|
| `CollectionEndpoint` | `getAll`, `create`, `delete` (+ their `getValidationRuleFor*`) | List/create/bulk-delete on a collection. URL pattern: `/api/v2/.../resources` (no `{id}`). |
| `ResourceEndpoint` | `getOne`, `update`, `delete` (+ their `getValidationRuleFor*`) | Read/update/delete a single resource. URL pattern: `/api/v2/.../resources/{id}`. |
| `CrudEndpoint` | All of the above (extends both) | Same Endpoint class handles both the collection and resource URLs — most common case. |

If your endpoint only supports a subset (e.g. read-only), still implement the full interface and `throw $this->getNotImplementedException()` from the unsupported methods. The framework dispatches by HTTP verb and a missing method becomes a 500, not a 405.

```php
namespace OrangeHRM\X\Api;

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;

class WidgetAPI extends Endpoint implements CrudEndpoint
{
    // getOne, getAll, create, update, delete + 5 getValidationRuleFor*() methods
}
```

The `__construct(Request)` is provided by the base class; **don't override it**. Use the `init()` hook for setup that needs to run after the request is bound:

```php
protected function init() { /* … */ }
```

## `routes.yaml` conventions

Routes live in `src/plugins/orangehrm{Plugin}Plugin/config/routes.yaml`. All authenticated REST routes share this shape:

```yaml
apiv2_pim_employees:                                                    # route name: apiv2_<plugin>_<resource>
  path: /api/v2/pim/employees
  controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
  methods: [ GET, POST, DELETE ]                                        # which HTTP verbs this route accepts
  defaults:
    _api: OrangeHRM\Pim\Api\EmployeeAPI                                 # FQCN of the Endpoint class
```

For a single-resource route with a URL placeholder:

```yaml
apiv2_pim_employee:
  path: /api/v2/pim/employees/{empNumber}
  controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
  methods: [ GET, PUT ]
  defaults:
    _api: OrangeHRM\Pim\Api\EmployeeAPI
    _key: empNumber                                                     # tells dispatcher "this is getOne, not getAll"
  requirements:
    empNumber: '\d+'                                                    # restrict to digits
```

Key conventions:
- **Route name** = `apiv2_<plugin>_<resource>[_<qualifier>]`. e.g. `apiv2_pim_employees`, `apiv2_pim_employees_count`, `apiv2_admin_user`.
- **Path** = `/api/v2/<module>/<resource>` for the collection, `/api/v2/<module>/<resource>/{key}` for the resource.
- **`_api`** is required. Without it, `GenericRestController` throws (becomes a 500).
- **`_key`** signals "this is the resource route, dispatch GET to `getOne()` not `getAll()`". The string is the attribute key — the value is what's bound to `PARAM_TYPE_ATTRIBUTE` under that key.
- **`requirements`** apply regex constraints to path params (`'\d+'` keeps non-numeric URLs from matching).
- **`methods`** restricts which HTTP verbs hit this route. The same Endpoint class typically backs both the collection route (`[GET, POST, DELETE]`) and the resource route (`[GET, PUT]`), but you can also expose a read-only resource by setting `methods: [ GET ]`.

**Public endpoints** swap the front controller to `GenericPublicRestController::handle` (and put the Endpoint in `PublicApi/` by convention). See the `authorization` skill.

## Reading request parameters

`RequestParams` is exposed via `$this->getRequestParams()`. Every accessor takes `(string $type, string $key, $default = …)`:

```php
RequestParams::PARAM_TYPE_BODY        // POST/PUT JSON body
RequestParams::PARAM_TYPE_QUERY       // ?foo=bar query string
RequestParams::PARAM_TYPE_ATTRIBUTE   // URL placeholders ({empNumber}) and `_key`-bound values
```

Typed accessors:

| Method | Returns |
|---|---|
| `getString`, `getStringOrNull` | `string` (default `""`) |
| `getInt`, `getIntOrNull` | `int` (default `0`) |
| `getFloat`, `getFloatOrNull` | `float` (default `0`) |
| `getBoolean`, `getBooleanOrNull` | `bool` (default `false`) |
| `getArray`, `getArrayOrNull` | `array` (default `[]`) |
| `getDateTime`, `getDateTimeOrNull` | `\DateTime` — extra args `?DateTimeZone $timezone, ?DateTime $default` |
| `getAttachment`, `getAttachmentOrNull` | `Base64Attachment` DTO (used for file uploads) |
| `has(type, key)` | `bool` — was the param sent? |

**Always use `*OrNull` variants for optional params** — the non-null versions return `0`/`""`/`false` for missing inputs, which is rarely what you want when you'll be passing the result into a DTO setter.

```php
$empNumber = $this->getRequestParams()->getInt(
    RequestParams::PARAM_TYPE_ATTRIBUTE,
    CommonParams::PARAMETER_EMP_NUMBER
);

$nameFilter = $this->getRequestParams()->getStringOrNull(
    RequestParams::PARAM_TYPE_QUERY,
    self::FILTER_NAME
);

$picture = $this->getRequestParams()->getAttachment(
    RequestParams::PARAM_TYPE_BODY,
    self::PARAMETER_EMP_PICTURE
);
```

`CommonParams` (`OrangeHRM\Core\Api\CommonParams`) holds the framework-standard param keys: `PARAMETER_ID`, `PARAMETER_IDS`, `PARAMETER_EMP_NUMBER`, `PARAMETER_SORT_FIELD`, `PARAMETER_SORT_ORDER`, `PARAMETER_OFFSET`, `PARAMETER_LIMIT`, `PARAMETER_TOTAL`. Use these constants instead of stringly-typed literals.

## Sorting, paging, filtering — the FilterParams pattern

`OrangeHRM\Core\Dto\FilterParams` is the base for resource-specific search DTOs. It holds `sortField`, `sortOrder` (default `ASC`), `limit` (default `50`), `offset` (default `0`). Each plugin extends it for its own filters — e.g. `EmployeeSearchFilterParams` adds `name`, `nameOrId`, `empStatusId`, `jobTitleId`, etc.

By convention the DTO subclass declares:
- `const ALLOWED_SORT_FIELDS = [...]` — whitelist of column references the validator allows
- enum-like maps where applicable (e.g. `INCLUDE_EMPLOYEES_MAP`)

The Endpoint base class provides two helpers that wire the DTO to the request:

```php
public function getAll(): EndpointCollectionResult
{
    $params = new EmployeeSearchFilterParams();
    $this->setSortingAndPaginationParams($params);                    // binds sortField/sortOrder/limit/offset from query

    $params->setName(
        $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_QUERY, self::FILTER_NAME)
    );
    // … set other resource-specific filters from query …

    $employees = $this->getEmployeeService()->getEmployeeList($params);
    $count     = $this->getEmployeeService()->getEmployeeCount($params);

    return new EndpointCollectionResult(
        EmployeeModel::class,
        $employees,
        new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])   // standard meta shape
    );
}

public function getValidationRuleForGetAll(): ParamRuleCollection
{
    return new ParamRuleCollection(
        new ParamRule(self::FILTER_NAME, new Rule(Rules::STRING_TYPE)),
        // … resource-specific filter rules …
        ...$this->getSortingAndPaginationParamsRules(                 // standard sort/page rules
            EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS
        ),
    );
}
```

`getSortingAndPaginationParamsRules($allowedSortFields, $excludeSortField = false)` returns 4 ParamRules covering `sortField` (must be in `$allowedSortFields`), `sortOrder` (must be `ASC` or `DESC`), `limit` and `offset` (zero or positive). Don't reinvent these in each Endpoint.

## Errors — exception classes + their status codes

All in `OrangeHRM\Core\Api\V2\Exception`:

| Exception | HTTP status | When |
|---|---|---|
| `BadRequestException` | 400 | Client error not covered by validation (e.g. business-rule violation) |
| `InvalidParamException` | 422 | Raised by `Validator` — never throw this manually, let validation rules catch issues |
| `ForbiddenException` | 403 | The user lacks a permission this Endpoint enforces *itself* (row-level / self-scoped checks) |
| `RecordNotFoundException` | 404 | Requested entity / row doesn't exist |
| `NotImplementedException` | 501 | Operation deliberately unsupported (use in stubbed CRUD methods) |

`Endpoint` uses `EndpointExceptionTrait`, which gives every endpoint these helpers:

```php
$this->getBadRequestException('Optional message');
$this->getForbiddenException('Optional message');
$this->getRecordNotFoundException('Optional message');
$this->getNotImplementedException('Optional message');
$this->getInvalidParamException($paramKey, ?$message);

// Common one-liners:
$this->throwRecordNotFoundExceptionIfNotExist($entity, Employee::class);
$this->throwRecordNotFoundExceptionIfEmptyIds($ids);
```

The typical pattern after fetching from the DAO:

```php
$employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
$this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);
```

## Access enforcement inside the handler

The gate-level auth (`ApiAuthorizationSubscriber`) only checks "does this user's role have CRUD on this Endpoint's data group?" — it doesn't filter rows or enforce ownership. **That's the Endpoint's responsibility.** See `authorization` skill for the full model; here are the three patterns you'll use inside handlers:

### 1. Restrict list queries to accessible entities

```php
use OrangeHRM\Core\Traits\UserRoleManagerTrait;

class WidgetAPI extends Endpoint implements CrudEndpoint {
    use UserRoleManagerTrait;

    public function getAll(): EndpointCollectionResult {
        $params = new WidgetSearchFilterParams();
        $this->setSortingAndPaginationParams($params);

        $accessible = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        $params->setEmployeeNumbers($accessible);
        // … fetch + return
    }
}
```

### 2. Reject inputs naming an inaccessible entity (validation-level)

The `IN_ACCESSIBLE_EMP_NUMBERS` rule does the lookup against the role manager. Apply it in the validation rule collection:

```php
new ParamRule(
    CommonParams::PARAMETER_EMP_NUMBER,
    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
)
```

This produces a 422 (validation failure) rather than a 403 — preferable, because it tells the client *which* param was bad.

### 3. Enforce `self` flag (row ownership)

When the permission grant has `self: true`, the Endpoint must verify the row belongs to the current user:

```php
use OrangeHRM\Core\Authorization\Helper\UserRoleManagerHelper;

if (!$this->getUserRoleManager()->isEntityAccessible(Employee::class, $empNumber, /* op */ 'view')) {
    throw $this->getForbiddenException();
}
// or:
if (!(new UserRoleManagerHelper())->isSelfByEmpNumber($empNumber)) {
    throw $this->getForbiddenException();
}
```

Forgetting this check means `self: true` users can act on *any* row their role data-group permits at all, not just their own.

---

# Recipes

## Recipe 1 — Full CRUD endpoint

```php
<?php
namespace OrangeHRM\X\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Widget;
use OrangeHRM\X\Api\Model\WidgetModel;
use OrangeHRM\X\Dto\WidgetSearchFilterParams;
use OrangeHRM\X\Traits\Service\WidgetServiceTrait;

class WidgetAPI extends Endpoint implements CrudEndpoint
{
    use WidgetServiceTrait;

    public const PARAMETER_NAME = 'name';
    public const PARAM_RULE_NAME_MAX_LENGTH = 100;

    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $widget = $this->getWidgetService()->getWidgetById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($widget, Widget::class);
        return new EndpointResourceResult(WidgetModel::class, $widget);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
        );
    }

    public function getAll(): EndpointCollectionResult
    {
        $params = new WidgetSearchFilterParams();
        $this->setSortingAndPaginationParams($params);
        $widgets = $this->getWidgetService()->getWidgetList($params);
        $count   = $this->getWidgetService()->getWidgetCount($params);
        return new EndpointCollectionResult(
            WidgetModel::class,
            $widgets,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getSortingAndPaginationParamsRules(WidgetSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResourceResult
    {
        $widget = new Widget();
        $widget->setName($this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME));
        $this->getWidgetService()->saveWidget($widget);
        return new EndpointResourceResult(WidgetModel::class, $widget);
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH])
                )
            ),
        );
    }

    public function update(): EndpointResourceResult { /* getOne + apply body + save */ }
    public function getValidationRuleForUpdate(): ParamRuleCollection { /* getOne rules + create rules */ }

    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getWidgetService()->deleteWidgets($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_IDS, new Rule(Rules::INT_ARRAY)),
        );
    }
}
```

Plus routes (collection + resource):

```yaml
apiv2_x_widgets:
  path: /api/v2/x/widgets
  controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
  methods: [ GET, POST, DELETE ]
  defaults: { _api: OrangeHRM\X\Api\WidgetAPI }

apiv2_x_widget:
  path: /api/v2/x/widgets/{id}
  controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
  methods: [ GET, PUT ]
  defaults: { _api: OrangeHRM\X\Api\WidgetAPI, _key: id }
  requirements: { id: '\d+' }
```

Permissions: seed via `permission/api.yaml` in a migration — see the `authorization` skill.

## Recipe 2 — File upload (Base64Attachment)

```php
public function update(): EndpointResourceResult
{
    $empNumber = $this->getRequestParams()->getInt(
        RequestParams::PARAM_TYPE_ATTRIBUTE,
        CommonParams::PARAMETER_EMP_NUMBER
    );

    $attachment = $this->getRequestParams()->getAttachment(
        RequestParams::PARAM_TYPE_BODY,
        self::PARAMETER_EMP_PICTURE
    );
    // $attachment is a Base64Attachment with: getFilename(), getFileType(), getSize(), getContent()

    // … persist using the attachment fields …
    return new EndpointResourceResult(EmployeePictureModel::class, $empPicture);
}

public function getValidationRuleForUpdate(): ParamRuleCollection
{
    return new ParamRuleCollection(
        new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)),
        new ParamRule(
            self::PARAMETER_EMP_PICTURE,
            new Rule(Rules::BASE_64_ATTACHMENT, [
                EmpPicture::ALLOWED_IMAGE_TYPES,
                EmpPicture::ALLOWED_IMAGE_EXTENSIONS,
                self::PARAM_RULE_EMP_PICTURE_FILE_NAME_MAX_LENGTH,
            ])
        ),
    );
}
```

Client sends `{ empPicture: { name, type, base64 } }` — the framework decodes it for you.

## Recipe 3 — "MyInfo" / self-scoped endpoint

When the operation is "act on the current user's row, with no `{empNumber}` in the URL":

```yaml
apiv2_pim_myinfo:
  path: /api/v2/pim/myself
  controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
  methods: [ GET ]
  defaults: { _api: OrangeHRM\Pim\Api\MyInfoAPI, _key: myself }   # _key triggers getOne()
```

Inside the handler, resolve the current user via the auth user trait rather than a URL param:

```php
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;

class MyInfoAPI extends Endpoint implements ResourceEndpoint {
    use AuthUserTrait;

    public function getOne(): EndpointResult {
        $empNumber = $this->getAuthUser()->getEmpNumber();
        // … fetch and return
    }
}
```

## Recipe 4 — Validation-only endpoint

Pattern used for asynchronous "is this email in use?" / "is this username available?" checks:

```php
class ValidationEmployeeEmailAPI extends Endpoint implements ResourceEndpoint
{
    public function getOne(): EndpointResult
    {
        // The rule already ran during validation; reaching here means it's valid OR the validator runs the lookup itself.
        // Common pattern: just check uniqueness and return {valid: true|false}
        $isUnique = $this->getEmployeeService()->isUniqueEmail(...);
        return new EndpointResourceResult(ArrayModel::class, ['valid' => $isUnique]);
    }
    // … delete/update throw NotImplementedException
}
```

See the `rest-validation` skill for the `ENTITY_UNIQUE_PROPERTY` rule, which often makes this pattern unnecessary (do the uniqueness check in the *create* rule directly).

---

# Checklists

## Add a new authenticated REST endpoint

- [ ] Create `src/plugins/orangehrm{X}Plugin/Api/<Name>API.php` extending `Endpoint` + appropriate interface (`CrudEndpoint` / `ResourceEndpoint` / `CollectionEndpoint`)
- [ ] Implement the verb methods + their `getValidationRuleFor*()` (throw `getNotImplementedException()` for unsupported verbs)
- [ ] Add route(s) to plugin's `config/routes.yaml` — collection without `_key`, resource with `_key` + `requirements`
- [ ] Create a Model class (see `rest-serialization` skill)
- [ ] Add validation rules (see `rest-validation` skill)
- [ ] Add OpenAPI annotations on the handler methods (see `rest-openapi` skill)
- [ ] Seed permissions in a migration: `permission/api.yaml` (see `authorization` skill)
- [ ] For list endpoints: extend `FilterParams` with `ALLOWED_SORT_FIELDS`, use `setSortingAndPaginationParams` + `getSortingAndPaginationParamsRules`
- [ ] For self-scoped operations: enforce ownership in the handler — gate-level auth alone is insufficient

## Debug an unexpected response

| Symptom | First places to look |
|---|---|
| 401 Unauthorized | Auth — see `authorization` skill |
| 403 Forbidden (REST) | Permissions — see `authorization` skill |
| 404 Not Found | (a) Route not registered (check `routes.yaml`, methods, requirements regex) or (b) `throwRecordNotFoundExceptionIfNotExist` fired |
| 422 Invalid params | Validation — see `rest-validation` skill. Specific cause: response body's `error.message` shows the offending param key |
| 500 | (a) `_api` missing/typo in route `defaults` (b) Endpoint doesn't implement the interface for the verb dispatched (c) Exception thrown inside the handler — check `src/log/orangehrm.log` |
| 501 Not Implemented | `getNotImplementedException()` thrown — operation deliberately unsupported |
| Wrong handler invoked (getAll instead of getOne, etc.) | `_key` attribute missing from resource route, or `requirements` not matching the input |

## Things that bite

- **The validator runs before the handler.** Don't put side effects in `getValidationRuleFor*()` — return the rule collection only. If you need to look up state for a check, write a custom Rule class (see `rest-validation`).
- **`getInt` / `getString` return zero values for missing keys** (`0`, `""`, `false`, `[]`). Use the `*OrNull` variant for optional params or your code will silently treat absence as "explicit zero."
- **`_key` is what distinguishes `getOne` from `getAll`** — if your `/{id}` route accidentally lacks `_key`, the framework routes to `getAll()` and the URL param is ignored.
- **`ParamRuleCollection` is strict by default** — every param sent has to have a matching rule. Sending `?debug=1` to an endpoint without a `debug` rule produces a 422 with "Unexpected Parameter `debug` Received".
- **Forgetting `throwRecordNotFoundExceptionIfNotExist`** after a DAO fetch produces a 500 (null pointer) instead of a 404. The helper is one line; use it.
- **The same Endpoint class handles both collection and resource routes** when implementing `CrudEndpoint`. Both routes point at the same FQCN; `_key` decides which CRUD method runs for GET.
- **Permissions are seeded in a migration, not by code.** Adding an Endpoint without the migration entry means it always returns 403 to non-Admin users (and possibly to Admin too, depending on the default data-group state).
