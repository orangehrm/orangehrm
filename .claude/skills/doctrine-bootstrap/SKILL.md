---
name: doctrine-bootstrap
description: Framework-level reference for how OrangeHRM wires up Doctrine ORM 2.20 — the single EntityManager singleton, multi-path entity discovery driven by `ohrm_plugin_paths`, the multi-path `OrangeHRM\Entity\` PSR-4 namespace in `src/composer.json`, the dev/prod cache split (`ArrayAdapter` vs `FilesystemAdapter`), proxy auto-generation strategy, the `enum`-to-`string` platform mapping, registering custom DQL functions like `TIME_DIFF`, and how to access the EntityManager from your code. Use whenever the user is adding a new plugin and needs to register its entity dir, debugging "class not found" / unmapped-entity errors, wondering why Doctrine still uses annotations instead of PHP 8 attributes, configuring cache behavior, regenerating proxies after deployment, or adding a custom DQL function. Companion to the `entities` skill (defining entities) and `daos` skill (querying them).
---

# Doctrine bootstrap

Everything about how the EntityManager exists, how it discovers entities, and how its caches and proxies behave. This skill rarely changes — it's the framework substrate that supports the `entities` and `daos` skills.

## The model: one EM, one connection, one DB

OrangeHRM uses **a single Doctrine `EntityManager` shared across the whole request**, talking to **one MySQL/MariaDB database**, with **all entities living in the flat `OrangeHRM\Entity\` namespace** regardless of which plugin's `entity/` directory they physically sit in.

No multi-tenancy, no read replicas, no second connection, no second EM. If you find yourself wanting any of those, you're outside the framework's assumptions.

The Doctrine version is **2.20** (per `src/composer.json`). This determines a lot of the day-to-day shape — most notably, **mapping metadata is docblock annotations (`@ORM\*`), not PHP 8 attributes.** Don't try to migrate one entity to attributes; the bootstrap uses `createAnnotationMetadataConfiguration` and the codebase is consistent throughout.

## The bootstrap class — `src/lib/orm/Doctrine.php`

```php
namespace OrangeHRM\ORM;

class Doctrine
{
    protected static ?EntityManager $entityManager = null;

    private function __construct() {
        $conf      = Config::getConf();
        $isDevMode = $this->isDevMode();
        $proxyDir  = Config::get(Config::DOCTRINE_PROXY_DIR);

        $cache  = new ArrayAdapter();
        $paths  = $this->getPaths();                                  // multi-path entity discovery
        $config = ORMSetup::createAnnotationMetadataConfiguration(    // ← annotations, not attributes
            $paths, $isDevMode, $proxyDir, $cache
        );

        if (!$isDevMode) {                                            // prod: persisted caches
            $config->setMetadataCache(new FilesystemAdapter('doctrine_metadata', 0, Config::get(Config::CACHE_DIR)));
            $config->setQueryCache   (new FilesystemAdapter('doctrine_queries',  0, Config::get(Config::CACHE_DIR)));
        }

        $config->setAutoGenerateProxyClasses(
            $isDevMode ? AUTOGENERATE_ALWAYS : AUTOGENERATE_NEVER
        );

        $config->addCustomStringFunction('TIME_DIFF', TimeDiff::class); // register custom DQL functions here

        self::$entityManager = EntityManager::create([
            'dbname' => $conf->getDbName(), 'user' => $conf->getDbUser(), 'password' => $conf->getDbPass(),
            'host' => $conf->getDbHost(), 'port' => $conf->getDbPort(),
            'driver' => 'pdo_mysql', 'charset' => 'utf8mb4',
        ], $config);

        self::$entityManager->getConnection()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');          // legacy ENUM columns load as strings
    }

