---
name: rest-serialization
description: Reference for shaping OrangeHRM REST API v2 responses — the `Normalizable` + `ModelTrait` contract, writing a Model class that maps a Doctrine entity to a JSON object, the `filter` array (getter chain — supports nested-getter sequences for related entities), the `attributeNames` array (output JSON keys), `EndpointResourceResult` vs `EndpointCollectionResult`, the `ParameterBag` meta envelope, generic non-entity models (`ArrayModel`, `ArrayCollectionModel`), and the `MODEL_MAP` pattern for `?model=default|detailed` variants. Use whenever the user is writing or editing a Model class, picking which `EndpointResult` to return, debugging "why is field X missing/null in the JSON response", adding a detailed-vs-default model variant, or returning an ad-hoc payload that doesn't come from an entity. Companion to `rest-endpoints` (the dispatch spine) and `rest-openapi` (Model classes carry `@OA\Schema`).
---

# REST API v2 response shaping

Every Endpoint handler returns an `EndpointResult` — a (modelClass, data, meta) triple. The framework's normalizer instantiates the Model with the data, calls `toArray()`, wraps it in `{data: ..., meta: {...}, rels: {...}}`, and returns JSON. The Model class is where you decide which entity fields appear in the response and what JSON keys they go under.

```
Endpoint handler
  └── returns new EndpointResourceResult(WidgetModel::class, $widgetEntity, ?$meta)
       └── framework: $model = new WidgetModel($widgetEntity)
            └── $model->toArray()                  ← ModelTrait does the heavy lifting
                 └── walks `filter` array, builds `attributeNames`-keyed output
       └── Response::formatData()
            └── {"data": {...model output...}, "meta": {...}, "rels": {...}}
```

This skill covers everything from "what to return from the handler" through "how the JSON envelope is built." For the dispatch flow into the handler, see `rest-endpoints`. For the `@OA\Schema` annotation that documents the Model, see `rest-openapi`.

## The `Normalizable` contract

A Model is any class implementing `OrangeHRM\Core\Api\V2\Serializer\Normalizable`:

```php
interface Normalizable
{
    public function toArray(): array;
}
```

You almost never implement this directly. The standard pattern is to `use ModelTrait` and wire an entity in the constructor.

## `ModelTrait` — the standard pattern

`ModelTrait` gives a Normalizable class three setters and an implementation of `toArray()`:

```php
$this->setEntity($entity);             // the Doctrine entity (or any object with getters)
$this->setFilters([...]);              // which fields to include, as a getter chain
$this->setAttributeNames([...]);       // the JSON output key for each filter entry
```

`toArray()` walks the `filter` array; for each entry, it derives a getter (string → `get<UcfirstAttr>()`), invokes it on the entity, and stores the result under the matching `attributeNames[$index]` key (or under `filter[$index]` itself if no override).

### The basic shape

```php
namespace OrangeHRM\X\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Widget;

class WidgetModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Widget $widget)
    {
        $this->setEntity($widget);
        $this->setFilters(['id', 'name', 'description']);
        // Attribute names default to filter strings when omitted — these would be {id, name, description}
    }
}
```

The framework calls `getId()`, `getName()`, `getDescription()` on the entity (camel-cased + `get` prefix). Output:

```json
{ "id": 1, "name": "Acme", "description": "…" }
```

### Renaming output keys

If JSON key shouldn't match the getter name (e.g. you want `lastName` in JSON but the getter is `getLast()`), provide `attributeNames`:

```php
$this->setFilters(['id', 'last', 'first', 'middle']);
$this->setAttributeNames(['id', 'lastName', 'firstName', 'middleName']);
```

The two arrays are **positional** — index 0 of `attributeNames` matches index 0 of `filter`. Mismatch in length silently produces shifted output, so keep them aligned.

### Nested getters — descending into related entities

When the value you want lives on a related entity, pass a sub-array of getter names instead of a string. `ModelTrait` walks the chain, calling each method on the previous result.

```php
$this->setFilters([
    'empNumber',
    'lastName',
    'firstName',
    'middleName',
    'employeeId',
    ['getEmployeeTerminationRecord', 'getId'],  // $employee->getEmployeeTerminationRecord()->getId()
]);
$this->setAttributeNames([
    'empNumber',
    'lastName',
    'firstName',
    'middleName',
    'employeeId',
    'terminationId',                            // the JSON key for the nested chain
]);
```

