---
name: entities
description: Reference for defining Doctrine entities in OrangeHRM — the docblock annotation conventions, table-name conventions (`ohrm_` for new tables, `hs_hr_` for legacy 4.x-imported tables), column types and options, ID strategies, relations (ManyToOne / OneToMany / ManyToMany with `mappedBy` / `inversedBy` / `cascade` / `JoinColumn` / `JoinTable`), `ArrayCollection` initialization, `@ORM\EntityListeners` for lifecycle (the only lifecycle pattern this codebase uses — not `@HasLifecycleCallbacks`), the project-specific Decorator pattern (`DecoratorTrait` + sibling `<Name>Decorator` class), and the `NestedSet` helper for tree-shaped entities. Use whenever the user is creating a new entity, adding columns or relations to an existing one, adding an EntityListener (typically for encrypting sensitive columns), writing a Decorator, working with a tree entity (Subunit pattern), or debugging "why is this field null after load" / "why does this relation not lazy-load." Companion to `doctrine-bootstrap` (how Doctrine is wired) and `daos` (querying entities).
---

# Defining entities

All entities live in the flat `OrangeHRM\Entity\` namespace regardless of which plugin owns them. Each plugin contributes its own `entity/` directory to the multi-path namespace — see the `doctrine-bootstrap` skill for the registration mechanics.

This skill covers everything from "annotate the class" to "wire up a Decorator." For querying these entities, see the `daos` skill. For DDL (creating the underlying table), see the `migrations` skill.

## The class shell

```php
<?php
namespace OrangeHRM\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ohrm_widget")
 * @ORM\Entity
 */
class Widget
{
    // …
}
```

Five rules followed everywhere:

1. **Namespace is `OrangeHRM\Entity`**, never a plugin-specific sub-namespace. The flat namespace is a hard constraint — the framework discovers entities across all plugin `entity/` directories via PSR-4.
2. **Class-level annotations** are `@ORM\Entity` and `@ORM\Table(name="…")`. `@ORM\Entity` is required; without it Doctrine ignores the class.
3. **Docblock annotations only — no PHP 8 attributes.** The bootstrap uses `createAnnotationMetadataConfiguration` (see `doctrine-bootstrap` skill). Mixing attributes silently breaks discovery.
4. **`use Doctrine\ORM\Mapping as ORM;`** at the top of every entity file.
5. **No business logic.** Entities are plain getters/setters + ORM mapping. Convenience methods that need DB access go on a sibling `Decorator` class (see below).

## Table naming convention

- **New tables**: `ohrm_<resource>` (e.g. `ohrm_widget`, `ohrm_data_group`, `ohrm_user_role`, `ohrm_api_permission`).
- **Legacy tables**: `hs_hr_<resource>` (e.g. `hs_hr_employee`, `hs_hr_emp_basicsalary`, `hs_hr_config`). These were imported from the 4.x codebase via the `V3_3_3` baseline migration. **Don't rename them** — too many SQL queries, FKs, and external integrations depend on the exact names.

When adding a new table for a new feature, use the `ohrm_` prefix. The `hs_hr_` prefix is historical only.

## Columns

```php
/**
 * @var string|null
 *
 * @ORM\Column(name="job_description", type="string", length=400, nullable=true)
 */
private ?string $jobDescription = null;
```

Conventions seen everywhere:

- **`@var` docblock** matches the typed property declaration. Always include both.
- **`name="…"`** — the DB column name. Often snake_case while the PHP property is camelCase.
- **`type="…"`** — Doctrine type constant. Common ones: `integer`, `string`, `text`, `boolean`, `smallint`, `bigint`, `decimal`, `date`, `datetime`, `datetimetz`, `time`, `json`, `blob`.
- **`length=…`** — required for `string`; for performance and to mirror the DB column. Common defaults: 100 for names, 255 for free text fields, 512 for ciphertext storage.
- **`nullable=true`** when the column allows null. Always pair with `?type` and `= null` default in the property declaration.
- **`options={"default" : …}`** — column default at the DB level. Use sparingly; prefer PHP-side defaults in the constructor when possible.

### Reserved-word column names

Backtick-quote them in the annotation:

```php
/**
 * @ORM\Column(name="`key`", type="string", length=100)
 */