    public static function getEntityManager(): EntityManager { … }    // singleton accessor
}
```

The class has two roles:
1. **Singleton holder** of the EntityManager, lazily constructed on first call to `Doctrine::getEntityManager()`.
2. **Registered into the DI container** as `Services::DOCTRINE` (via `Framework::configureContainer()` in `src/lib/framework/Framework.php`), with `Doctrine::getEntityManager` as its factory. This is how the rest of the codebase actually gets at the EM — via DI, not the singleton method directly.

The singleton-vs-DI duality is intentional: the singleton works inside the installer/upgrader bootstrap (where the DI container may not yet exist), the DI service works inside the request lifecycle.

## Multi-path entity discovery — the two registrations

Entities **must be registered in two places** for everything to work:

### 1. `ohrm_plugin_paths` — tells Doctrine where to scan

`Doctrine::getPaths()` walks the configured plugin paths and includes every `entity/` subdir that exists:

```php
private function getPaths(): array {
    $paths = [];
    foreach (Config::get('ohrm_plugin_paths') as $pluginPath) {
        $entityPath = realpath($pluginPath . '/entity');
        if ($entityPath) $paths[] = $entityPath;
    }
    return $paths;
}
```

`ohrm_plugin_paths` is populated from the project's plugin registration (generated at install time, stored in `src/config/Conf.php`). **Every plugin under `src/plugins/` is in the list automatically** — you don't need to add new plugins here.

### 2. `OrangeHRM\Entity\` PSR-4 in `src/composer.json` — tells PHP autoloader where to load from

```json
"OrangeHRM\\Entity\\": [
    "./plugins/orangehrmAdminPlugin/entity",
    "./plugins/orangehrmPerformancePlugin/entity",
    "./plugins/orangehrmPimPlugin/entity",
    "./plugins/orangehrmLeavePlugin/entity",
    "./plugins/orangehrmCorePlugin/entity",
    "./plugins/orangehrmCoreOAuthPlugin/entity",
    "./plugins/orangehrmAuthenticationPlugin/entity",
    …
]
```

This is a **multi-path PSR-4 namespace** — `OrangeHRM\Entity\Foo` can live in *any* of the listed dirs. The autoloader checks each path in order.

**Adding a new plugin with entities means editing this array AND running `composer dump-autoload -d src`.** Forgetting causes silent failures: the entity may still load (because plain PHP class resolution might find it via some other path), but Doctrine's metadata scan misses it and you get "Entity 'OrangeHRM\Entity\Foo' has no metadata" at query time.

This is already called out in `.claude/CLAUDE.md`'s "Things that bite" — it's the classic foot-gun when introducing a new plugin.

## Cache strategy

Two Doctrine caches: **metadata cache** (entity → ORM mapping) and **query cache** (DQL → SQL).

| Env | Metadata cache | Query cache | Effect |
|---|---|---|---|
| Dev (`isDebug()` true) | `ArrayAdapter` | `ArrayAdapter` | Per-request only — rescans annotations every request. Slow but always fresh. |
| Prod | `FilesystemAdapter('doctrine_metadata', 0, src/cache)` | `FilesystemAdapter('doctrine_queries', 0, src/cache)` | Persisted on disk. Fast but **must be cleared on entity changes.** |

`bin/console cache:clear` wipes both `src/cache/doctrine_metadata` and `src/cache/doctrine_queries`. Run it after deploying entity changes to prod.

There is **no result cache** (Doctrine's optional third cache) configured. Don't try to enable one ad-hoc; it would change query semantics in non-obvious ways.

## Proxies

Doctrine generates proxy classes for entities involved in lazy-loaded relations. These live in `src/config/proxy/` (the path set by `Config::DOCTRINE_PROXY_DIR`).

| Env | Strategy |
|---|---|
| Dev | `AUTOGENERATE_ALWAYS` — proxies regenerated on every request that needs them. Slow but always current. |
| Prod | `AUTOGENERATE_NEVER` — proxies must exist on disk. **Stale proxies = stale relation mappings.** |

The fix in prod is `bin/console orm:generate-proxies`, which `src/composer.json`'s `post-autoload-dump` script runs automatically:

```json
"scripts": {
    "post-autoload-dump": [
        "php ../bin/console orm:generate-proxies",
        "php ../bin/console cache:clear"
    ]
}
```

So `composer install` and `composer dump-autoload` keep proxies + caches in sync. **In prod, if you change an entity without `composer dump-autoload`, proxies are stale and lazy-loading will misbehave.**

## The `enum` → `string` platform mapping

Legacy 4.x tables (those prefixed `hs_hr_*`) use MySQL `ENUM` columns. Doctrine's DBAL has no built-in `enum` type. The bootstrap registers a platform-level mapping:

```php
self::$entityManager->getConnection()
    ->getDatabasePlatform()
    ->registerDoctrineTypeMapping('enum', 'string');
```

So an `ENUM('A','B','C')` column loads as a string into the entity. No domain-level enum class — the entity just declares the column `type="string"` and uses class constants for the allowed values:

```php
public const MARITAL_STATUS_SINGLE  = 'Single';
public const MARITAL_STATUS_MARRIED = 'Married';
public const MARITAL_STATUS_OTHER   = 'Other';

