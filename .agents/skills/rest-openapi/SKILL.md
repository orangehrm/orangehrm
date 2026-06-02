---
name: rest-openapi
description: Reference for OrangeHRM's OpenAPI v3 annotations via zircote/swagger-php — what to add to Endpoint methods and Model classes, the project's shared component refs (RecordNotFound, ForbiddenResponse, sortOrder, limit, offset), how class constants are used inside annotations, and the `generate-open-api-doc` command. Use whenever the user is annotating a new endpoint, debugging a `generate-open-api-doc --throw` failure in CI, asking which `#/components/…` ref to use, or referencing constants inside `@OA\*` blocks. **CI enforces this** — the `Lint` workflow runs `generate-open-api-doc --throw`, so a PR without proper annotations fails the build. Companion to `rest-endpoints` (where the annotations live, on handler methods) and `rest-serialization` (where Model `@OA\Schema` blocks live).
---

# OpenAPI annotations

OrangeHRM uses `zircote/swagger-php` annotations to generate `build/orangehrm-v2.json` (and an HTML viewer) from the source. The annotations sit in PHP docblocks on:

- **Endpoint handler methods** — one `@OA\Get` / `Post` / `Put` / `Delete` per verb, plus `@OA\Response` per HTTP status.
- **Model classes** — one `@OA\Schema` per model, with `@OA\Property` for each field.
- **One global file** (`src/plugins/orangehrmCorePlugin/Controller/Rest/V2/OpenApi.php`) declaring the spec, components, security scheme, and shared `Response` / `Parameter` definitions.

CI runs `php devTools/core/console.php generate-open-api-doc --throw` in `.github/workflows/linting.yml`. **`--throw` causes the command to fail non-zero on any parse error or missing schema reference** — your PR will be red until the annotations are right.

Verify locally before pushing:

```bash
php devTools/core/console.php generate-open-api-doc --throw
```

Output lands in `build/index.html` (Swagger UI viewer) and `build/orangehrm-v2.json` (raw spec). Both are uploaded as CI artifacts on failure.

## Annotation namespace

All annotations are under `OpenApi\Annotations` (aliased to `OA` in tooling). You don't need an explicit `use OpenApi\Annotations as OA;` at the top of the file — the swagger-php scanner resolves `@OA\…` based on its own conventions. Match the existing file style of whatever plugin you're working in.

## Endpoint method annotations

Every handler method (the ones the framework dispatches to: `getOne`, `getAll`, `create`, `update`, `delete`) needs a `@OA\<Verb>` block immediately above it. Shape:

```php
/**
 * @OA\Get(
 *     path="/api/v2/pim/employees/{empNumber}",          # the URL (must match routes.yaml)
 *     tags={"PIM/Employee"},                             # used for grouping in Swagger UI
 *     summary="Get an Employee",                         # one-line title
 *     operationId="get-an-employee",                     # globally unique kebab-case ID
 *     description="Retrieve details for a specific employee.",
 *
 *     @OA\PathParameter(
 *         name="empNumber",
 *         description="Specify the numerical employee number",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="model",
 *         description="Specify default or detailed response",
 *         in="query",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             enum={OrangeHRM\Pim\Api\EmployeeAPI::MODEL_DEFAULT, OrangeHRM\Pim\Api\EmployeeAPI::MODEL_DETAILED},
 *             default=OrangeHRM\Pim\Api\EmployeeAPI::MODEL_DEFAULT
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response="200",
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 oneOf={
 *                     @OA\Schema(ref="#/components/schemas/Pim-EmployeeModel"),
 *                     @OA\Schema(ref="#/components/schemas/Pim-EmployeeDetailedModel"),
 *                 }
 *             ),
 *             @OA\Property(property="meta", type="object", additionalProperties=false)
 *         )
 *     ),
 *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
 * )
 *
 * @inheritDoc
 */
public function getOne(): EndpointResourceResult { … }
```

Required pieces per method:
- **`path`** — must exactly match `routes.yaml`.
- **`tags`** — convention is `"<Plugin>/<Resource>"` (e.g. `"PIM/Employee"`, `"Admin/Job Title"`). Keeps Swagger UI grouped.
- **`operationId`** — kebab-case, globally unique. e.g. `get-an-employee`, `list-all-employees`, `create-an-employee`.
- **`summary`** — sentence-case one-liner.
- **At least one `@OA\Response`** — minimum `200`. Add `404` / `403` / `422` refs as appropriate (see "Common refs" below).