private string $key;
```

`key`, `order`, `value`, `status` — MySQL reserves these. The V5_0_0_beta migration renamed `hs_hr_config.key` to `name` precisely to avoid the issue ongoing.

### JSON columns

Doctrine has a built-in `json` type that handles serialization/deserialization:

```php
/**
 * @ORM\Column(name="additional_params", type="text", nullable=true,
 *             options={"comment" = "(DC2Type:json)"})
 */
private ?array $additionalParams = null;
```

The `(DC2Type:json)` marker in the comment is Doctrine's convention — it's how the schema tool round-trips the type when introspecting an existing column declared as `TEXT`. New JSON columns should use `type="json"` directly; the comment marker only appears on legacy columns that started as TEXT.

### Legacy ENUM columns

Columns declared `ENUM(...)` in MySQL load as **plain strings**. The bootstrap registers `enum → string` at the platform level (see `doctrine-bootstrap`). Don't try to declare `type="enum"` — Doctrine has no such type. Pattern:

```php
public const STATE_ACTIVE     = 'ACTIVE';
public const STATE_TERMINATED = 'TERMINATED';

/**
 * @ORM\Column(name="state", type="string", length=20)
 */
private string $state = self::STATE_ACTIVE;
```

Class constants document the allowed values; validators (`Rules::IN`) gate input; the column itself is just a string at the ORM level.

## IDs

The codebase has **one ID pattern**, used by every entity:

```php
/**
 * @var int
 *
 * @ORM\Column(name="id", type="integer", length=4)
 * @ORM\Id
 * @ORM\GeneratedValue(strategy="AUTO")
 */
private int $id;
```

`strategy="AUTO"` on MySQL becomes `AUTO_INCREMENT`. Don't use `IDENTITY`, `SEQUENCE`, `UUID`, or composite keys without a strong reason — the rest of the codebase assumes integer surrogate keys.

The **property name is not always `id`** — it follows the domain. Employee uses `$empNumber` (column `emp_number`), but UserRole uses `$id`. Whatever the property is, getters follow it (`getEmpNumber()`, `getId()`). Other entities reference the ID via that property name, e.g. `@JoinColumn(name="employee_id", referencedColumnName="emp_number")`.

## Unique constraints and indexes

Declared at the table level:

```php
/**
 * @ORM\Table(
 *     name="ohrm_api_permission",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="api_name", columns={"api_name"})
 *     }
 * )
 * @ORM\Entity
 */
class ApiPermission { … }
```

Multi-column unique constraints follow the same shape — list all columns in the `columns={…}` array. `@ORM\Index` works analogously for non-unique indexes (rare in this codebase; most indexes are added via migrations directly).

## Constructor — initialize collections

Every collection-valued relation needs an `ArrayCollection` initialized in the constructor:

```php
public function __construct()
{
    $this->dependents          = new ArrayCollection();
    $this->emergencyContacts   = new ArrayCollection();
    $this->skills              = new ArrayCollection();
    // … one per OneToMany / ManyToMany on the class
}
```

If you skip this, the relation is `null` until lazy-loaded — and code that adds to it before fetching from the DB will throw `Call to a member function add() on null`. The Employee entity initializes ~17 collections in its constructor; mirror that for any entity with many relations.

## Relations

The four flavors all appear in the codebase. Reference Employee (Pim plugin) for the canonical examples.

### ManyToOne — the most common

The "many" side owns the foreign key:

```php
/**
 * @var JobTitle|null
 *
 * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\JobTitle", inversedBy="employees")
 * @ORM\JoinColumn(name="job_title_code", referencedColumnName="id", nullable=true)
 */
private ?JobTitle $jobTitle = null;
```

- `targetEntity` is the related entity's FQCN.
- `inversedBy="employees"` is optional but conventional — points at the `OneToMany` collection on the other side. Doctrine uses this to keep both sides in sync when you set one.
- `@JoinColumn` names the FK column. `referencedColumnName` defaults to `id` if omitted — be explicit when the target's ID column is non-standard (e.g. `emp_number`).
- `nullable=true` when the relation is optional. Always pair with `?Type` and `= null` default.

### OneToMany — the inverse of ManyToOne

The collection side. **Always paired with a ManyToOne on the other entity.**

```php
/**
 * @var Employee[]
 *
 * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="jobTitle")
 */
