---
name: daos
description: Reference for writing query/persistence code in OrangeHRM — the plugin-specific `<Name>Dao` classes that extend `OrangeHRM\Core\Dao\BaseDao`, the `EntityManagerHelperTrait` and its 13 methods, the QueryBuilder patterns the codebase uses heavily (joins via relation aliases, `$q->expr()->orX/andX/like/concat/in/isNull`, conditional joins), `Paginator` for accurate counts on joined queries, `QueryBuilderWrapper` for returning a buildable QB to callers, the `FilterParams` flow from API down through `setSortingAndPaginationParams`, the `SubunitIdChainTrait` for subunit-tree filters, transaction management via `beginTransaction`/`commitTransaction`/`rollBackTransaction`, and `find` / `findOneBy` / `findBy` for simple equality lookups. Use whenever the user is writing a new DAO method, debugging a query, paginating a list, deciding whether to use `findBy` vs QueryBuilder, wrapping a multi-table write in a transaction, or asking "how do I run raw SQL" (answer: usually you don't, the codebase has zero `createNativeQuery` usages and raw SQL only appears in migrations). Companion to `entities` (the entities you're querying), `doctrine-bootstrap` (how the EM is wired), `migrations` (DDL — for schema changes), and `rest-endpoints`/`rest-serialization` (the API layer that calls these DAOs).
---

# DAOs — querying and persisting

OrangeHRM has a single data-access pattern: **plugin-specific `<Name>Dao` classes that extend `OrangeHRM\Core\Dao\BaseDao`**. ~99 DAOs exist across all plugins, all following the same shape. They're plain classes (not Doctrine `EntityRepository` subclasses), they use a trait to talk to the EntityManager, and they build queries via Doctrine's `QueryBuilder`.

The path from API request to data is:

```
API endpoint handler
  ↓ calls a Service method (sometimes; often skipped for simple cases)
  ↓ calls a DAO method
  └── DAO: createQueryBuilder → joins → expressions → execute → return entities
       ↓ or: getRepository(Entity)->findOneBy([...]) for simple equality lookups
```

This skill covers the DAO layer. For the entity definitions themselves see `entities`; for how the FilterParams DTO flows in from the API layer see `rest-endpoints`.

## The base class — `BaseDao`

```php
namespace OrangeHRM\X\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Widget;
use OrangeHRM\X\Dto\WidgetSearchFilterParams;

class WidgetDao extends BaseDao
{
    public function getWidgetById(int $id): ?Widget
    {
        return $this->getRepository(Widget::class)->find($id);
    }

    public function getWidgetList(WidgetSearchFilterParams $params): array
    {
        return $this->getWidgetListPaginator($params)->getQuery()->execute();
    }

    public function getWidgetCount(WidgetSearchFilterParams $params): int
    {
        return $this->getWidgetListPaginator($params)->count();
    }

    private function getWidgetListPaginator(WidgetSearchFilterParams $params): Paginator
    {
        $q = $this->createQueryBuilder(Widget::class, 'w');
        $this->setSortingAndPaginationParams($q, $params);
        // … filters from $params …
        return $this->getPaginator($q);
    }

    public function saveWidget(Widget $widget): Widget
    {
        $this->persist($widget);
        return $widget;
    }

    public function deleteWidgets(array $ids): int
    {
        $q = $this->createQueryBuilder(Widget::class, 'w')->delete();
        $q->where($q->expr()->in('w.id', ':ids'))->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }
}
```

That's the whole pattern. `BaseDao` itself is small:

```php
abstract class BaseDao
{
    use EntityManagerHelperTrait;

    protected function count(QueryBuilder $qb): int;
    protected function setSortingParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder;
    protected function setPaginationParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder;
    protected function setSortingAndPaginationParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder;
}
```

It contributes four FilterParams-aware helpers. The heavy lifting comes from `EntityManagerHelperTrait`.

## `EntityManagerHelperTrait` — the full method catalog

Every DAO has access to these via `BaseDao`:

| Method | Returns | Purpose |
|---|---|---|
| `getEntityManager()` | `EntityManagerInterface` | The DI-registered shared EM. Rarely called directly. |
| `getRepository(string $entityClass)` | `EntityRepository<T>` | For `find`, `findBy`, `findOneBy`, `findAll`. |
| `persist($entity)` | `void` | **Persist + flush** in one call. The codebase doesn't batch flushes. |
| `remove($entity)` | `void` | **Remove + flush** in one call. |
| `createQueryBuilder(string $entityClass, string $alias, ?string $indexBy = null)` | `QueryBuilder` | Start a new QB selecting from `$entityClass AS $alias`. **Not** Doctrine's repo-level `createQueryBuilder` — this one is on the trait. |
| `getPaginator(QueryBuilder $qb)` | `Paginator` | Wraps a QB for `count()` and paginated iteration. Use for counts on joined queries. |
| `getQueryBuilderWrapper(QueryBuilder $qb)` | `QueryBuilderWrapper` | Returns a thin wrapper exposing `getQueryBuilder()`. Use when a DAO method needs to return a "buildable" QB to its caller without executing it. |
| `fetchOne(QueryBuilder $qb, int $offset = 0)` | `?object` | Convenience for `setFirstResult($offset)->setMaxResults(1)->execute()[0] ?? null`. |
| `getReference(string $entityName, $id)` | `?T` | Returns a Doctrine proxy for the entity — no SELECT until accessed. Use for FK-binding (decorators' `setXById` pattern). |
| `beginTransaction()` | `bool` | Opens a DB transaction. |
| `commitTransaction()` | `bool` | Commits. |
| `rollBackTransaction()` | `bool` | Rolls back. |

The trait wraps a DBAL `Connection` for transactions (not `EntityManager::transactional`, which exists but isn't used here).

## The persist + flush convention

`persist()` in this codebase does **both** persist and flush, in one call:

```php
protected function persist($entity): void
{
    $this->getEntityManager()->persist($entity);
    $this->getEntityManager()->flush();
}
```

This means the codebase **does not batch writes** through Doctrine's unit-of-work. Every save = one flush. Same for `remove()`. If you find yourself wanting to batch:

- For inserts/updates of many entities of the same type: use a DAO method that does `$qb->insert()` / `$qb->update()` directly (one SQL statement).
- For a multi-step operation that must be atomic: wrap in a transaction (see below). Even with one-flush-per-persist, the transaction guarantees rollback on failure.

Don't try to use `$em->persist()` directly to skip the flush — the convention is enforced for predictability.

## Simple queries — `getRepository()` + `find`, `findOneBy`, `findBy`

For pure equality lookups (`SELECT * FROM x WHERE foo = ? AND bar = ?`), skip the QueryBuilder:

```php
// By primary key
$widget = $this->getRepository(Widget::class)->find($id);

// By criteria — single result
$dependent = $this->getRepository(EmpDependent::class)->findOneBy([
    'employee' => $empNumber,
    'name'     => $name,
]);

// By criteria — multiple results
$attachments = $this->getRepository(EmployeeAttachment::class)->findBy([
    'employee' => $empNumber,
]);
```

~10 usages across PimPlugin DAOs (`EmployeeDependentDao`, `EmpEmergencyContactDao`, `EmployeeImmigrationRecordDao`, `EmployeeMembershipDao`, etc.). The pattern is: when the criteria are a flat set of `field = value` AND conditions, `findOneBy` is shorter than building a QB. **Anything more complex (LIKE, OR, joins, IN, NULL checks, sort/page) goes through QueryBuilder.**

`findBy` accepts criteria, an optional `orderBy` array, `limit`, `offset` — but at that point you should be using a QB anyway, for visibility.

## QueryBuilder — the everyday case

`createQueryBuilder(Entity::class, 'alias')` returns a Doctrine `QueryBuilder` with `SELECT $alias FROM $entityClass AS $alias` pre-filled. Build from there.

### Joins by relation name

Use the relation property name from the entity, not raw table names:

```php
$q = $this->createQueryBuilder(Employee::class, 'employee');
$q->leftJoin('employee.jobTitle',  'jobTitle');     // ManyToOne
$q->leftJoin('employee.subDivision', 'subunit');    // ManyToOne
$q->leftJoin('employee.locations',   'location');   // ManyToMany
$q->leftJoin('employee.supervisors', 'supervisor'); // self-referential ManyToMany
```

`innerJoin` / `leftJoin` semantics same as SQL. `addSelect('jobTitle')` to also hydrate the joined entities (avoids N+1 if you're going to read job title fields).

### Expression builder

`$q->expr()` returns Doctrine's `Expr` class. Heavy usage across DAOs:

```php
$q->andWhere($q->expr()->isNull('employee.employeeTerminationRecord'));
$q->andWhere($q->expr()->isNotNull('employee.employeeTerminationRecord'));
$q->andWhere($q->expr()->eq('jobTitle.id', ':id'));
$q->andWhere($q->expr()->in('employee.empNumber', ':empNumbers'));
$q->andWhere($q->expr()->notIn('employee.empNumber', ':excluded'));
$q->andWhere($q->expr()->like('employee.firstName', ':name'));

// OR with multiple conditions
$q->andWhere($q->expr()->orX(
    $q->expr()->like('employee.firstName', ':name'),
    $q->expr()->like('employee.lastName',  ':name'),
    $q->expr()->like(
        $q->expr()->concat(
            'employee.firstName',
            $q->expr()->literal(' '),
            'employee.lastName',
        ),
        ':name'
    ),
));
```

The pattern in EmployeeDao (search by name across `firstName`, `lastName`, `middleName`, and concatenated full name) is worth copying when you need a similar "search anywhere" feature.

`literal()` produces a quoted string literal in DQL — safe for SQL because Doctrine handles quoting. Don't try to interpolate user input into expressions; **always** use named parameters (`:name`) and `setParameter('name', $value)`.

### Parameter binding

```php
$q->setParameter('name', '%' . $userInput . '%');                 // for LIKE
$q->setParameter('empNumbers', [1, 2, 3]);                        // for IN — array
$q->setParameter('startDate', $date, Types::DATE_MUTABLE);        // explicit type for date/time
```

**Never concatenate user input into the DQL string.** All filters go through `setParameter`. Doctrine binds them and the underlying DBAL escapes correctly.

### Conditional joins

When a join is only needed for some queries (e.g. sorting by a supervisor's name), check the FilterParams and add the join lazily:

```php
$joinedSupervisors = false;
if ($this->getTextHelper()->strStartsWith($params->getSortField(), 'supervisor')) {
    $q->leftJoin('employee.supervisors', 'supervisor');
    $joinedSupervisors = true;
}

// later, when applying a filter that also needs supervisor:
if (!is_null($params->getSupervisorEmpNumbers())) {
    if (!$joinedSupervisors) {
        $q->leftJoin('employee.supervisors', 'supervisor');
        $joinedSupervisors = true;
    }
    $q->andWhere($q->expr()->in('supervisor.empNumber', ':supervisorEmpNumbers'))
      ->setParameter('supervisorEmpNumbers', $params->getSupervisorEmpNumbers());
}
```

The `$joinedSupervisors` flag prevents double-joining. Pattern from `EmployeeDao::getEmployeeListQueryBuilderWrapper`.

### Result retrieval

Choose by what you want:

```php
$entities = $q->getQuery()->execute();              // = getResult() — array of hydrated entities
$entities = $q->getQuery()->getResult();            // same thing, explicit

$arrays = $q->getQuery()->getArrayResult();         // arrays instead of entities (faster, no proxies)

$entity = $q->getQuery()->getOneOrNullResult();     // single entity or null
$entity = $q->getQuery()->getSingleResult();        // throws if 0 or >1 rows

$value = $q->getQuery()->getSingleScalarResult();   // single scalar (e.g. COUNT)
$values = $q->getQuery()->getScalarResult();        // array of arrays of scalars
```

`getArrayResult()` is the right choice when you only need IDs or a few columns — avoids the cost of entity hydration. EmployeeDao uses it for `getEmpNumbersByFilterParams`:

```php
$q->select('employee.empNumber');
$result = $q->getQuery()->getArrayResult();
return array_column($result, 'empNumber');
```

### The `array_column(..., 0)` trick

When a query selects `[entity, extraField]` (e.g. to enable sorting by a computed expression that needs to be in SELECT), the result is an array of `[Entity, $extraField]` pairs. Extract the entities with:

```php
$qb->addSelect('employee.empNumber');  // forces empNumber into select for sorting
$rows = $q->getQuery()->execute();
return array_column($rows, 0);          // returns just the Employee objects
```

Used in EmployeeDao when sorting requires `addSelect` of an extra column. The `0` is the index of the entity in each pair.

## Pagination — use `Paginator` for counts

Counts on **joined** queries can't be done by replacing `SELECT` with `COUNT(*)` — duplicates from JOIN/DISTINCT throw the count off. Doctrine's `Paginator` handles this correctly:

```php
private function getJobTitlesPaginator(JobTitleSearchFilterParams $params): Paginator
{
    $q = $this->createQueryBuilder(JobTitle::class, 'jt');
    $this->setSortingAndPaginationParams($q, $params);
    // … filters …
    return $this->getPaginator($q);
}

public function getJobTitles(JobTitleSearchFilterParams $params): array
{
    return $this->getJobTitlesPaginator($params)->getQuery()->execute();
}

public function getJobTitlesCount(JobTitleSearchFilterParams $params): int
{
    return $this->getJobTitlesPaginator($params)->count();
}
```

**Always pair list + count via the same Paginator-builder method.** This guarantees the filter set used for the list matches the count — drift between them is the most common cause of "total says 137 but I see 200 rows" bugs.

`setSortingAndPaginationParams($q, $filterParams)` (from `BaseDao`):

```php
protected function setSortingAndPaginationParams(QueryBuilder $qb, FilterParams $filterParams): QueryBuilder
{
    if (!is_null($filterParams->getSortField())) {
        $qb->addOrderBy($filterParams->getSortField(), $filterParams->getSortOrder());
    }
    if (!empty($filterParams->getLimit())) {                 // limit=0 means "no limit"
        $qb->setFirstResult($filterParams->getOffset())
           ->setMaxResults($filterParams->getLimit());
    }
    return $qb;
}
```

**`limit=0` disables pagination** — the FilterParams convention. Use it for reports or exports that need the full set.

## `QueryBuilderWrapper` — returning a buildable QB

When a DAO method builds a QB but the caller wants to layer more on top (additional filters, different ordering), return a `QueryBuilderWrapper` instead of executing:

```php
protected function getEmployeeListQueryBuilderWrapper(EmployeeSearchFilterParams $params): QueryBuilderWrapper
{
    $q = $this->createQueryBuilder(Employee::class, 'employee');
    // … standard joins and filters …
    return $this->getQueryBuilderWrapper($q);
}

public function getEmployeeList(EmployeeSearchFilterParams $params): array
{
    $qb = $this->getEmployeeListQueryBuilderWrapper($params)->getQueryBuilder();
    return array_column($qb->getQuery()->execute(), 0);
}

public function getEmpNumbersByFilterParams(EmployeeSearchFilterParams $params): array
{
    $params->setSortField('employee.empNumber');
    $q = $this->getEmployeeListQueryBuilderWrapper($params)->getQueryBuilder();
    $q->select('employee.empNumber');                                // override what's selected
    return array_column($q->getQuery()->getArrayResult(), 'empNumber');
}
```

`QueryBuilderWrapper` is intentionally thin — just a typed return so the caller knows it's getting a QB-in-progress, not an executed result. Used heavily in EmployeeDao.

## The FilterParams flow

FilterParams DTOs are introduced in the `rest-endpoints` skill (the API layer creates them and binds query params). The DAO consumes them via `BaseDao::setSortingAndPaginationParams` and individual filter getters:

```php
public function getEmployeeListQueryBuilderWrapper(EmployeeSearchFilterParams $params): QueryBuilderWrapper
{
    $q = $this->createQueryBuilder(Employee::class, 'employee');

    $this->setSortingAndPaginationParams($q, $params);

    if (!is_null($params->getName())) {                              // each filter is "if not null, apply"
        $q->andWhere($q->expr()->like('employee.firstName', ':name'))
          ->setParameter('name', '%' . $params->getName() . '%');
    }

    if (!is_null($params->getJobTitleId())) {
        $q->andWhere('jobTitle.id = :jobTitleId')
          ->setParameter('jobTitleId', $params->getJobTitleId());
    }

    // …

    return $this->getQueryBuilderWrapper($q);
}
```

The convention: **one if-block per filter, all `andWhere`**. Don't try to combine filter logic across blocks; keeping each one independent makes the code grep-able and the SQL inspectable.

## `SubunitIdChainTrait` — subunit-tree filtering

When a filter "include this subunit" should mean "include this subunit AND all its children", the `OrangeHRM\Pim\Dto\Traits\SubunitIdChainTrait` provides:

```php
// On the FilterParams DTO
class EmployeeSearchFilterParams extends FilterParams
{
    use SubunitIdChainTrait;
    protected ?int $subunitId = null;
    // …
}

// In the DAO:
if (!is_null($params->getSubunitId())) {
    $q->andWhere($q->expr()->in('subunit.id', ':subunitIds'))
      ->setParameter('subunitIds', $params->getSubunitIdChain());    // ← the chain, not just the ID
}
```

The trait memoizes the chain per DTO instance (calls `CompanyStructureService::getSubunitChainById($subunitId)` once). Mirror this pattern for any other tree-shaped filter that needs subtree expansion.

## Transactions

`EntityManagerHelperTrait` exposes the DBAL connection's transaction methods:

```php
public function importEmployees($csvContent): array
{
    $this->beginTransaction();
    try {
        $result = $this->getPimCsvDataImportService()->import($csvContent);
        $this->commitTransaction();
        return $result;
    } catch (CSVUploadFailedException $e) {
        $this->rollBackTransaction();
        throw new BadRequestException($e->getMessage());
    } catch (Exception $e) {
        $this->rollBackTransaction();
        throw new TransactionException($e);
    }
}
```

When to use:
- **Multi-table writes that must be atomic** — CSV import, bulk role-permission seeding, defined-report delete (which removes rows from multiple tables).
- **Anywhere a partial failure would leave inconsistent state** — e.g. creating an employee + its initial salary + its work shift assignments.

Where to place the boundary:
- **Inside a DAO method** if the transaction is scoped to a single DAO's operations (e.g. `CustomFieldDao::deleteFieldAndItsValues`).
- **Inside an API endpoint handler** if the transaction spans multiple DAOs/services (e.g. `EmployeeCSVImportAPI::create`, `PimDefinedReportAPI::delete`).

**Do not** use `EntityManager::transactional()` (the closure-based API). It exists in Doctrine but isn't used in this codebase — stick to explicit begin/commit/rollback for consistency.

## What's NOT in the codebase

- **Custom `EntityRepository` subclasses.** Entities don't declare `repositoryClass` on their `@Entity` annotation. All custom query logic lives in DAOs, not repositories. **Don't introduce one** — it'd add a parallel pattern with no clear win over DAOs.
- **`createNativeQuery`.** Zero usages in plugin DAOs. If you find yourself wanting raw SQL, it almost certainly means the query can be done in DQL or via a custom DQL function (see `doctrine-bootstrap` for adding one). The exception is DDL inside migrations — that's a different layer.
- **`EntityManager::transactional()`** — the closure variant. Use begin/commit/rollback as everywhere else.
- **Batched flushes** (the standard Doctrine "persist many, flush once" pattern). `persist()` does persist + flush in one call here.
- **DBAL connection events / SQL logging hooks** outside of dev-only listeners. Don't subscribe to connection events ad-hoc.
- **Doctrine's second-level cache** (`@Cache` annotation). Not used.

---

# Recipes

## Recipe 1 — A standard DAO with CRUD methods

```php
<?php
namespace OrangeHRM\X\Dao;

use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Entity\Widget;
use OrangeHRM\ORM\Paginator;
use OrangeHRM\X\Dto\WidgetSearchFilterParams;

class WidgetDao extends BaseDao
{
    public function getWidgetById(int $id): ?Widget
    {
        return $this->getRepository(Widget::class)->find($id);
    }

    public function saveWidget(Widget $widget): Widget
    {
        $this->persist($widget);
        return $widget;
    }

    public function deleteWidgets(array $ids): int
    {
        $q = $this->createQueryBuilder(Widget::class, 'w')->delete();
        $q->where($q->expr()->in('w.id', ':ids'))->setParameter('ids', $ids);
        return $q->getQuery()->execute();
    }

    public function getWidgetList(WidgetSearchFilterParams $params): array
    {
        return $this->getWidgetListPaginator($params)->getQuery()->execute();
    }

    public function getWidgetCount(WidgetSearchFilterParams $params): int
    {
        return $this->getWidgetListPaginator($params)->count();
    }

    private function getWidgetListPaginator(WidgetSearchFilterParams $params): Paginator
    {
        $q = $this->createQueryBuilder(Widget::class, 'w');
        $q->leftJoin('w.category', 'category');
        $this->setSortingAndPaginationParams($q, $params);

        if (!is_null($params->getName())) {
            $q->andWhere($q->expr()->like('w.name', ':name'))
              ->setParameter('name', '%' . $params->getName() . '%');
        }
        if (!is_null($params->getCategoryId())) {
            $q->andWhere('category.id = :categoryId')
              ->setParameter('categoryId', $params->getCategoryId());
        }

        return $this->getPaginator($q);
    }
}
```

## Recipe 2 — Fetch + 404-on-missing pattern (used by API handlers)

DAO:

```php
public function getWidgetById(int $id): ?Widget
{
    return $this->getRepository(Widget::class)->find($id);
}
```

API endpoint:

```php
$widget = $this->getWidgetService()->getWidgetDao()->getWidgetById($id);
$this->throwRecordNotFoundExceptionIfNotExist($widget, Widget::class);
```

`throwRecordNotFoundExceptionIfNotExist` is in `EndpointExceptionTrait` — see the `rest-endpoints` skill. Convention is: **DAO returns `?Entity`, API handler throws the 404**. Don't throw inside the DAO; that couples it to the API layer.

## Recipe 3 — Transactional multi-step write

```php
public function deleteCustomField(int $fieldId): void
{
    $this->beginTransaction();
    try {
        $this->createQueryBuilder(CustomFieldValue::class, 'v')
            ->delete()
            ->where('v.field = :fieldId')
            ->setParameter('fieldId', $fieldId)
            ->getQuery()
            ->execute();

        $field = $this->getRepository(CustomField::class)->find($fieldId);
        if ($field !== null) {
            $this->remove($field);
        }

        $this->commitTransaction();
    } catch (Exception $e) {
        $this->rollBackTransaction();
        throw $e;
    }
}
```

The pattern is consistent across the codebase: try { … commit; } catch { rollback; rethrow; }. Always rethrow — don't swallow the original exception.

## Recipe 4 — Conditional-join sort

```php
private function getEmployeeListQB(EmployeeSearchFilterParams $params): QueryBuilder
{
    $q = $this->createQueryBuilder(Employee::class, 'employee');
    $q->leftJoin('employee.jobTitle', 'jobTitle');

    // Only join supervisors when needed
    $joinedSupervisors = false;
    if (str_starts_with((string) $params->getSortField(), 'supervisor')) {
        $q->leftJoin('employee.supervisors', 'supervisor');
        $joinedSupervisors = true;
    }

    $this->setSortingAndPaginationParams($q, $params);

    if (!is_null($params->getSupervisorEmpNumbers())) {
        if (!$joinedSupervisors) {
            $q->leftJoin('employee.supervisors', 'supervisor');
        }
        $q->andWhere($q->expr()->in('supervisor.empNumber', ':sup'))
          ->setParameter('sup', $params->getSupervisorEmpNumbers());
    }

    return $q;
}
```

The `$joinedSupervisors` flag prevents adding the same join twice when both the sort and a filter need it.

## Recipe 5 — Search across multiple columns + concatenated names

```php
if (!is_null($params->getName())) {
    $q->andWhere($q->expr()->orX(
        $q->expr()->like('employee.firstName', ':name'),
        $q->expr()->like('employee.lastName',  ':name'),
        $q->expr()->like('employee.middleName', ':name'),
        $q->expr()->like(
            $q->expr()->concat(
                'employee.firstName',
                $q->expr()->literal(' '),
                'employee.lastName',
            ),
            ':name'
        ),
        $q->expr()->like(
            $q->expr()->concat(
                'employee.firstName',
                $q->expr()->literal(' '),
                'employee.middleName',
                $q->expr()->literal(' '),
                'employee.lastName',
            ),
            ':name'
        ),
    ));
    $q->setParameter('name', '%' . $params->getName() . '%');
}
```

`concat` + `literal` is how to compose a full-name match in DQL. Copy the pattern when a search needs to span multiple name columns or a concatenated representation.

## Recipe 6 — Returning a buildable QueryBuilder

When callers want to layer more onto a DAO-built query:

```php
public function getEmployeeListQueryBuilderWrapper(EmployeeSearchFilterParams $params): QueryBuilderWrapper
{
    $q = $this->createQueryBuilder(Employee::class, 'employee');
    // … standard joins + filters …
    return $this->getQueryBuilderWrapper($q);
}

// Caller adds a custom SELECT for sorting purposes:
$qb = $this->dao->getEmployeeListQueryBuilderWrapper($params)->getQueryBuilder();
$qb->addSelect($params->getSortField());
$rows = $qb->getQuery()->execute();
$entities = array_column($rows, 0);
```

Use this pattern when two DAO methods need 90% of the same query — extract the common build into a `QueryBuilderWrapper`-returning method, and let each caller specialize.

---

# Checklists

## Add a new DAO

- [ ] File at `src/plugins/orangehrm{X}Plugin/Dao/<Name>Dao.php`
- [ ] Extends `OrangeHRM\Core\Dao\BaseDao`
- [ ] One method per persistence operation: `get<Name>ById`, `save<Name>`, `delete<Name>s`, `get<Name>List` + `get<Name>Count`
- [ ] List/count methods share a private paginator-builder method to keep filters in sync
- [ ] `getRepository(…)->find($id)` for primary-key lookups
- [ ] `getRepository(…)->findOneBy([…])` for simple equality lookups; QB for everything else
- [ ] Use `persist()` (which flushes) — don't call `$em->persist()` or `flush()` directly
- [ ] FilterParams binding: `$this->setSortingAndPaginationParams($q, $params)` plus `if (!is_null(...)) andWhere` per filter
- [ ] Named parameters for **all** values; never concatenate user input into DQL

## Write a query that joins

- [ ] Use relation property names in joins (`employee.jobTitle`, not table names)
- [ ] `leftJoin` unless inner-join semantics are explicitly required
- [ ] `addSelect('joinedAlias')` if you'll read fields from the joined entity (avoids N+1)
- [ ] For counts with joins: use `$this->getPaginator($q)->count()`, not `COUNT(*)`
- [ ] Conditional joins: track with a flag to avoid double-joining

## Wrap in a transaction

- [ ] Place the boundary at the right layer — DAO for single-DAO ops, API handler for cross-service ops
- [ ] `try { … beginTransaction → work → commitTransaction; } catch { rollBackTransaction; throw; }`
- [ ] Never swallow the original exception — always rethrow (or wrap)
- [ ] Don't use `EntityManager::transactional()` — stick to explicit begin/commit/rollback

## Debug a failing query

- [ ] **Wrong count vs. list** — verify both go through the same paginator-builder method
- [ ] **Duplicates in list result** — joins + missing `distinct()`. Add `$q->distinct()` after the joins
- [ ] **Empty result when data exists** — usually a parameter binding miss (used `setParameter('foo', …)` but DQL has `:bar`). Check exact spelling
- [ ] **"Unknown entity"** — see `doctrine-bootstrap` skill — missing PSR-4 registration or stale prod cache
- [ ] **Slow query** — `$q->getQuery()->getSQL()` shows the generated SQL; run EXPLAIN against the dev DB
- [ ] **Lazy-load N+1** — add `addSelect('aliasOfJoinedEntity')` to materialize the relation in one query

## Things that bite

- **`persist()` flushes immediately.** Don't structure code expecting deferred flush — each `save` is a separate SQL round-trip in this codebase.
- **`limit=0` in FilterParams means "no limit"** (pagination disabled). If you're seeing the full set back when expecting just 50, check that the DTO didn't get a `0` limit.
- **`Paginator::count()` runs an extra COUNT query.** Calling it many times in a loop is expensive — call it once per request, cache the result for the response.
- **Forgetting to use `Paginator` for joined-query counts** produces wrong totals (joins multiply rows). Symptom: client-side pagination calculator says there should be more pages than there really are.
- **`getReference()` returns a proxy that throws on access if the row doesn't exist.** Fine for FK-binding (where you trust the ID), unsafe if the ID came from outside without validation.
- **`Repository::findOneBy` with `null` criteria value** is `WHERE column IS NULL`, not `WHERE column = NULL` (which never matches). Doctrine handles this correctly, but be aware if you're translating SQL mentally.
- **Caching `EntityRepository` instances across requests** doesn't work — DAOs are constructed per-request. The repository is fetched fresh from the EM each call.
- **`$q->expr()->literal($userInput)`** is a SQL injection if `$userInput` isn't sanitized — `literal()` just quotes the value as-is. Use named parameters instead for user input; reserve `literal()` for compile-time constants (like the space in `concat(..., literal(' '), ...)`).
