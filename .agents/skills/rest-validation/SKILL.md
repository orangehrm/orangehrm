---
name: rest-validation
description: Reference for OrangeHRM's REST API v2 input validation — `ParamRule`, `ParamRuleCollection`, `Rule`, the `Rules::*` constant catalog (OrangeHRM custom rules + Respect/Validation rules), the `ValidationDecorator` for required/not-required wrapping, composite rules (ALL_OF / ANY_OF / ONE_OF / NONE_OF), and writing custom rule classes. Use whenever the user is writing or editing a `getValidationRuleFor*()` method on an Endpoint, debugging a 422 "Invalid parameter" response, asking which rule exists for a check, writing a new `Rules\Foo` class, or wondering why a required-looking field passes empty-string. Companion to the `rest-endpoints` skill which covers the Endpoint dispatch flow.
---

# REST API v2 request validation

Every Endpoint method that handles an HTTP verb has a paired `getValidationRuleFor<Verb>()` method returning a `ParamRuleCollection`. The framework runs this before the handler — failures throw `InvalidParamException` (HTTP 422) with the offending param keys in the body. The handler never runs on validation failure.

```
HTTP request
  → AbstractRestController::handle
     → Endpoint::getValidationRuleFor<Verb>()  ← you implement this
     → Validator::validate($allParams, $rules) ← framework runs this
        ✓ passes  → handler runs
        ✗ fails   → InvalidParamException (422)
```

This skill covers everything inside `getValidationRuleFor*()`. The dispatch flow lives in the `rest-endpoints` skill.

## The four building blocks

```
ParamRuleCollection                  ← what the framework receives
  └── ParamRule (per param key)      ← combines its rules via a composite
        └── Rule (per check)         ← instantiates a validator class with positional args
              └── Rules::*           ← class constant catalog
```

### `Rule($class, [...args])`

Instantiates one of the rule classes with positional args. The class is named via a `Rules::*` constant rather than a literal string.

```php
new Rule(Rules::STRING_TYPE)
new Rule(Rules::LENGTH, [null, 100])           // [min, max] → max 100, no min
new Rule(Rules::IN, [['ASC', 'DESC']])         // first arg is an array → wrapped in another array
new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [JobTitle::class, 'jobTitleName', $option])
```

### `ParamRule(string $paramKey, Rule ...$rules)`

One per request parameter. Multiple rules combine via the composite class (`ALL_OF` by default). Override with `setCompositeClass()`:

```php
new ParamRule(self::PARAMETER_NAME,
    new Rule(Rules::STRING_TYPE),
    new Rule(Rules::LENGTH, [null, 100]),
    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [JobTitle::class, 'jobTitleName'])
)
// → must be a non-blank string AND ≤100 chars AND unique
```

### `ParamRuleCollection(ParamRule ...$paramValidations)`

What `getValidationRuleFor*()` returns. Holds one `ParamRule` per param key. **Strict by default** — any param sent that has no `ParamRule` produces a 422 with `Unexpected Parameter (\`x\`) Received`. Symfony framework keys (`_api`, `_key`, `_controller`, `_route`, `_route_params`, `_i18nEnabled`, `_dateFormattingEnabled`) are auto-excluded.

```php
public function getValidationRuleForGetAll(): ParamRuleCollection
{
    return new ParamRuleCollection(
        new ParamRule(self::FILTER_NAME, new Rule(Rules::STRING_TYPE)),
        new ParamRule(self::FILTER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)),
        ...$this->getSortingAndPaginationParamsRules(EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS),
    );
}
```

### `ValidationDecorator` — required / not required wrapping

`$this->getValidationDecorator()` returns a helper that wraps a `ParamRule` with either `REQUIRED` or `NOT_REQUIRED` semantics. **Always use one or the other** — a bare `ParamRule` with no required/not-required wrapper validates the value if present but never fails on absence, which is rarely intentional.