The handler method's `@inheritDoc` stays — it picks up the docstring from the interface.

## Path parameters, query parameters, request bodies

| Annotation | Use for |
|---|---|
| `@OA\PathParameter(name=…, @OA\Schema(...))` | URL placeholders like `{empNumber}`. The `name` matches the route placeholder. |
| `@OA\Parameter(name=…, in="query", required=…, @OA\Schema(...))` | Query string params. `in` is required. `required=false` for optional. |
| `@OA\RequestBody(@OA\JsonContent(@OA\Property...))` | POST/PUT bodies. |

For DELETE collection endpoints — the project ships a shared `DeleteRequestBody` ref:

```php
* @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody")
```

Sends `{ "ids": [1, 2, 3] }`. Use this for any bulk-delete endpoint.

## Response annotations

Always specify `200` (or `201` for create — though the codebase uses `200` consistently). Then layer on error responses by reference:

```php
@OA\Response(response="200", description="Success", @OA\JsonContent(...))
@OA\Response(response="404", ref="#/components/responses/RecordNotFound")
@OA\Response(response="403", ref="#/components/responses/ForbiddenResponse")
```

For bulk-delete responses:

```php
@OA\Response(response="200", ref="#/components/responses/DeleteResponse")
```

## Common refs (`src/plugins/orangehrmCorePlugin/Controller/Rest/V2/OpenApi.php`)

The project defines these shared components — **reference them rather than redefining**:

### Responses

| Ref | Use |
|---|---|
| `#/components/responses/RecordNotFound` | 404 — the standard `{error:{status:"404", message:"Record Not Found"}}` shape |
| `#/components/responses/ForbiddenResponse` | 403 — standard unauthorized shape |
| `#/components/responses/DeleteResponse` | 200 for bulk delete — `{data: [int...], meta: {}}` |

### Request bodies

| Ref | Use |
|---|---|
| `#/components/requestBodies/DeleteRequestBody` | `{ids: [int...]}` for bulk delete |

### Parameters

| Ref | Use |
|---|---|
| `#/components/parameters/sortOrder` | Query param `sortOrder` enum `{ASC, DESC}` |
| `#/components/parameters/limit` | Query param `limit` integer default 50 |
| `#/components/parameters/offset` | Query param `offset` integer default 0 |

So for any list endpoint with sort/page support:

```php
* @OA\Parameter(ref="#/components/parameters/sortOrder"),
* @OA\Parameter(ref="#/components/parameters/limit"),
* @OA\Parameter(ref="#/components/parameters/offset"),
* @OA\Parameter(
*     name="sortField",
*     in="query",
*     required=false,
*     @OA\Schema(type="string", enum=EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
* ),
```

(`sortField` isn't a shared ref because the allowed values are resource-specific — reference the DTO's `ALLOWED_SORT_FIELDS` constant.)

## Using PHP constants inside annotations

swagger-php evaluates class constants written as FQCN or short name (if `use`d) inside annotation expressions:

```php
* @OA\Schema(
*     type="string",
*     maxLength=OrangeHRM\Pim\Api\EmployeeAPI::PARAM_RULE_FILTER_NAME_MAX_LENGTH,
*     enum={OrangeHRM\Pim\Api\EmployeeAPI::MODEL_DEFAULT, OrangeHRM\Pim\Api\EmployeeAPI::MODEL_DETAILED},
* )
```

Or with imports already in the file:

```php
* @OA\Schema(type="string", enum=EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
```

**This is the right pattern** — duplicating the same constant value as a literal in the annotation creates drift when the constant changes. The CI doc-gen evaluates the constants at scan time; if the constant disappears or is renamed, generation fails (caught by `--throw`).