private iterable $employees;
```

- `mappedBy="jobTitle"` — names the property on the *other* entity that owns the FK. **Required.**
- Property type is `iterable` in annotations + initialized to `ArrayCollection` in the constructor.
- `@var` docblock uses array notation (`Employee[]`) for static analysis hints.

### ManyToMany — needs a JoinTable

Owning side declares the join table:

```php
/**
 * @var Location[]
 *
 * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Location", inversedBy="employees")
 * @ORM\JoinTable(
 *     name="hs_hr_emp_locations",
 *     joinColumns={@ORM\JoinColumn(name="emp_number", referencedColumnName="emp_number")},
 *     inverseJoinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id")}
 * )
 */
private iterable $locations;
```

Inverse side just uses `mappedBy`:

```php
/**
 * @ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="locations")
 */
private iterable $employees;
```

### OneToOne

```php
/**
 * @var EmpPicture|null
 *
 * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\EmpPicture", mappedBy="employee")
 */
private ?EmpPicture $empPicture = null;
```

Or on the owning side (with `@JoinColumn`):

```php
/**
 * @ORM\OneToOne(targetEntity="OrangeHRM\Entity\JobSpecificationAttachment", mappedBy="jobTitle",
 *               cascade={"persist", "remove"})
 */
private ?JobSpecificationAttachment $jobSpecificationAttachment = null;
```

### Self-referential M:N (Employee.supervisors)

Same class on both sides. The owning side declares the join table; the inverse uses `mappedBy`:

```php
// Owning side
@ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", inversedBy="subordinates")
@ORM\JoinTable(name="hs_hr_emp_reportto", joinColumns=…, inverseJoinColumns=…)
private iterable $supervisors;

// Inverse side
@ORM\ManyToMany(targetEntity="OrangeHRM\Entity\Employee", mappedBy="supervisors")
private iterable $subordinates;
```

### `cascade` and `orphanRemoval`

Used sparingly. `cascade={"persist", "remove"}` is the most common pair, mostly on `OneToOne` where the child is logically owned by the parent (JobSpecificationAttachment → JobTitle). **Most relations have no cascade** — services explicitly manage related-entity lifecycle.

`orphanRemoval=true` exists but is rare in this codebase. Don't add cascades reflexively; only when the related entity has no meaning outside the parent.

## Entity lifecycle — `@ORM\EntityListeners`, not lifecycle callbacks

**The codebase does NOT use `@HasLifecycleCallbacks` / `@PrePersist` / `@PreUpdate` on the entity class itself.** Zero usages. The lifecycle pattern is **always external listener classes** registered via `@ORM\EntityListeners`.

```php
/**
 * @ORM\Table(name="hs_hr_employee")
 * @ORM\Entity
 * @ORM\EntityListeners({"OrangeHRM\Entity\Listener\EmployeeListener"})
 */
class Employee { … }
```

The listener class:

```php
namespace OrangeHRM\Entity\Listener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use OrangeHRM\Entity\Employee;

class EmployeeListener extends BaseListener
{
    public function preUpdate(Employee $employee, PreUpdateEventArgs $eventArgs): void
    {
        if ($this->encryptionEnabled() && $eventArgs->hasChangedField('ssnNumber')) {
            $employee->setSsnNumber(
                $this->getCryptographer()->encrypt($employee->getSsnNumber())
            );
        }
    }

    public function postUpdate(Employee $employee, LifecycleEventArgs $eventArgs): void
    {
        if ($this->encryptionEnabled()) {
            $employee->setSsnNumber(
                $this->getCryptographer()->decrypt($employee->getSsnNumber())
            );
        }
    }
}
```

Three real listeners exist: `EmployeeListener` (SSN encryption), `EmployeeSalaryListener` (basic-salary encryption), `EmailConfigurationListener` (SMTP password encryption).

`BaseListener` (`src/plugins/orangehrmCorePlugin/entity/Listener/BaseListener.php`) is a thin abstract:

```php
abstract class BaseListener
{
    use EncryptionHelperTrait;  // gives $this->encryptionEnabled(), $this->getCryptographer()
}
```

**Why external listeners and not `@HasLifecycleCallbacks`?** Because the lifecycle logic needs a service (`Cryptographer`) that itself depends on config. Listeners can pull services in via traits; lifecycle callbacks on the entity itself can't easily, and they couple business logic to the data shape.

The pattern: **encrypt on preUpdate (and prePersist if needed), decrypt on postUpdate (and postLoad) so the in-memory entity stays readable**. Symmetric round-trip.

## The Decorator pattern — entity-adjacent logic with DB access

Entities are plain POPOs. Logic that involves the EntityManager — looking up a related entity by ID, formatting a date with the user's timezone, computing the employee's `STATE` from termination + active flags — lives on a **sibling Decorator class**.

### The trait

`OrangeHRM\Entity\Decorator\DecoratorTrait` is `use`d on the entity:

```php
namespace OrangeHRM\Entity;