```php
// Required field — must be present and valid
$this->getValidationDecorator()->requiredParamRule(
    new ParamRule(self::PARAMETER_TITLE,
        new Rule(Rules::STRING_TYPE),
        new Rule(Rules::LENGTH, [null, 100])
    )
)

// Optional field — if present, must be valid; if absent (or null), pass
$this->getValidationDecorator()->notRequiredParamRule(
    new ParamRule(self::FILTER_NAME,
        new Rule(Rules::STRING_TYPE),
    )
)
```

#### The `$excludeEmptyString` flag

Both decorator methods take an optional `bool $excludeEmptyString = false`:

- `requiredParamRule($rule, false)` (default) — empty string `""` **counts as valid** (the value is present, just empty). Pass `true` to treat `""` as missing.
- `notRequiredParamRule($rule, false)` (default) — empty string `""` is treated as null/missing, so the inner rules don't run. Pass `true` to validate `""` against the inner rules.

This trips devs up — when a frontend posts `{ "name": "" }`, the default `requiredParamRule` accepts it. Add `true` to enforce non-empty.

## Composite classes

A `ParamRule` combines its child rules via a composite class. Default is `ALL_OF` (all rules must pass — typical AND).

```php
public const ALL_OF  = OHRMRules\Composite\AllOf::class;
public const ANY_OF  = OHRMRules\Composite\AnyOf::class;
public const ONE_OF  = OHRMRules\Composite\OneOf::class;     // exactly one
public const NONE_OF = OHRMRules\Composite\NoneOf::class;
```

The decorator pattern uses composites internally — `requiredParamRule` wraps the user's rules with `REQUIRED + ALL_OF(originalRules)`, and `notRequiredParamRule` wraps them with `NOT_REQUIRED + ONE_OF(originalRules)` to allow the not-required short-circuit. You don't normally set the composite directly, but it's available via `$paramRule->setCompositeClass(Rules::ANY_OF)` if you need OR semantics.

## OrangeHRM custom rules (the catalog)

These are the rules **specific to OrangeHRM** that aren't in Respect/Validation. Knowing they exist is half the battle — devs reinventing wheels for "email format" or "this employee number is one the user can see" is the most common waste.

### Presence

| `Rules::*` | What it does |
|---|---|
| `REQUIRED` | Field must be present. `$excludeEmptyString` (constructor arg) controls whether `""` counts as present. Used by `ValidationDecorator::requiredParamRule`. |
| `NOT_REQUIRED` | Field may be absent or `null`. Used by `ValidationDecorator::notRequiredParamRule`. |

You rarely instantiate these directly — go through the decorator.

### Strings