/**
 * @ORM\Column(name="emp_marital_status", type="string", length=20, nullable=true)
 */
private ?string $maritalStatus = null;
```

This is how all legacy enum-backed fields work — see `Employee::MARITAL_STATUS_*`, `Employee::STATE_*`, etc.

## Custom DQL functions

The bootstrap registers one custom DQL function: `TIME_DIFF`.

```php
$config->addCustomStringFunction('TIME_DIFF', TimeDiff::class);
```

`OrangeHRM\ORM\Functions\TimeDiff` extends `Doctrine\ORM\Query\AST\Functions\FunctionNode`, implements `parse(Parser)` and `getSql(SqlWalker)`. It's the canonical pattern; copy it when adding new functions.

**To add a new custom function:**

1. Create the class under `src/lib/orm/Functions/` extending `FunctionNode`.
2. Implement `parse(Parser $parser)` — reads the DQL tokens.
3. Implement `getSql(SqlWalker $sqlWalker)` — outputs the SQL.
4. Register it in `Doctrine.php` near the existing `TIME_DIFF` line. Use the matching call:
   - `addCustomStringFunction` for functions that return strings (DATE/TIME-family)
   - `addCustomNumericFunction` for COUNT/SUM/etc.
   - `addCustomDatetimeFunction` for date-returning functions
5. Clear the query cache (`bin/console cache:clear`) — DQL parsing is cached.

Doctrine's standard DQL grammar (`COUNT`, `SUM`, `AVG`, `MIN`, `MAX`, `CONCAT`, `LENGTH`, `LOWER`, `UPPER`, `SUBSTRING`, `TRIM`, `MOD`, `ABS`, `SQRT`, `CURRENT_DATE`, `CURRENT_TIME`, `CURRENT_TIMESTAMP`, `DATE_DIFF`, `DATE_ADD`, `DATE_SUB`, `BIT_AND`, `BIT_OR`) is already available — don't re-register those.

## Accessing the EntityManager from code

Two patterns, used in different contexts.

### `EntityManagerTrait` / `EntityManagerHelperTrait` (the default)

In normal request-handling code (DAOs, decorators, validators, services), `use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait` (or its smaller parent `EntityManagerTrait`):

```php
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

class WidgetDao extends BaseDao {
    public function findActive(): array {
        return $this->createQueryBuilder(Widget::class, 'w')
            ->andWhere('w.isActive = true')
            ->getQuery()->execute();
    }
}
```

`EntityManagerTrait::getEntityManager()` returns the DI-registered EM (`Services::DOCTRINE`). `EntityManagerHelperTrait` adds 13 convenience methods on top — see the `daos` skill for the full catalog. **This is the standard way to talk to the DB across the codebase.**

### Singleton `Doctrine::getEntityManager()` (bootstrap-only)

In code that runs **before the DI container exists** — primarily the installer (`installer/cli_install.php`, `installer/console`) and the migration runtime — use the static singleton:

```php
use OrangeHRM\ORM\Doctrine;