use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\EmployeeDecorator;

/**
 * @method EmployeeDecorator getDecorator()    ← IDE hint for the typed return
 *
 * @ORM\Table(name="hs_hr_employee")
 * @ORM\Entity
 */
class Employee
{
    use DecoratorTrait;
    // …
}
```

The trait gives you a single method:

```php
public function getDecorator(): object
{
    if (is_null($this->entityDecorator)) {
        $decoratorClassName = $this->getDecoratorClassName();
        $this->entityDecorator = new $decoratorClassName($this);
    }
    return $this->entityDecorator;
}
```

It resolves the Decorator class name by reflection: `OrangeHRM\Entity\Decorator\<EntityName>Decorator`. Override `getDecoratorClassName()` if you need something different (rarely needed).

The `@method` PHPDoc on the entity tells IDEs the precise return type — without it, IDEs see `object` and lose autocomplete.

### The Decorator class

```php
namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;

class EmployeeDecorator
{
    use EntityManagerHelperTrait;
    use DateTimeHelperTrait;

    public function __construct(private Employee $employee) {}

    public function setJobTitleById(?int $id): void
    {
        $jobTitle = is_null($id) ? null : $this->getReference(JobTitle::class, $id);
        $this->employee->setJobTitle($jobTitle);
    }

    public function getJoinedDate(): ?string {
        $date = $this->employee->getJoinedDate();
        return is_null($date) ? null : $this->getDateTimeHelper()->formatDate($date);
    }

    public function getState(): string { /* compute from termination + status */ }

    public function getFullName(): string { /* compose first + middle + last */ }
}
```

The Decorator is where you put:
- **`setXById($id)` shortcuts** — fetch the related entity via `getReference()` and call the real setter. Used heavily during API request body binding so callers don't have to fetch FK targets themselves.
- **Formatters that need DI** — date formatters (need user timezone), translators, etc.
- **Computed properties** — `getState()` (Active / Terminated / Not Exist), `getFullName()`, etc.

### When to use a Decorator vs. a Service

- **Decorator** if the logic is intrinsic to the entity and naturally written as "operations on this object." `Employee->getFullName()`. The Decorator gives you the entity in `$this->employee`.
- **Service** if the logic involves multiple unrelated entities, complex workflow, or external integrations. `EmployeeService::transferToLocation($empNumber, $locationId)` belongs in a Service, not a Decorator.

Decorators are widely used — 11+ in PimPlugin alone. The pattern is well-established; mirror it for any new entity that has both DB-touching helpers and a need to stay POPO.

## Tree-shaped entities — the NestedSet helper

For traditional MPTT (nested set) trees, `src/lib/orm/NestedSet/` provides:
- `NestedSetInterface`, `NodeInterface` — contracts
- `NestedSetTrait` — `lft`/`rgt`/`treeId` accessors
- `Node` — a value object

One real consumer: `Subunit` (the org structure tree, `orangehrmAdminPlugin/entity/Subunit.php`).

```php
class Subunit implements NestedSetInterface
{
    use NestedSetTrait;
    // … usual entity stuff, plus lft/rgt/depth columns
}
```

If you need a hierarchy with frequent "all descendants of X" queries, NestedSet is the existing pattern. For simple parent/child without deep traversal, a regular self-referential `ManyToOne` is fine and lower-friction.

## What's NOT used (don't propose these)

- **PHP 8 attributes** for mapping. Doctrine 2.20 supports them, but the bootstrap uses `createAnnotationMetadataConfiguration` so only docblock annotations are scanned. A partial migration silently breaks the migrated entities.
- **`@HasLifecycleCallbacks`** / `@PrePersist` / `@PreUpdate` / etc. on the entity class itself. Use `@ORM\EntityListeners` instead — every existing example uses the external listener pattern.
- **Custom `repositoryClass`** on `@Entity`. The DAO layer (see `daos` skill) replaces Doctrine's repository-per-entity feature; entities don't reference a repository class.
- **Embedded objects (`@Embedded`)**. Not used.
- **Inheritance mapping (`@InheritanceType`, `@DiscriminatorColumn`)**. Not used. The few class hierarchies in the codebase (e.g. report definitions) use separate tables and separate entities, not single-table or joined-table inheritance.
- **UUID IDs.** Every entity uses integer AUTO_INCREMENT.

---

# Recipes

## Recipe 1 — A simple entity with one M2O relation

```php
<?php
namespace OrangeHRM\Entity;