| `Rules::*` | What it does |
|---|---|
| `STRING_TYPE` | "Not-blank string type" — wraps Respect's StringType but rejects empty strings (use composite + `NotBlankStringType`). |
| `STR_LENGTH` | OrangeHRM length variant (rarely used — prefer Respect's `LENGTH`). |

### Numbers

| `Rules::*` | What it does |
|---|---|
| `ZERO_OR_POSITIVE` | Integer ≥ 0. Useful for limit/offset/page params. |
| `LESS_THAN_OR_EQUAL` | `new Rule(Rules::LESS_THAN_OR_EQUAL, [$max])` |

### Identifiers / formats

| `Rules::*` | What it does |
|---|---|
| `EMAIL` | Custom email regex (more permissive than Respect's; allows `+`, etc.) — see `Rules\Email::EMAIL_REGEX`. Empty string passes (use REQUIRED + this for "must be a valid email"). |
| `PHONE` | OrangeHRM phone format. |
| `PASSWORD` | Project's password policy. |
| `API_DATE` | OrangeHRM API date format (`Y-m-d`). |
| `COUNTRY_CODE` | ISO 3166-1 alpha-2 from the `hs_hr_country` table. |
| `PROVINCE_CODE` | Province code from the `hs_hr_province` table. |
| `CURRENCY` | ISO 4217 from `hs_hr_currency_type`. |
| `TIMEZONE_NAME` | IANA timezone name (e.g. `Asia/Colombo`). |
| `TIMEZONE_OFFSET` | Timezone offset string (e.g. `+05:30`). |

### Arrays

| `Rules::*` | What it does |
|---|---|
| `INT_ARRAY` | Array of integers. Use for bulk-delete `ids` param. |
| `EACH` | `new Rule(Rules::EACH, [$innerRuleInstance])` — apply a rule to every array element. |
| `NOT_IN` | `new Rule(Rules::NOT_IN, [[forbidden, values]])` |

### File upload

| `Rules::*` | What it does |
|---|---|
| `BASE_64_ATTACHMENT` | `new Rule(Rules::BASE_64_ATTACHMENT, [$allowedMimeTypes, $allowedExtensions, $maxFilenameLength])` — validates the structure of an uploaded Base64 attachment plus its type/extension/filename. |

### Authorization / entity existence

These are the ones most often forgotten — they integrate validation with the role-based access model:

| `Rules::*` | What it does |
|---|---|
| `ENTITY_ID_EXISTS` | `new Rule(Rules::ENTITY_ID_EXISTS, [JobTitle::class])` — the given ID must exist in that entity table. Use for FK-like inputs in create/update bodies. |
| `ENTITY_UNIQUE_PROPERTY` | `new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Entity::class, 'property', ?EntityUniquePropertyOption])` — the value must not already exist on `Entity.property`. The option object configures: trim behavior, "ignore self on update" via `setIgnoreValues(['id' => $id])`, "only match within scope" via `setMatchValues(['parentId' => $x])`. |
| `IN_ACCESSIBLE_EMP_NUMBERS` | The supplied employee number must be one the current user is permitted to access (via `UserRoleManager::getAccessibleEntityIds(Employee::class)`). Use on `{empNumber}` route params + employee-id body fields. |
| `IN_ACCESSIBLE_ENTITY_ID` | Generic version for entities other than Employee. |

### Composite (you rarely set these explicitly)

`ALL_OF`, `ANY_OF`, `ONE_OF`, `NONE_OF` — used by `ParamRule::setCompositeClass()`.

## Respect/Validation rules (point at the catalog)

`Rules::*` also aliases the full Respect/Validation library — ~150 rules. The high-frequency ones:

| `Rules::*` | What it does |
|---|---|
| `LENGTH` | `[null, 100]` or `[3, 100]` — bounded string length. |
| `IN` | `[['ASC', 'DESC']]` — value must be in the list. Note the double-array (the rule constructor takes an array). |
| `BETWEEN` | `[1, 100]` — numeric range. |
| `REGEX` | `['/pattern/']` |
| `EQUALS` / `NOT_EQUALS` | |
| `POSITIVE` / `NEGATIVE` | Numeric sign. |
| `BOOL_TYPE`, `INT_TYPE`, `ARRAY_TYPE`, `STRING_TYPE` (custom) | Strict type checks. |
| `DATE`, `DATE_TIME`, `TIME` | Date/time validity (use `API_DATE` for the OHRM standard date format). |
| `URL`, `IP`, `JSON` | Format checks. |

For anything not listed, search `src/plugins/orangehrmCorePlugin/Api/V2/Validator/Rules.php` — the constants are all in one file and the names are predictable from the Respect class names.

## Writing a custom Rule

When no existing rule covers your check, add a new class. The contract is small:

1. Extend `OrangeHRM\Core\Api\V2\Validator\Rules\AbstractRule` (which extends Respect's `AbstractRule`).
2. Implement `public function validate($input): bool`.
3. Constructor takes any positional args your rule needs.
4. Register a constant for it in `Rules.php`.

```php
<?php
namespace OrangeHRM\Core\Api\V2\Validator\Rules;

class IsBusinessDay extends AbstractRule
{
    private array $weekendDays;

    public function __construct(array $weekendDays = [6, 7])  // Sat, Sun
    {
        $this->weekendDays = $weekendDays;
    }

    public function validate($input): bool
    {
        if (!is_string($input) && !$input instanceof \DateTimeInterface) {
            return false;
        }
        $date = $input instanceof \DateTimeInterface ? $input : new \DateTime($input);
        return !in_array((int) $date->format('N'), $this->weekendDays, true);
    }
}
```

Then add to `Rules.php`:

```php
public const IS_BUSINESS_DAY = OHRMRules\IsBusinessDay::class;
```

Usage:

```php
new Rule(Rules::IS_BUSINESS_DAY, [[6, 7]])
```

### Rules that need DB access

Use `EntityManagerHelperTrait` (the same trait `EntityUniqueProperty` uses) to get a query builder inside the rule class. **The validator runs inside the request lifecycle**, so DI services are available — no need to pass a Doctrine connection in.

```php
class HasActiveSubscription extends AbstractRule
{
    use \OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

    public function validate($input): bool
    {
        return $this->createQueryBuilder(Subscription::class, 's')
            ->select('1')
            ->where('s.employee = :empNumber AND s.active = true')
            ->setParameter('empNumber', $input)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult() !== null;
    }
}
```

### Options classes for complex rules

When a rule needs more than a few primitive args, follow the `EntityUniquePropertyOption` pattern — a separate sibling class held by the rule:

```php
class JobTitleAPI implements CrudEndpoint {
    protected function getCommonBodyValidationRules(?EntityUniquePropertyOption $option = null): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(self::PARAMETER_TITLE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, 100]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [JobTitle::class, 'jobTitleName', $option])
                )
            ),
        ];
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(...$this->getCommonBodyValidationRules());
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $option = (new EntityUniquePropertyOption())
            ->setIgnoreValues(['id' => $id]);              // "unique, ignoring this row"
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE)),
            ...$this->getCommonBodyValidationRules($option),
        );
    }
}
```

This is the canonical "unique on create, unique-except-self on update" pattern. Both create and update share the body validators; update layers an `EntityUniquePropertyOption` on top.

---

# Recipes

## Recipe 1 — Validation-only endpoint (async field check)

Used for "is this email/username available?" UI checks that hit before form submit:

```yaml
apiv2_pim_employees_validation_work_email:
  path: /api/v2/pim/employees/{empNumber}/contact-details/validation/work-emails
  controller: OrangeHRM\Core\Controller\Rest\V2\GenericRestController::handle
  methods: [ GET ]
  defaults: { _api: OrangeHRM\Pim\Api\ValidationEmployeeEmailAPI, _key: empNumber }
  requirements: { empNumber: '\d+' }
```

```php
class ValidationEmployeeEmailAPI extends Endpoint implements ResourceEndpoint
{
    public function getOne(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_EMP_NUMBER);
        $email = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_WORK_EMAIL);
        $isUnique = $this->getEmployeeService()->isUniqueEmail($email, $empNumber);
        return new EndpointResourceResult(ArrayModel::class, ['valid' => $isUnique]);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_EMP_NUMBER, new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(self::PARAMETER_WORK_EMAIL,
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_WORK_EMAIL_MAX_LENGTH]),
                )
            ),
        );
    }
    // update/delete throw $this->getNotImplementedException()
}
```

## Recipe 2 — Unique-on-create, unique-except-self-on-update

Already shown above — the `EntityUniquePropertyOption` with `setIgnoreValues(['id' => $id])` is the key.

## Recipe 3 — Custom array element rule with `EACH`

Apply a rule to every element in an array param:

```php
new ParamRule(self::PARAMETER_EMPLOYEE_IDS,
    new Rule(Rules::ARRAY_TYPE),
    new Rule(Rules::EACH, [
        new Rules\Composite\AllOf(
            new Rule(Rules::POSITIVE),
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS),
        )
    ])
)
```

## Recipe 4 — Conditional validation (different rules per request shape)

`getValidationRuleFor*()` is just a method — read the request first and branch:

```php
public function getValidationRuleForCreate(): ParamRuleCollection
{
    $type = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, 'type');
    $rules = [
        new ParamRule('type', new Rule(Rules::IN, [['employee', 'contractor']])),
    ];
    if ($type === 'employee') {
        $rules[] = $this->getValidationDecorator()->requiredParamRule(
            new ParamRule('employeeId', new Rule(Rules::POSITIVE))
        );
    } else {
        $rules[] = $this->getValidationDecorator()->requiredParamRule(
            new ParamRule('contractorRef', new Rule(Rules::STRING_TYPE))
        );
    }
    return new ParamRuleCollection(...$rules);
}
```

Use sparingly — too much branching here makes the API hard to document via OpenAPI.

---

# Checklists

## Writing a new validation rule collection

- [ ] Wrap every `ParamRule` in `requiredParamRule(…)` or `notRequiredParamRule(…)` — bare rules silently pass when absent
- [ ] For optional non-empty fields, pass `excludeEmptyString: true` to `requiredParamRule` (or the field accepts `""`)
- [ ] Use `IN_ACCESSIBLE_EMP_NUMBERS` for any employee-number input — don't roll your own access check
- [ ] Use `ENTITY_ID_EXISTS` for FK-like inputs in create/update — saves writing a "does this exist" check in the handler
- [ ] Use `ENTITY_UNIQUE_PROPERTY` with `EntityUniquePropertyOption::setIgnoreValues` for the unique-on-update case
- [ ] For list endpoints: spread `getSortingAndPaginationParamsRules($allowedSortFields)` at the end
- [ ] Use `CommonParams::PARAMETER_*` constants for standard keys (`id`, `ids`, `empNumber`, `limit`, `offset`, `sortField`, `sortOrder`)

## Writing a new custom Rule class

- [ ] Place under `src/plugins/orangehrmCorePlugin/Api/V2/Validator/Rules/` (or a plugin-specific Rules dir if scope is narrow)
- [ ] Extend `AbstractRule`
- [ ] Implement `public function validate($input): bool` — return `false` for type mismatches, don't throw
- [ ] If it needs DB access: `use EntityManagerHelperTrait`
- [ ] If args grow beyond ~3, introduce a sibling `<Name>Option` class
- [ ] Register the class in `Rules.php` as `public const FOO = OHRMRules\Foo::class`
- [ ] Add a unit test alongside (mirror the existing test layout)

## Debug a 422

- [ ] Look at the response body — `error.message` and the keys under it identify which param failed
- [ ] `Unexpected Parameter (\`x\`) Received` → `ParamRuleCollection` is strict; either add a rule for `x` or stop sending it
- [ ] Required field passing when empty string sent → add `excludeEmptyString: true` to `requiredParamRule`
- [ ] Field validates as present when client sent `null` → `null` is treated as absent by `notRequiredParamRule`, so the inner rules don't run. If you want the inner rules to reject `null`, use `requiredParamRule` instead
- [ ] Array-element rule firing for the whole array → wrap the inner check with `EACH`
- [ ] Rule passes locally but fails in CI → check whether the rule queries the DB and the test DB lacks the seeded row

## Things that bite

- **A bare `ParamRule` with no required/not-required wrapper validates the value if present and passes silently if absent.** Almost never what you want. Always wrap with the decorator.
- **`Rules::IN` takes a double array**: `new Rule(Rules::IN, [['ASC', 'DESC']])` — the outer array is the constructor args, the inner is the actual list. Easy to miss.
- **Strict mode rejects unknowns by default.** Don't try to "silently allow extras" — either rule them or remove them from the request.
- **`EMAIL` rule accepts empty string.** This is intentional (empty + not-required = valid). To force non-empty: combine with `excludeEmptyString: true`.
- **`ENTITY_UNIQUE_PROPERTY` does its own query each request** — no shared cache. On bulk-import or list-validation endpoints, this is per-row. If you see slow validation, profile this.
- **Validation rules can throw** — if a rule's `validate()` throws an exception (e.g. malformed DB state), it doesn't become a 422, it becomes a 500. Defensive `false` returns are better than exceptions.
