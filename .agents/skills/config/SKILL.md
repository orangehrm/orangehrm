---
name: config
description: Reference for OrangeHRM's `hs_hr_config` key/value settings table — `ConfigService` (the runtime accessor with typed getters/setters and `KEY_*` constants), `ConfigServiceTrait` for DI access, `ConfigHelper` (the migration-time accessor used during install/upgrade), key naming convention (`<module>.<descriptor>`), and the deliberate choice of when to use a config row vs. a proper entity-backed setting. Use whenever the user is adding a runtime-tunable setting, reading a config value from a service or command, working out where a magic constant should live, or asking why `hs_hr_config.name` is the column instead of `key`. Companion to `services` (ConfigService is the canonical service trait consumer), `migrations` (where new config keys get seeded via `getConfigHelper()`), `entities` (the alternative — structured settings get an entity).
---

# Config — the key/value settings table

`hs_hr_config` is OrangeHRM's catch-all settings table. **Single table, name + value columns**, holding everything from password policy thresholds to feature flags to OAuth encryption keys to the singleton `instance.version` (which is how the upgrader knows what version you're on — see `migrations` skill).

```sql
SELECT name, value FROM hs_hr_config LIMIT 5;
-- auth.password_policy.min_password_length    | 8
-- pim_show_ssn                                 | true
-- admin.localization.default_language          | en_US
-- instance.version                             | 5.8.1
-- oauth.access_token_ttl                       | 3600
```

This skill covers reading/writing config from code, the two access paths (runtime vs. migration-time), key-naming conventions, and the decision boundary between "config row" and "real entity."

## The table

`hs_hr_config` is one of the V3.3.3 legacy tables. Two relevant facts:

1. **The column used to be called `key`** (a MySQL reserved word). V5_0_0_beta renamed it to `name`. `ConfigHelper` includes back-compat code that introspects the table and uses whichever column name exists — so very old upgrade paths still work. **New code always uses `name`.**
2. **All values are strings.** Booleans are stored as `'true'`/`'false'` (literally those strings), numbers as their string form, JSON as serialized JSON. Type coercion happens in the getter, not in the DB.

## Two access paths

### Runtime — `ConfigService` (the canonical accessor)

`OrangeHRM\Core\Service\ConfigService`. Lives in the DI container, accessed via `ConfigServiceTrait`. Every runtime use of config goes through this.

```php
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class WidgetService
{
    use ConfigServiceTrait;

    public function isFancyMode(): bool
    {
        return $this->getConfigService()->showPimSSN();             // typed getter
    }
}
```

`ConfigService` has dozens of typed methods — `showPimSSN()`, `setShowPimSSN(bool)`, `getAdminLocalizationDefaultLanguage()`, `getDefaultWorkShiftStartTime()`, etc. Each one wraps the underlying generic get/set with type coercion and the `KEY_*` constant.

When the project needs a new setting, the convention is to:
1. Add a `KEY_<NAME>` constant on `ConfigService`
2. Add a typed `getX()` / `setX()` method that reads/writes that key
3. Seed the default via a migration

This keeps callers from having to know the string key — they call `getConfigService()->getMinPasswordLength()` instead of `getConfigService()->getByKey('auth.password_policy.min_password_length')`.

### Migration-time — `ConfigHelper`

`OrangeHRM\Installer\Util\ConfigHelper`. Used inside migration `up()` methods (see `migrations` skill). **Generic** — no typed methods, just `getConfigValue($name, $default = null)` and `setConfigValue($name, $value)`.

```php
// Inside a migration's up()
public function up(): void
{
    $this->getConfigHelper()->setConfigValue(
        'auth.password_policy.min_password_length',
        '12',
    );
    // …
}
```

`$this->getConfigHelper()` is available on any `AbstractMigration` subclass — see `migrations` skill.

The reason for two helpers: the installer/migration system runs outside the full request lifecycle (no DI container, no Doctrine ORM yet), so it can't use `ConfigService`. `ConfigHelper` talks directly to the DBAL connection. Same table, same rows, simpler access.

## The DAO below it — `ConfigDao`

`ConfigService` delegates to `ConfigDao` (`OrangeHRM\Core\Dao\ConfigDao`) which holds the actual SQL. Typical chain:

```
WidgetService::isFancyMode()
  → ConfigService::showPimSSN()
     → ConfigDao::getValue(ConfigService::KEY_PIM_SHOW_SSN)
        → ConfigService converts 'true' string → boolean true
```

You rarely interact with `ConfigDao` directly. Use `ConfigService`.