use Doctrine\ORM\Mapping as ORM;
use OrangeHRM\Entity\Decorator\DecoratorTrait;
use OrangeHRM\Entity\Decorator\WidgetDecorator;

/**
 * @method WidgetDecorator getDecorator()
 *
 * @ORM\Table(name="ohrm_widget")
 * @ORM\Entity
 */
class Widget
{
    use DecoratorTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=100)
     */
    private string $name;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\Employee")
     * @ORM\JoinColumn(name="owner_emp_number", referencedColumnName="emp_number", nullable=true)
     */
    private ?Employee $owner = null;

    // getId, getName/setName, getOwner/setOwner …
}
```

Migration creates the table (see `migrations` skill). DAO queries it (see `daos` skill).

## Recipe 2 — Entity with one-to-many + initialized collection

```php
/**
 * @ORM\Table(name="ohrm_widget_category")
 * @ORM\Entity
 */
class WidgetCategory
{
    // … id, name …

    /**
     * @var Widget[]
     * @ORM\OneToMany(targetEntity="OrangeHRM\Entity\Widget", mappedBy="category")
     */
    private iterable $widgets;

    public function __construct()
    {
        $this->widgets = new ArrayCollection();
    }

    public function getWidgets(): iterable { return $this->widgets; }
}
```

The matching `Widget` adds:

```php
/**
 * @ORM\ManyToOne(targetEntity="OrangeHRM\Entity\WidgetCategory", inversedBy="widgets")
 * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
 */
private ?WidgetCategory $category = null;
```

## Recipe 3 — Decorator class with `setXById` shortcuts

```php
namespace OrangeHRM\Entity\Decorator;

use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Widget;
use OrangeHRM\Entity\WidgetCategory;

class WidgetDecorator
{
    use EntityManagerHelperTrait;

    public function __construct(private Widget $widget) {}

    public function setOwnerByEmpNumber(?int $empNumber): void
    {
        $employee = is_null($empNumber) ? null : $this->getReference(Employee::class, $empNumber);
        $this->widget->setOwner($employee);
    }

    public function setCategoryById(?int $id): void
    {
        $category = is_null($id) ? null : $this->getReference(WidgetCategory::class, $id);
        $this->widget->setCategory($category);
    }
}
```

API handler then uses these shortcuts to bind from request body without fetching FK targets first:

```php
$widget->getDecorator()->setCategoryById($categoryId);
$widget->getDecorator()->setOwnerByEmpNumber($ownerEmpNumber);
```

`getReference()` (from `EntityManagerHelperTrait`) returns a proxy — no SELECT until the related entity's data is accessed. Cheap for the FK-binding case.

## Recipe 4 — EntityListener for encrypting a sensitive field

```php
// Entity
/**
 * @ORM\EntityListeners({"OrangeHRM\Entity\Listener\OAuthClientListener"})
 */
class OAuthClient { /* clientSecret column … */ }

// Listener
namespace OrangeHRM\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use OrangeHRM\Entity\OAuthClient;

class OAuthClientListener extends BaseListener
{
    public function prePersist(OAuthClient $client, LifecycleEventArgs $args): void
    {
        if ($this->encryptionEnabled()) {
            $client->setClientSecret($this->getCryptographer()->encrypt($client->getClientSecret()));
        }
    }

    public function preUpdate(OAuthClient $client, PreUpdateEventArgs $args): void
    {
        if ($this->encryptionEnabled() && $args->hasChangedField('clientSecret')) {
            $client->setClientSecret($this->getCryptographer()->encrypt($client->getClientSecret()));
        }
    }

    public function postLoad(OAuthClient $client, LifecycleEventArgs $args): void
    {
        if ($this->encryptionEnabled()) {
            $client->setClientSecret($this->getCryptographer()->decrypt($client->getClientSecret()));
        }
    }