$em = Doctrine::getEntityManager();
```

PHPUnit's bootstrap also uses this (`src/test/phpunit/Util/bootstrap.php`). Outside those contexts, prefer the trait.

The two paths return the **same instance** — the DI service is registered with `Doctrine::getEntityManager` as its factory. They're not separate EMs.

## What's NOT in the bootstrap (and why)

- **PHP 8 attributes for mapping** — Doctrine 2.20 supports them, but the codebase is on annotations and isn't mid-migration. Don't switch one entity; it'll break consistency.
- **Multiple connections / read replicas** — single-DB by design.
- **Result cache** — only metadata and query caches are configured. Don't enable a result cache piecemeal.
- **DBAL event subscribers / Doctrine event manager** — not used. The only "lifecycle" wiring is per-entity `@EntityListeners` (see the `entities` skill).
- **A connection wrapper / event-emitting connection** — vanilla DBAL.

---

# Recipes

## Recipe 1 — Add a new custom DQL function

Goal: a `WEEKDAY(date)` function (1=Monday, 7=Sunday, mirroring MySQL).

```php
// src/lib/orm/Functions/Weekday.php
namespace OrangeHRM\ORM\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Weekday extends FunctionNode
{
    public $date;

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->date = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'WEEKDAY(' . $this->date->dispatch($sqlWalker) . ')';
    }
}
```

Register it in `src/lib/orm/Doctrine.php`:

```php
$config->addCustomNumericFunction('WEEKDAY', Weekday::class);
```

Clear the query cache, then use it in DQL: `SELECT WEEKDAY(e.birthday) FROM Employee e`.

## Recipe 2 — Register a new plugin's entity directory

When you add `src/plugins/orangehrmFooPlugin/entity/`:

1. Add the path to `OrangeHRM\Entity\` in `src/composer.json` (so PSR-4 autoloads it):
   ```json
   "OrangeHRM\\Entity\\": [
       …existing paths…,
       "./plugins/orangehrmFooPlugin/entity"
   ]
   ```
2. Run `composer dump-autoload -d src`.
3. Verify the plugin is in `ohrm_plugin_paths` (it should be automatic — every dir under `src/plugins/` is included). If not, check the plugin registration generated into `src/config/Conf.php`.
4. In dev, that's it — proxies regenerate per request. In prod, also run `bin/console orm:generate-proxies` and `bin/console cache:clear`.

## Recipe 3 — Debug "Entity 'OrangeHRM\Entity\Foo' has no metadata"

In order of most-common cause:

1. **PSR-4 path missing in `src/composer.json`** — search the `"OrangeHRM\\Entity\\"` array; if the new plugin's dir isn't there, add it + `composer dump-autoload -d src`.
2. **Class autoloads but isn't in Doctrine's metadata scan** — happens when the class exists in some other location (a forgotten old file, a vendor lib) and PSR-4 finds it there first. `composer dump-autoload` rebuilds the optimized map.
3. **Prod: stale metadata cache** — `bin/console cache:clear`.
4. **Class has no `@ORM\Entity` annotation** — open the file and check the docblock above the class is intact.

---

# Checklists

## Add a new plugin with entities

- [ ] Create `src/plugins/orangehrm{X}Plugin/entity/` and put entity classes there in `namespace OrangeHRM\Entity;`
- [ ] Add the path to `"OrangeHRM\\Entity\\"` array in `src/composer.json`
- [ ] Run `composer dump-autoload -d src`
- [ ] In prod (or to mirror prod): `bin/console orm:generate-proxies` + `bin/console cache:clear`
- [ ] Verify in dev: instantiate the entity in a test or `tinker`-style script and confirm `Doctrine::getEntityManager()->getClassMetadata(Foo::class)` doesn't throw

## Deploy entity changes to prod

- [ ] After `composer install` (or `composer dump-autoload`), confirm the `post-autoload-dump` script ran (`orm:generate-proxies` + `cache:clear`)
- [ ] If you bypassed composer, manually run `bin/console orm:generate-proxies` + `bin/console cache:clear`
- [ ] Verify lazy-loaded relations work in a smoke test — stale proxies typically present as "Class … not found" or fields silently returning null

## Add a custom DQL function

- [ ] Class in `src/lib/orm/Functions/` extending `FunctionNode` with `parse()` + `getSql()`
- [ ] Registered in `Doctrine.php` near `addCustomStringFunction('TIME_DIFF', …)`
- [ ] Match the right registration method (`String` / `Numeric` / `Datetime`)
- [ ] `bin/console cache:clear` after registering (DQL is cached)
- [ ] Test in DQL before the rule is committed

## Things that bite

- **Forgetting the PSR-4 entry in `src/composer.json`** when adding a new plugin's entity dir. Doctrine's metadata scan walks `ohrm_plugin_paths` (which includes the new dir automatically), but PHP's autoloader doesn't find the class. Result: "class not found" or "no metadata."
- **Forgetting `composer dump-autoload` after editing the PSR-4 array.** The JSON change alone doesn't update the optimized class map; `composer install` or `dump-autoload` must run.
- **Prod proxies stale after entity changes** — until `orm:generate-proxies` runs, lazy-loaded relations misbehave. The `post-autoload-dump` script handles this for normal composer-driven deploys; manual deploys need to remember it.
- **Cache:clear forgotten after a query-cache miss-causing change** (custom DQL function added, entity mapping evolved). DQL parses are cached. The fix is always `bin/console cache:clear`.
- **`enum` mapping is platform-wide, not per-column.** Don't try to declare `type="enum"` on a Column — Doctrine has no built-in `enum` type. Use `type="string"` + class constants and rely on the platform mapping to load ENUM columns as strings.
- **Trying to migrate one entity to PHP 8 attributes.** The bootstrap uses `createAnnotationMetadataConfiguration`, so attributes on a single entity will be ignored. Don't do partial migrations.