Output:
```json
{
  "empNumber": 1,
  "lastName": "…",
  ...,
  "terminationId": 42                          // null if no termination record
}
```

**Null-safety is automatic at each link.** `ModelTrait::toArray()` checks `is_null($value)` before each step — if `getEmployeeTerminationRecord()` returns null, the chain short-circuits and the output value is null (without calling `getId()` on null). This is convenient but masks errors: if you typo a getter name, the chain silently produces null instead of throwing.

### Producing nested JSON objects

If you want a nested JSON object instead of a flat key, provide a sub-array under `attributeNames` for that index:

```php
$this->setFilters([
    'id',
    ['getJobTitle', 'getId'],
    ['getJobTitle', 'getJobTitleName'],
]);
$this->setAttributeNames([
    'id',
    ['jobTitle', 'id'],          // nested: { "jobTitle": { "id": … } }
    ['jobTitle', 'name'],        // merged: { "jobTitle": { "id": …, "name": … } }
]);
```

`ModelTrait::makeNestedArray()` builds the nested structure; `array_merge_recursive` combines the two entries into one `jobTitle` object. Use this when the response should surface a sub-resource rather than flat scalars.

### Collections within a model

If a step in the getter chain returns an iterable (e.g. `getSkills()` returns `Collection<EmployeeSkill>`), the trait switches to collection mode: the remaining getter names are applied to *each* element, and the result is an array under the model's output key.

```php
$this->setFilters([
    'id',
    ['getSkills', ['getName', 'getYears']],
]);
$this->setAttributeNames([
    'id',
    ['skills', ['name', 'years']],
]);
```

Output:
```json
{ "id": 1, "skills": [{"name": "PHP", "years": 5}, {"name": "Vue", "years": 3}] }
```

The trait detects the iterable at any point in the chain and switches mode automatically.

## Picking the result class

| Result | When |
|---|---|
| `EndpointResourceResult($modelClass, $entity, ?$meta, ?$rels)` | Single resource (getOne, create, update). `$entity` is one object — the framework instantiates the Model with it. |
| `EndpointCollectionResult($modelClass, $entities, ?$meta, ?$rels)` | Collection (getAll). `$entities` is an array of objects — the framework instantiates the Model once per element. |

`EndpointResourceResult` is also used for **delete** responses (which echo back the deleted IDs) and **MyInfo-style** endpoints. The interface is the same shape; the result type is the only difference between one-vs-many output.

```php
// Single
return new EndpointResourceResult(WidgetModel::class, $widget);

// Collection with meta total
return new EndpointCollectionResult(
    WidgetModel::class,
    $widgets,
    new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
);
```

The constructor signature is `(string $modelClass, $data, ?ParameterBag $meta = null, ?ParameterBag $rels = null)`. `$rels` is rarely used in this codebase — it would surface as a `rels` key in the envelope.

## `ParameterBag` for meta