    public function postUpdate(OAuthClient $client, LifecycleEventArgs $args): void
    {
        if ($this->encryptionEnabled()) {
            $client->setClientSecret($this->getCryptographer()->decrypt($client->getClientSecret()));
        }
    }
}
```

The column type stays `string` with widened length (≥512) to accommodate ciphertext. The migration that introduces the field should size it correctly from the start.

---

# Checklists

## Add a new entity

- [ ] File at `src/plugins/orangehrm{X}Plugin/entity/<Name>.php` in `namespace OrangeHRM\Entity;`
- [ ] `@ORM\Entity` + `@ORM\Table(name="ohrm_<resource>")` on class
- [ ] Plugin's entity dir is in `OrangeHRM\Entity\` PSR-4 in `src/composer.json` (see `doctrine-bootstrap` skill)
- [ ] `composer dump-autoload -d src` after touching composer.json
- [ ] Integer AUTO_INCREMENT ID — `@ORM\Id + @ORM\GeneratedValue(strategy="AUTO")`
- [ ] All collection-valued relations initialized to `new ArrayCollection()` in `__construct`
- [ ] Getters/setters for every persisted property + each relation
- [ ] If logic needs DB access: sibling Decorator class in `Entity\Decorator\` + `use DecoratorTrait` + `@method` PHPDoc hint
- [ ] If lifecycle hooks needed: `@ORM\EntityListeners({"…"})` + listener class extending `BaseListener`
- [ ] Migration to create the table (see `migrations` skill)

## Add a relation between entities

- [ ] Decide owning side — the one with the FK column. M2O ↔ O2M: owning is the M2O side. M2M: arbitrary, but `JoinTable` lives there.
- [ ] Owning side: `@ManyToOne` / `@OneToOne` / `@ManyToMany` + `@JoinColumn` (or `@JoinTable` for M2M) + optional `inversedBy="…"`
- [ ] Inverse side: `@OneToMany` / `@OneToOne` / `@ManyToMany` + `mappedBy="…"`
- [ ] Inverse side collection: initialize in constructor
- [ ] `nullable=true` on `@JoinColumn` paired with `?Type` and `= null` if relation is optional
- [ ] Add a Decorator method `setXById($id)` if FK will be set from a body param
- [ ] Schema migration (see `migrations` skill)

## Add an EntityListener

- [ ] Class in `src/plugins/orangehrm{X}Plugin/entity/Listener/` extending `BaseListener`
- [ ] Listener methods take `($entity, $eventArgs)` — type-hint the entity for IDE help
- [ ] Use `PreUpdateEventArgs::hasChangedField('property')` to short-circuit when the relevant field didn't change
- [ ] For encryption: symmetric pair — encrypt in `prePersist` / `preUpdate`, decrypt in `postLoad` / `postUpdate` so the in-memory entity stays readable
- [ ] Register the listener on the entity: `@ORM\EntityListeners({"OrangeHRM\Entity\Listener\<Name>Listener"})`

## Things that bite

- **Forgetting to initialize a collection in `__construct`** — first `$entity->getThings()->add(...)` on a fresh entity throws "Call to a member function add() on null." Doctrine populates collections during hydration but constructors run before hydration.
- **Using a sub-namespace for entities** (e.g. `OrangeHRM\Pim\Entity\Foo`). Doctrine's path scanner finds it, but the framework's PSR-4 + multi-path lookup expects flat `OrangeHRM\Entity\`. Anywhere else and references break.
- **Adding `@HasLifecycleCallbacks` + `@PrePersist`** to an entity. The codebase uses `@EntityListeners` exclusively; mixing patterns produces inconsistent maintenance debt and other devs won't look for callbacks on the entity itself.
- **`mappedBy` vs `inversedBy` confusion** — owning side has `inversedBy`, inverse side has `mappedBy`. Getting it backwards still "works" for reads but breaks Doctrine's change-tracking when setting one side and expecting the other to sync.
- **`@JoinColumn` referencing the wrong column when target's ID column is non-standard** — `referencedColumnName` defaults to `id`; Employee's ID column is `emp_number`. Always specify explicitly when joining to Employee.
- **Relying on `getDecorator()` without the `@method` PHPDoc hint** — works at runtime but IDEs see `object` and lose autocomplete. Always add the `@method <Name>Decorator getDecorator()` line.
- **Putting business logic on the entity directly** instead of in a Decorator or Service. Future you (or PHPCS-fix) will hate it; entities should stay POPO for clean serialization.
- **Setting two M2M sides explicitly** — when you call `$a->addB($b)` from the owning side, Doctrine syncs the inverse via `inversedBy`. Calling `$b->getAs()->add($a)` separately can produce duplicate join rows.