## The `KEY_*` constants — the live catalog

`ConfigService` declares ~50 `public const KEY_*` constants. Sample (search `ConfigService.php` for the full list):

| Constant | Used for |
|---|---|
| `KEY_PIM_SHOW_DEPRECATED` | Whether to render deprecated PIM fields |
| `KEY_PIM_SHOW_SSN`, `KEY_PIM_SHOW_SIN`, `KEY_PIM_SHOW_TAX_EXEMPTIONS` | Per-region field toggles |
| `KEY_TIMESHEET_TIME_FORMAT` | Time format for timesheets |
| `KEY_TIMESHEET_PERIOD_AND_START_DATE` | When the work week starts |
| `KEY_ADMIN_LOCALIZATION_DEFAULT_LANGUAGE` | Default UI language |
| `KEY_ADMIN_LOCALIZATION_DEFAULT_DATE_FORMAT` | Default date display format |
| `KEY_ADMIN_DEFAULT_WORKSHIFT_START_TIME` / `_END_TIME` | Default shift times |
| `KEY_OPENID_PROVIDER_ADDED` | Whether an OpenID provider has been configured |
| `KEY_INSTANCE_IDENTIFIER` | Unique per-instance UUID (set during install) |
| `KEY_LDAP_SETTINGS` | Serialized LDAP config (JSON blob) |
| `KEY_MIN_PASSWORD_LENGTH`, `KEY_MIN_UPPERCASE_LETTERS`, etc. | Password policy |
| `KEY_OAUTH_ENCRYPTION_KEY`, `KEY_OAUTH_TOKEN_ENCRYPTION_KEY` | OAuth runtime keys |
| `KEY_OAUTH_ACCESS_TOKEN_TTL`, `KEY_OAUTH_REFRESH_TOKEN_TTL` | OAuth TTLs |
| `KEY_SHOW_SYSTEM_CHECK_SCREEN` | Whether to show the installer's system check on each login |