`OrangeHRM\Core\Api\V2\ParameterBag` (extends Symfony's `ParameterBag`) holds the response's `meta` object. The framework always emits a `meta` key — pass an empty `ParameterBag` or `null` for `{ "meta": {} }`.

The single project-wide convention: list endpoints include `total`:

```php
new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
// → {"meta": {"total": 137}}
```

Some endpoints add more keys (e.g. employee-list reports add `employeeIds` or filter-summary data). Always use `CommonParams::PARAMETER_*` constants when the key is a framework-standard one.

## The response envelope

`Response::formatData()` always produces:

```json
{
  "data": { ... model output ... },     // or [...] for collection
  "meta": { ... },
  "rels": { ... }
}
```

`meta` and `rels` default to `{}` when not set. Errors are a separate shape produced by `Response::formatError()` — `{ "error": { "status": "...", "message": "..." } }`.

## Generic models (non-entity payloads)

When your response isn't backed by a Doctrine entity, use the generic models instead of inventing an entity:

### `ArrayModel($data)`

Single response. Pass an associative array; that array becomes the `data` key verbatim.

```php
return new EndpointResourceResult(ArrayModel::class, ['valid' => true]);
// → {"data": {"valid": true}, "meta": {}, ...}
```

Common uses: validation-only endpoints (`{valid: true}`), delete responses (echoing back the IDs), single-field reads.

### `ArrayCollectionModel($data)`

Collection. Pass a plain array; becomes `data` as a JSON array.

```php
return new EndpointCollectionResult(ArrayCollectionModel::class, [1, 2, 3]);
// → {"data": [1, 2, 3], "meta": {}, ...}
```

Used when "the list" doesn't map to entities — version constants, computed aggregates, etc.

## The `MODEL_MAP` pattern — `?model=default|detailed`

For endpoints where consumers want either a slim or a rich version of the same resource, declare a `MODEL_MAP` constant and switch on a `?model=` query param. `EmployeeAPI` is the canonical example:

```php
class EmployeeAPI extends Endpoint implements CrudEndpoint
{
    public const FILTER_MODEL = 'model';
    public const MODEL_DEFAULT  = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT  => EmployeeModel::class,
        self::MODEL_DETAILED => EmployeeDetailedModel::class,
    ];

    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT,
        );
        return self::MODEL_MAP[$model];
    }

    protected function getModelParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            )
        );
    }

    public function getOne(): EndpointResourceResult
    {
        // ...
        return new EndpointResourceResult($this->getModelClass(), $employee);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)),
            $this->getModelParamRule(),
        );
    }
}
```

The `EmployeeDetailedModel` adds nested chains for job title, employment status, supervisor, etc. — same entity, expanded `filter` and `attributeNames` arrays. The handler doesn't know or care which one was picked.

Use this pattern when:
- The "rich" version triggers extra DB joins you don't want by default
- Different consumers (mobile app vs. admin UI) want different field sets
- You'd otherwise be tempted to add `?include=jobTitle,supervisor` style toggles

Avoid when:
- The difference is one or two fields — just include them by default
- Variants would proliferate (`detailed`, `verbose`, `minimal`, `internal`) — at that point, design a field-selection mechanism instead

---

# Recipes

## Recipe 1 — A basic Model for a Doctrine entity

```php
namespace OrangeHRM\X\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Widget;

class WidgetModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Widget $widget)
    {
        $this->setEntity($widget);
        $this->setFilters([
            'id',
            'name',
            'description',
            ['getOwner', 'getEmpNumber'],
            ['getOwner', 'getLastName'],
        ]);
        $this->setAttributeNames([
            'id',
            'name',
            'description',
            ['owner', 'empNumber'],          // nested {owner: {empNumber, lastName}}
            ['owner', 'lastName'],
        ]);
    }
}
```

Output:
```json
{ "id": 1, "name": "Acme", "description": "…",
  "owner": { "empNumber": 7, "lastName": "Smith" } }
```

## Recipe 2 — Default vs detailed variants

Already shown above (the `MODEL_MAP` pattern). The detailed model has the same constructor signature but bigger `filter`/`attributeNames` arrays:

```php
class WidgetDetailedModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Widget $widget)
    {
        $this->setEntity($widget);
        $this->setFilters([
            'id', 'name', 'description', 'createdAt', 'updatedAt',
            ['getOwner', 'getEmpNumber'],
            ['getOwner', 'getLastName'],
            ['getOwner', 'getFirstName'],
            ['getCategory', 'getId'],
            ['getCategory', 'getName'],
            ['getTags', ['getId', 'getName']],     // collection inside the model
        ]);
        $this->setAttributeNames([
            'id', 'name', 'description', 'createdAt', 'updatedAt',
            ['owner', 'empNumber'],
            ['owner', 'lastName'],
            ['owner', 'firstName'],
            ['category', 'id'],
            ['category', 'name'],
            ['tags', ['id', 'name']],
        ]);
    }
}
```

## Recipe 3 — Validation-only / single-field response (no entity)

```php
public function getOne(): EndpointResourceResult
{
    $isUnique = $this->getService()->isUniqueEmail($email);
    return new EndpointResourceResult(ArrayModel::class, ['valid' => $isUnique]);
}
```

## Recipe 4 — Delete-response echo

The delete handler echoes the deleted IDs back to the client. Use `ArrayModel`:

```php
public function delete(): EndpointResourceResult
{
    $ids = $this->getRequestParams()->getArray(
        RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS
    );
    $this->getWidgetService()->deleteWidgets($ids);
    return new EndpointResourceResult(ArrayModel::class, $ids);
}
```

This is the convention across the codebase. Look at any plugin's `delete()` to confirm — they all return `ArrayModel` with the IDs.

## Recipe 5 — Collection with total + extra meta

```php
return new EndpointCollectionResult(
    EmployeeModel::class,
    $employees,
    new ParameterBag([
        CommonParams::PARAMETER_TOTAL => $count,
        'filterSummary' => ['activeCount' => $activeCount, 'terminatedCount' => $terminatedCount],
    ])
);
```

Output:
```json
{
  "data": [{...}, {...}],
  "meta": { "total": 137, "filterSummary": { "activeCount": 120, "terminatedCount": 17 } },
  "rels": {}
}
```

---

# Checklists

## Writing a new Model class

- [ ] File location: `src/plugins/orangehrm{X}Plugin/Api/Model/<Name>Model.php`
- [ ] `implements Normalizable; use ModelTrait;`
- [ ] Constructor takes the entity (typed); calls `setEntity`, `setFilters`, `setAttributeNames`
- [ ] `filter` and `attributeNames` arrays are the same length and aligned by index
- [ ] Nested chains (`[getX, getY]`) for related entities; output key is the matching `attributeNames` entry
- [ ] For nested *output objects*, use `['outerKey', 'innerKey']` in `attributeNames`
- [ ] Add an `@OA\Schema` block — see the `rest-openapi` skill
- [ ] If detailed variant needed: register a `MODEL_MAP` in the Endpoint and switch on `?model=`

## Picking the result class

- [ ] Single resource → `EndpointResourceResult`
- [ ] Collection → `EndpointCollectionResult`
- [ ] Non-entity object payload → `ArrayModel` + `EndpointResourceResult`
- [ ] Non-entity array payload → `ArrayCollectionModel` + `EndpointCollectionResult`
- [ ] Delete echo → `ArrayModel` + `EndpointResourceResult`
- [ ] Include `total` in `meta` for list endpoints — convention: `new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])`

## Debug "field missing / null in JSON"

- [ ] **Field absent from response** → not in `filter` array
- [ ] **Field appears under wrong JSON key** → `filter` and `attributeNames` indices misaligned
- [ ] **Nested field shows up as null** → an intermediate getter returned null; `ModelTrait` short-circuits the chain silently. Verify by calling the getter manually
- [ ] **Nested field shows up as null but the related entity exists** → typo in the getter name in the `filter` chain; the silent null-safety hides this
- [ ] **Detailed model returns default fields** → handler is using `EmployeeModel::class` instead of `$this->getModelClass()`
- [ ] **Collection items have inconsistent shape** → the entity for some items has lazy-loaded relations not yet materialized; force-fetch in the service before normalization

## Things that bite

- **`ModelTrait` chain null-safety is silent.** A typo in a nested getter (`getEmployeeTerminationRecord` vs `getEmpTerminationRecord`) produces `null`, not an error. Verify chains against the entity class's real getter list when adding nested fields.
- **`filter` and `attributeNames` are positional**, not associative. Off-by-one in one but not the other shifts every key from that point on.
- **`EndpointResourceResult` vs `EndpointCollectionResult` use the same constructor signature** but produce `data: {...}` vs `data: [...]`. Picking the wrong one produces JSON that won't parse the way the client expects.
- **Generic models (`ArrayModel`, `ArrayCollectionModel`) bypass entity getters**. Whatever you pass becomes the `data` verbatim — null-safety, type coercion, and `attributeNames` mapping do not apply.
- **`MODEL_MAP` ?model= validation must use `Rules::IN`**, not a custom rule — `?model=garbage` should be a 422, not a 500 from `MODEL_MAP[$invalid]`.
- **Meta is a `ParameterBag`, not an array** — pass `new ParameterBag([...])`, not the array directly. Construct errors here become "Argument must be of type ParameterBag, array given" at runtime.