Constants are commonly used for:
- `maxLength` (mirror the validation rule's max length constant)
- `enum` (mirror the rule's `IN` list or a DTO's allowed-values constant)
- `default`

## Model `@OA\Schema` annotations

Every Model class gets a `@OA\Schema` block above the class declaration. **The `schema` name follows `<Plugin>-<ModelName>` convention** — referenced from endpoint responses via `#/components/schemas/<Plugin>-<ModelName>`.

```php
/**
 * @OA\Schema(
 *     schema="Pim-EmployeeModel",
 *     type="object",
 *     @OA\Property(property="empNumber", description="The employee number", type="integer"),
 *     @OA\Property(property="lastName", description="The last name", type="string"),
 *     @OA\Property(property="firstName", description="The first name", type="string"),
 *     @OA\Property(property="middleName", description="The middle name", type="string"),
 *     @OA\Property(property="employeeId", description="The employee ID", type="string"),
 *     @OA\Property(property="terminationId", description="ID of the termination record", type="integer"),
 * )
 */
class EmployeeModel implements Normalizable
{
    use ModelTrait;
    // ...
}
```

Property listing rules:
- One `@OA\Property` per attribute in `setAttributeNames()` — **keep them aligned**. A property in the model but not in the schema = consumer documentation lies. A property in the schema but not in the model = the rendered docs claim a field that never appears.
- Use `type="integer"`, `"string"`, `"boolean"`, `"number"`, `"array"`, `"object"`.
- For nested objects (from sub-arrays in `attributeNames`), nest `@OA\Property` blocks:
  ```php
  * @OA\Property(property="jobTitle", type="object",
  *     @OA\Property(property="id", type="integer"),
  *     @OA\Property(property="name", type="string"),
  * ),
  ```
- For arrays of nested objects:
  ```php
  * @OA\Property(property="skills", type="array",
  *     @OA\Items(type="object",
  *         @OA\Property(property="id", type="integer"),
  *         @OA\Property(property="name", type="string"),
  *     )
  * ),
  ```

## Models with variants — `oneOf`

When an endpoint can return either default or detailed (the `MODEL_MAP` pattern from `rest-serialization`):

```php
@OA\Property(
    property="data",
    oneOf={
        @OA\Schema(ref="#/components/schemas/Pim-EmployeeModel"),
        @OA\Schema(ref="#/components/schemas/Pim-EmployeeDetailedModel"),
    }
)
```

Both schemas must be declared (each on its respective Model class).

---

# Recipes

## Recipe 1 — Annotating a list (collection) endpoint

```php
/**
 * @OA\Get(
 *     path="/api/v2/x/widgets",
 *     tags={"X/Widget"},
 *     summary="List All Widgets",
 *     operationId="list-all-widgets",
 *     @OA\Parameter(
 *         name="name",
 *         description="Filter by widget name",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=OrangeHRM\X\Api\WidgetAPI::PARAM_RULE_NAME_MAX_LENGTH)
 *     ),
 *     @OA\Parameter(
 *         name="sortField",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum=WidgetSearchFilterParams::ALLOWED_SORT_FIELDS)
 *     ),
 *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
 *     @OA\Parameter(ref="#/components/parameters/limit"),
 *     @OA\Parameter(ref="#/components/parameters/offset"),
 *     @OA\Response(
 *         response="200",
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/X-WidgetModel")
 *             ),
 *             @OA\Property(property="meta", type="object",
 *                 @OA\Property(property="total", description="Total widget count", type="integer")
 *             )
 *         )
 *     ),
 * )
 */
public function getAll(): EndpointCollectionResult { … }
```

## Recipe 2 — Annotating a resource (single) endpoint

```php
/**
 * @OA\Get(
 *     path="/api/v2/x/widgets/{id}",
 *     tags={"X/Widget"},
 *     summary="Get a Widget",
 *     operationId="get-a-widget",
 *     @OA\PathParameter(name="id", @OA\Schema(type="integer")),
 *     @OA\Response(
 *         response="200",
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", ref="#/components/schemas/X-WidgetModel"),
 *             @OA\Property(property="meta", type="object", additionalProperties=false)
 *         )
 *     ),
 *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
 * )
 */
public function getOne(): EndpointResourceResult { … }
```

## Recipe 3 — Annotating a POST (create)

```php
/**
 * @OA\Post(
 *     path="/api/v2/x/widgets",
 *     tags={"X/Widget"},
 *     summary="Create a Widget",
 *     operationId="create-a-widget",
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 maxLength=OrangeHRM\X\Api\WidgetAPI::PARAM_RULE_NAME_MAX_LENGTH
 *             ),
 *             required={"name"}
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", ref="#/components/schemas/X-WidgetModel"),
 *             @OA\Property(property="meta", type="object", additionalProperties=false)
 *         )
 *     ),
 * )
 */
public function create(): EndpointResourceResult { … }
```

## Recipe 4 — Annotating a bulk DELETE

```php
/**
 * @OA\Delete(
 *     path="/api/v2/x/widgets",
 *     tags={"X/Widget"},
 *     summary="Delete Widgets",
 *     operationId="delete-widgets",
 *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
 *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
 * )
 */
public function delete(): EndpointResourceResult { … }
```

## Recipe 5 — A new Model with nested object

```php
/**
 * @OA\Schema(
 *     schema="X-WidgetModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="owner", type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string")
 *     ),
 * )
 */
class WidgetModel implements Normalizable { use ModelTrait; … }
```

Aligns with a `setAttributeNames(['id', 'name', ['owner', 'empNumber'], ['owner', 'lastName']])` in the model constructor.

---

# Checklists

## Add OpenAPI annotations to a new endpoint

- [ ] One `@OA\<Verb>` block per handler method (`getOne`, `getAll`, `create`, `update`, `delete`)
- [ ] `path` matches `routes.yaml` exactly (incl. `{placeholder}` casing)
- [ ] `tags` follows `"<Plugin>/<Resource>"` convention
- [ ] `operationId` is kebab-case and globally unique (`grep -rn 'operationId="…"' src/plugins/` to verify uniqueness)
- [ ] `summary` is sentence-case
- [ ] Each path placeholder has `@OA\PathParameter`
- [ ] Each query param the validator accepts has `@OA\Parameter(..., in="query")`
- [ ] Request body covered with `@OA\RequestBody` for POST/PUT (or ref the shared `DeleteRequestBody` for bulk delete)
- [ ] `200` response declared with `@OA\JsonContent` referencing the right Model schema
- [ ] `404` / `403` responses ref'd where applicable (`#/components/responses/RecordNotFound`, `ForbiddenResponse`)
- [ ] List endpoint: ref `sortOrder`, `limit`, `offset`; declare own `sortField` with `enum=DTO::ALLOWED_SORT_FIELDS`
- [ ] Constants used (not literal-duplicated) for max lengths, enums, defaults
- [ ] `php devTools/core/console.php generate-open-api-doc --throw` passes locally

## Add OpenAPI schema to a new Model

- [ ] `@OA\Schema` block above the class
- [ ] `schema="<Plugin>-<ModelName>"` matches convention (e.g. `Pim-EmployeeModel`)
- [ ] One `@OA\Property` per entry in `setAttributeNames()` — same order, same names
- [ ] Nested keys (`['outer', 'inner']`) translate to nested `@OA\Property(type="object", ...)`
- [ ] Iterable nested keys translate to `@OA\Property(type="array", @OA\Items(...))`
- [ ] Type annotations match what the entity actually returns

## Debug a `generate-open-api-doc --throw` failure

- [ ] Run locally with `--throw` (without it, the command exits 0 even on parse errors)
- [ ] Read the error output for the file/line — swagger-php is specific about which annotation is malformed
- [ ] Common: unbalanced braces in `enum={...}`, missing comma, unknown ref name, `path` not matching any route, duplicated `operationId`
- [ ] Constant not found: the FQCN inside the annotation must be a real constant — typos, recently-renamed constants, or constants moved to a different class all break here
- [ ] Schema ref not found: the `#/components/schemas/X-WidgetModel` ref needs a matching `@OA\Schema(schema="X-WidgetModel", ...)` in some scanned file (Model classes are scanned)
- [ ] Look at `build/orangehrm-v2.json` if generated — partial output can help isolate the failing block

## Things that bite

- **`operationId` collisions silently overwrite in the JSON spec.** swagger-php doesn't always warn loudly; the UI just shows one method instead of two. Use unique operation IDs from the start.
- **The `path` in `@OA\Get/Post/...` must exactly match `routes.yaml`** — including placeholder names, case, and trailing slashes. Drift here means the doc lies about which URL to call.
- **Constants inside annotations are evaluated at scan time** — if they're behind a conditional `class_exists`, swagger-php may not see them. Keep them as plain class constants.
- **`@OA\Property` order in a Schema is preserved in the rendered docs**. Match the Model's `attributeNames` order so docs reflect actual JSON key order.
- **Annotations on inherited methods (`@inheritDoc`) don't propagate** — every concrete handler needs its own `@OA\*` block, even if the parent interface or class has one.
- **`generate-open-api-doc` (without `--throw`) exits 0 on errors** — only the CI lint step uses `--throw`. When testing locally, always include the flag or you'll think it passed when it didn't.