Plus `instance.version` (the upgrader's anchor — see `migrations`), `oauth.encryption_key`, `pim_id_generator.next` (auto-increment-like for `Employee.employeeId`), and so on.

**Before adding a new key, search `ConfigService::KEY_*`** to see if something close exists. Reusing avoids duplication and matches existing seed/upgrade paths.

## Key naming convention

```
<module>.<feature>.<descriptor>
```

Examples:
- `auth.password_policy.min_password_length`
- `admin.localization.default_language`
- `oauth.access_token_ttl`
- `dashboard.employees_on_leave_today.show_only_accessible`

Some older keys use a flatter shape (`pim_show_ssn`, `timesheet_time_format`, `email_config.sendmail_path`). New keys should use the dotted hierarchy — it groups settings nicely in queries and matches the convention used by lang-string keys.

Lowercase with snake_case segments. **Don't use camelCase or kebab-case** — the existing keys are universally snake_case, and migration scripts pattern-match on this.

## When to use config — and when not to

### Use `hs_hr_config` for

- **Feature flags** — `pim_show_ssn`, `dashboard.foo.enabled`. Boolean toggles that should change without a deploy.
- **Singleton metadata** — `instance.version`, `instance.identifier`. One value per instance.
- **Serialized blobs of low-volume settings** — `KEY_LDAP_SETTINGS` stores JSON; the LDAP config is one logical object, modified rarely, fits in one cell.

### Don't use `hs_hr_config` for

- **Per-user settings** — those need a separate `<entity>_user` join table or a column on the user-related entity, scoped by user.
- **Lists of structured records** — if you have many rows of the same shape (modules, integrations, scheduled tasks), make a proper entity. `hs_hr_config` is for the singletons.
- **Frequently-updated values** — every read does a fresh query (no caching layer in front). For hot paths, materialize the value into a proper column.
- **Anything queried by value** — `hs_hr_config` has no indexes on `value`. If you'd ever do `WHERE value = 'x'`, use a proper entity.

The deciding test: **"is this one global setting that toggles or tunes something?"** → config. **"Is this domain data?"** → entity.

## The OHRM-specific edge: `Conf.php` vs `hs_hr_config`

Two distinct config systems coexist:

| Layer | Location | Use for | Set by |
|---|---|---|---|
| `Conf.php` | `src/config/Conf.php` (file, PHP class) | DB connection params (`dbName`, `dbHost`, `dbUser`, `dbPass`), data encryption flag | The installer once during install (`writeConfFile()`); never written at runtime |
| `hs_hr_config` | DB table | Application settings (everything else) | `ConfigService::setConfigValue()` at runtime; `ConfigHelper::setConfigValue()` in migrations |

`Conf.php` is **infrastructure config** — you can't read app settings from it because Doctrine isn't bootstrapped yet when it's read. `hs_hr_config` is **application config** — read once Doctrine + the EM are up.

Don't try to add new entries to `Conf.php`. It's deliberately minimal; everything new goes in `hs_hr_config`.

## Reading and writing — the typical patterns

### Read with a typed accessor

```php
class WidgetService
{
    use ConfigServiceTrait;

    public function getBatchSize(): int
    {
        return (int) $this->getConfigService()->getConfigDao()->getValue('widget.batch_size', '100');
    }
}
```

If `ConfigService` doesn't have a typed method for your key yet, you can use the generic accessor through `ConfigDao::getValue($key, $default)`. **Better**: add a typed `getBatchSize()` method on `ConfigService` so other callers can use the same accessor.

### Read with the generic accessor inside a migration

```php
public function up(): void
{
    $existing = $this->getConfigHelper()->getConfigValue('widget.batch_size');
    if ($existing === null) {
        $this->getConfigHelper()->setConfigValue('widget.batch_size', '100');
    }
}
```

Always check before setting — re-running a migration shouldn't reset values devs may have customized.

### Write a value from runtime code

```php
$this->getConfigService()->setShowPimSSN(true);
```

Goes through `ConfigService::setConfigValue($key, $value)` → `ConfigDao::setValue` → SQL UPDATE (or INSERT if no row exists yet).

### Delete a value

```php
$this->getConfigService()->getConfigDao()->deleteValue('widget.batch_size');

// Or in a migration:
$this->getConfigHelper()->deleteConfigValue('widget.batch_size');
```

`ConfigService` has no top-level `deleteX()` — go through `getConfigDao()->deleteValue()`. Rare; usually keys persist forever and just get their value updated.

## Boolean / numeric / JSON storage

Values are always strings in the DB. Typed accessors coerce:

```php
// In ConfigService.php — representative pattern
public function showPimSSN(): bool
{
    return $this->getConfigDao()->getValue(self::KEY_PIM_SHOW_SSN) === 'true';
}

public function setShowPimSSN(bool $value): void
{
    $this->getConfigDao()->setValue(
        self::KEY_PIM_SHOW_SSN,
        $value ? 'true' : 'false',
    );
}
```

Boolean → `'true'`/`'false'` (lowercase strings).
Integer → numeric string (`'42'`).
JSON → serialized JSON string (use `json_encode`/`json_decode`).
DateTime → ISO 8601 string.

**Don't store binary or large blobs** — the `value` column is sized for short strings + small JSON. For larger config, introduce a dedicated entity.

## Seeding new config values via migration

When a new feature ships with a config-driven default:

```php
// installer/Migration/V5_9_0/Migration.php
public function up(): void
{
    $defaults = [
        'widget.batch_size'   => '100',
        'widget.enabled'      => 'true',
        'widget.timeout_secs' => '30',
    ];
    foreach ($defaults as $key => $value) {
        if ($this->getConfigHelper()->getConfigValue($key) === null) {
            $this->getConfigHelper()->setConfigValue($key, $value);
        }
    }
}
```

Always guard with `getConfigValue($key) === null` — re-running a migration shouldn't clobber an operator's customization.

For an installer-only default (set during initial install only, never re-set on upgrade), the install flow itself handles this through `AppSetupUtility` — see `migrations` skill.

---

# Recipes

## Recipe 1 — Add a new feature flag

1. Add the constant + typed methods to `ConfigService.php`:

```php
class ConfigService
{
    public const KEY_WIDGET_FANCY_MODE = 'widget.fancy_mode';

    public function isWidgetFancyModeEnabled(): bool
    {
        return $this->getConfigDao()->getValue(self::KEY_WIDGET_FANCY_MODE) === 'true';
    }

    public function setWidgetFancyModeEnabled(bool $value): void
    {
        $this->getConfigDao()->setValue(
            self::KEY_WIDGET_FANCY_MODE,
            $value ? 'true' : 'false',
        );
    }
}
```

2. Seed the default in a migration (see `migrations` skill):

```php
public function up(): void
{
    if ($this->getConfigHelper()->getConfigValue('widget.fancy_mode') === null) {
        $this->getConfigHelper()->setConfigValue('widget.fancy_mode', 'false');
    }
}
```

3. Read from runtime code:

```php
class WidgetService
{
    use ConfigServiceTrait;

    public function renderMode(): string
    {
        return $this->getConfigService()->isWidgetFancyModeEnabled() ? 'fancy' : 'plain';
    }
}
```

## Recipe 2 — Store a small structured setting (LDAP-style)

When you have a bundle of related primitives (e.g. SMTP host + port + user + password), serialize to JSON and store under one key.

```php
class ConfigService
{
    public const KEY_WIDGET_SETTINGS = 'widget.settings';

    public function getWidgetSettings(): WidgetSettings
    {
        $raw = $this->getConfigDao()->getValue(self::KEY_WIDGET_SETTINGS);
        $data = $raw ? json_decode($raw, true) : [];
        return WidgetSettings::fromArray($data);
    }

    public function setWidgetSettings(WidgetSettings $settings): void
    {
        $this->getConfigDao()->setValue(
            self::KEY_WIDGET_SETTINGS,
            json_encode($settings->toArray()),
        );
    }
}
```

Pattern: a `WidgetSettings` DTO class with `fromArray`/`toArray`, persisted as JSON. Mirrors how `KEY_LDAP_SETTINGS` works.

**Threshold for moving to an entity:** when there's more than one logical instance (e.g. multiple integrations, multiple branding profiles), or when you need to query individual fields. A single global LDAP config = JSON in config. A list of OpenID providers = an entity with rows.

## Recipe 3 — Read in a migration to make a conditional decision

```php
public function up(): void
{
    $existingVersion = $this->getConfigHelper()->getConfigValue('widget.schema_version', '1');

    if ((int) $existingVersion < 2) {
        // … schema upgrade …
        $this->getConfigHelper()->setConfigValue('widget.schema_version', '2');
    }
}
```

Useful when a migration's behavior depends on what an earlier migration did, but you can't tell from the schema alone.

---

# Checklists

## Add a new config key

- [ ] Decide if it really belongs in `hs_hr_config` — singleton or feature-flag, not domain data, not per-user
- [ ] Choose a key name following `<module>.<feature>.<descriptor>` convention, all lowercase snake_case
- [ ] Add `KEY_<NAME>` constant + typed `getX()`/`setX()` methods to `ConfigService`
- [ ] Seed default in a migration via `getConfigHelper()->setConfigValue()` — **guard with `getConfigValue($key) === null` when it's necessary** so re-runs don't clobber operator customizations
- [ ] Use `ConfigServiceTrait` + the typed getter from runtime code; don't hardcode the string key in callers
- [ ] Think twice if the value is a sensitive credentials

## Read a config value

- [ ] In runtime code (service, controller, endpoint, command): `use ConfigServiceTrait` + call the typed `getX()` method
- [ ] No typed method yet? Add one, or use `$this->getConfigService()->getConfigDao()->getValue($key, $default)` as a stopgap
- [ ] In a migration: `$this->getConfigHelper()->getConfigValue($key, $default)`

## Decide between config and an entity

- [ ] **Single global value?** → config
- [ ] **List of records of the same shape?** → entity
- [ ] **Per-user setting?** → entity (or column on user, or employee or new table/entity depend on the scenario)
- [ ] **Queried by content?** → entity (config has no indexes on `value`)
- [ ] **Bundle of related primitives, single instance?** → config with JSON blob

## Things that bite

- **All values are strings.** A `'false'` from the DB is *truthy* in PHP — `if ($value)` is true for both `'false'` and `'true'`. Always use the typed accessor, or explicitly compare `=== 'true'` OR use 1 and 0 for boolean.
- **No type validation at write time.** `setConfigValue('widget.batch_size', 'not-a-number')` succeeds. Type coercion happens on read, where it'll either silently `(int) 'not-a-number' = 0` or return your default. Always seed valid defaults.
- **The column is `name`, not `key`** — `key` was a MySQL reserved word and got renamed in V5_0_0_beta. `ConfigHelper` has back-compat for very old installs but new code always uses `name`.
- **No caching.** Every `getConfigValue()` hits the DB. Hot paths should cache the value at the start of the request (in a service property) rather than re-reading.
- **Two values with the same name?** No — the table has a unique constraint on `name`. The "no row found" return is what makes `null` default semantics work.
- **Don't `JOIN` against `hs_hr_config`** in queries. It's a key/value table; joining loses the typed accessor coercion and couples your query to string literal keys.
- **`Conf.php` ≠ `hs_hr_config`.** Two separate config systems. `Conf.php` is infrastructure (DB credentials), file-based, written at install only. `hs_hr_config` is application config, DB-backed, written at runtime. Don't try to read app settings via `Conf::*`.
