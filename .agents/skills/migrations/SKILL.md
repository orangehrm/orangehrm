---
name: migrations
description: Reference for OrangeHRM database migrations — the `installer/Migration/V{x}/Migration.php` pattern, what runs them (the installer for fresh databases, the upgrader for existing instances), the `MIGRATIONS_MAP` registry, and the helpers available inside a migration (`SchemaHelper`, `LangStringHelper`, `ConfigHelper`, `DataGroupHelper`). Use whenever the user is writing a new migration, bumping the version, debugging a failed migration, recovering a half-applied schema, asking how install vs upgrade decide which migrations to run, or asking about schema changes / lang-string updates / `hs_hr_config` writes / FK handling. **All 5.x migrations use Doctrine DBAL via `SchemaHelper` and `createQueryBuilder()` — raw SQL is the legacy V3.3.3 pattern (imported from 4.x) and not used for new work.** Permission seeding inside migrations is documented in the `authorization` skill; this skill covers the migration mechanics, not the permission model.
---

# OrangeHRM database migrations

The migration system is **a single ordered list of versions**, executed by either the installer (fresh DB → run all) or the upgrader (existing DB → run only what's newer). There is no rollback, no per-table version tracker, and no Doctrine Migrations bundle — the project rolls its own thin layer on top of Doctrine DBAL.

## The two entry points

Both ultimately call `AppSetupUtility::runMigrations($from, $to)`.

### Installer — `php installer/console install:on-new-database`

For a fresh DB. `InstallOnNewDatabaseCommand` calls:

```php
$appSetupUtility->runMigrations('3.3.3', Config::PRODUCT_VERSION);
```

The starting version is always `'3.3.3'` and `includeFromVersion=true` (default) — so `V3_3_3` runs first to import the legacy schema baseline, then every newer version in order.

The same is true for `install:on-existing-database` (assuming the DB is empty of OHRM tables) and the web installer at `/installer/`.

### Upgrader — `php installer/console upgrade:run`

For an existing OHRM instance moving to a newer release. `UpgradeCommand` calls:

```php
$migrationVersions = $appSetupUtility->getVersionsInRange($currentVersion, null, false);
//                                                                            ^^^^^
//                                                            includeFromVersion = false
foreach ($migrationVersions as $version) {
    $appSetupUtility->runMigrationFor($version);
}
```

`$currentVersion` comes from the user (or auto-detected via `getCurrentProductVersionFromDatabase()` reading `hs_hr_config.value` for `instance.version`). `includeFromVersion=false` is the key difference from install — the version the user is already on does **not** re-run; only newer versions execute.

### Dev iteration — `migration:up`

Run a single migration class without going through the full version range or doing a reinstall:

```bash
php devTools/core/console.php migration:up "\OrangeHRM\Installer\Migration\V5_9_0\Migration"
```

This is what to use while *writing* a migration. It executes `up()`, then writes `instance.version` to the migration's version string. It does **not** write to `ohrm_migration_log` (that's installer/upgrader-only) and does not check the previous version. If you re-run it and it isn't idempotent, it'll fail or double-write — make migrations idempotent where you can, or `instance:reset` between attempts.

## The registry — `MIGRATIONS_MAP`

`installer/Util/AppSetupUtility.php::MIGRATIONS_MAP` is an ordered associative array `version => MigrationClass` (or `=> [MigrationClassA, MigrationClassB]` for multi-step versions). **A migration class does not run unless it's registered here.**

```php
public const MIGRATIONS_MAP = [
    '3.3.3' => Migration::class,                              // legacy SQL baseline
    '4.0'   => \OrangeHRM\Installer\Migration\V4_0\Migration::class,
    // …
    '5.0'   => [                                              // multi-step version
        \OrangeHRM\Installer\Migration\V5_0_0_beta\Migration::class,
        \OrangeHRM\Installer\Migration\V5_0_0\Migration::class,
    ],
    '5.8'   => \OrangeHRM\Installer\Migration\V5_8_0\Migration::class,
    '5.8.1' => \OrangeHRM\Installer\Migration\V5_8_1\Migration::class,
];
```

**Conventions** (observable from the existing entries):
- Map key is the *user-facing version* (`5.8`, `5.8.1`), not the namespace (`V5_8_0`). The migration's own `getVersion()` returns the precise version string written to `instance.version` (e.g. `5.8.0`).
- Patch versions get a key only when they ship a migration (otherwise the previous version is "current").
- Array form for multi-step versions runs the listed classes in order, all attributed to the same map key.
- New entries always go at the **end**. Iteration order is array order, not key order.

## The `AbstractMigration` contract

Every migration extends `OrangeHRM\Installer\Util\V1\AbstractMigration`:

```php
abstract class AbstractMigration
{
    abstract public function up(): void;          // do the work
    abstract public function getVersion(): string; // e.g. '5.9.0' — written to instance.version

    protected function getConnection(): Connection;
    protected function createQueryBuilder(): QueryBuilder;
    protected function getSchemaManager(): AbstractSchemaManager;

    protected function getSchemaHelper(): SchemaHelper;       // DDL operations
    protected function getDataGroupHelper(): DataGroupHelper; // permission/screen YAML seeding (see `authorization` skill)
    protected function getLangHelper(): LanguageHelper;       // i18n group/string lookups + delete
    protected function getConfigHelper(): ConfigHelper;       // hs_hr_config key/value
}
```

There is no `down()`. **Migrations are forward-only.** Rolling back a release means restoring a DB backup, not running a reverse migration.

## What runs around your `up()`

`AppSetupUtility::_runMigration()` wraps each migration class with:

1. `MigrationHelper::logMigrationStarted($version)` — appends a row to `ohrm_migration_log` (auto-creates the table if missing) with `version`, MySQL version, PHP version, `started_at`.
2. `StateContainer::setMigrationCompleted(false)` — session flag flipped to "in progress."
3. `set_time_limit(0)` — defends against long-running migrations being killed by PHP execution time limits.
4. **Your `up()` runs.**
5. `ConfigHelper::setConfigValue('instance.version', $version)` — updates `hs_hr_config.value` where `name='instance.version'`.
6. `StateContainer::setMigrationCompleted(true)` — session flag flipped to "done."
7. `MigrationHelper::logMigrationFinished($version)` — updates the matching `ohrm_migration_log` row's `finished_at`.

Between migrations, `AppSetupUtility::throwMigrationErrorIfPreviousIncomplete()` checks the session flag. If it's still false (because the previous migration threw mid-way), the next migration **refuses to start** and throws `MigrationException::previousMigrationIncomplete()`. This protects the schema from getting further corrupted by stacking partial changes — but it also means **failed migrations need manual recovery** (see "Recovery" below).

`ohrm_migration_log` is your audit trail. Any row with `finished_at IS NULL` is a half-applied migration.

---

# `SchemaHelper` — DDL via Doctrine DBAL

`OrangeHRM\Installer\Util\V1\SchemaHelper`. **Use this for all schema changes in new migrations** rather than raw `ALTER TABLE` statements. Methods accept a Doctrine `Types::*` constant and an options array.

| Method | Use for |
|---|---|
| `createTable(string $name, string $charset = 'utf8', ?string $collate = null): Table` | Returns a fluent `Table` builder (`->addColumn()->addColumn()->setPrimaryKey()->addForeignKeyConstraint()->create()`). Default engine is InnoDB. |
| `tableExists(array $tableNames): bool` | Guard before `createTable()` for idempotency. |
| `columnExists(string $table, string $column): bool` | Guard before `addColumn()`. |
| `addColumn(string $table, string $column, string $type, array $options = [])` | Single column. |
| `addOrChangeColumns(string $table, array $columnOptions)` | Adds missing, modifies existing in one call — the safest pattern for evolving columns. |
| `changeColumn(string $table, string $column, array $options)` | Modify type/length/nullability/default. |
| `renameColumn(string $table, string $oldName, string $newName)` | |
| `dropColumn(...)`, `dropColumns(...)` | |
| `addForeignKey(string $localTable, ForeignKeyConstraint $fk)` | |
| `dropForeignKeys(string $table, array $fkNames)` | |
| `dropIndex(string $table, string $indexName)`, `dropPrimaryKey(string $table)` | |
| `disableConstraints()` / `enableConstraints()` | Wraps `SET FOREIGN_KEY_CHECKS=0/1`. Use sparingly — only when reshaping FK-bearing columns. |

The options array is the Doctrine `Column` options shape:
```php
['Type' => Type::getType(Types::STRING), 'Length' => 512, 'Notnull' => false, 'Default' => null,
 'Unsigned' => false, 'Autoincrement' => false, 'Comment' => null,
 'CustomSchemaOptions' => ['collation' => 'utf8mb3_general_ci', 'charset' => 'utf8mb3']]
```

**Concrete patterns from real migrations:**

```php
// V5_8_1 — widen columns for ciphertext (most common change shape)
$opts = ['Type' => Type::getType(Types::STRING), 'Length' => 512, 'Notnull' => false];
$this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_ssn_num', $opts);

// V5_0_0_beta — create a brand-new table with FKs
$this->getSchemaHelper()->createTable('ohrm_api_permission')
    ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
    ->addColumn('module_id', Types::INTEGER, ['Notnull' => false])
    ->addColumn('data_group_id', Types::INTEGER, ['Notnull' => false])
    ->addColumn('api_name', Types::STRING, ['Length' => 255])
    ->addUniqueIndex(['api_name'], 'api_name')
    ->setPrimaryKey(['id'])
    ->addForeignKeyConstraint('ohrm_module', ['module_id'], ['id'], [], 'fk_ohrm_module_module_id')
    ->addForeignKeyConstraint('ohrm_data_group', ['data_group_id'], ['id'], [], 'fk_ohrm_data_group_data_group_id')
    ->create();

// V5_8_0 — guarded change (only fix what wasn't already fixed in a prior migration)
$existingLength = $schemaManager->introspectTable('hs_hr_emp_basicsalary')
                                ->getColumn('currency_id')->getLength();
if ($existingLength == 6) {
    $this->correctingCurrencyIdColumnInconsistencies();
}

// V5_8_0 — disable FKs, reshape a referenced column, re-add FKs
$this->disableForeignKeyChecks();
$fks = $this->getConflictingForeignKeys('ohrm_pay_grade_currency');
$this->removeConflictingForeignKeys($fks);
$this->getSchemaHelper()->changeColumn('hs_hr_emp_basicsalary', 'currency_id', [...]);
$this->recreateRemovedForeignKeys($fks);
$this->enableForeignKeyChecks();
```

The FK-juggling helpers (`getConflictingForeignKeys`, `removeConflictingForeignKeys`, `recreateRemovedForeignKeys`) live inside V5_8_0's migration class itself — they're not in `AbstractMigration`. Lift the pattern when needed; don't try to import them.

## When `SchemaHelper` isn't enough — raw DBAL

For data manipulation (UPDATE/INSERT/DELETE on existing rows) and queries, use `createQueryBuilder()`:

```php
$qb = $this->createQueryBuilder()->update('ohrm_data_group')
    ->set('can_delete', ':value')
    ->where('id = :id')
    ->setParameter('id', $localizationDataGroupId)
    ->setParameter('value', 1);
$qb->executeQuery();
```

Or raw statements for platform-specific operations Doctrine doesn't model — but **wrap them in parameter binding when there's any user-controlled input** (rare in a migration, but still):

```php
$this->getConnection()->executeStatement(
    'ALTER TABLE hs_hr_currency_type MODIFY COLUMN currency_id VARCHAR(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci'
);
```

**Avoid raw `executeStatement` for things `SchemaHelper` can express.** Schema diffs travel through DBAL's platform-aware generation, so they work across MySQL 5.7 and MariaDB 10.3 (the CI matrix) — raw SQL doesn't always.

---

# `LangStringHelper` and `LanguageHelper` — i18n strings

Translatable strings are stored in `ohrm_i18n_lang_string` (key/value) and `ohrm_i18n_translate` (per-language overrides). Two helpers:

- `LangStringHelper::insertOrUpdateLangStrings(string $directoryPath, string $groupName)` — reads `$directoryPath/lang-string/$groupName.yaml`, then for each entry: inserts a new `ohrm_i18n_lang_string` row, or updates the existing one matched by value (or by unit_id within a group). Idempotent.
- `LanguageHelper::deleteLangStringByUnitId($unitId, $groupId)` and `getGroupIdByName($module)` — remove strings.

**The standard pattern in a migration:**

```php
// in up()
$groups = ['auth', 'pim'];
foreach ($groups as $group) {
    $this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, $group);
}
$this->updateLangStringVersion($this->getVersion());
```

`LangStringHelper` is constructed by the migration itself (not in `AbstractMigration`'s lazy getters) — copy the pattern from `V5_8_0::getLangStringHelper()`:

```php
private function getLangStringHelper(): LangStringHelper
{
    return $this->langStringHelper ??= new LangStringHelper($this->getConnection());
}
```

`updateLangStringVersion` is also a private helper in the migration — it stamps newly-inserted strings (`version IS NULL`) with the migration's version so later releases can identify what came from where.

YAML format (`lang-string/auth.yaml`):
```yaml
langStrings:
  - { value: 'Reset password link was not sent', unitId: reset_password_link_was_not_sent }
  - { value: 'Your reset password link was not sent due to an error.', unitId: your_reset_password_link_not_sent_error }
```

---

# `ConfigHelper` — `hs_hr_config` key/value

The catch-all settings table. Used by the migration runtime itself (`instance.version`) and by features that need a stored config knob without a dedicated table.

```php
$this->getConfigHelper()->setConfigValue('feature.x.enabled', '1');
$value = $this->getConfigHelper()->getConfigValue('feature.x.enabled', '0'); // default
$this->getConfigHelper()->deleteConfigValue('feature.x.enabled');
```

Use this rather than introducing a 1-row settings table.

---

# Multi-step versions

```php
'5.0' => [
    \OrangeHRM\Installer\Migration\V5_0_0_beta\Migration::class,
    \OrangeHRM\Installer\Migration\V5_0_0\Migration::class,
],
```

The two classes run in order, each going through the full lifecycle (log start → up → write `instance.version` → log finish). **Each gets its own `instance.version` write**, so a failure in the second leaves `instance.version='5.0.0-beta'`. That's intentional — the upgrader can re-attempt and pick up from there.

Use multi-step when:
- A large release naturally splits into independent stages (e.g. "create new tables" then "backfill data").
- A beta/preview migration shipped to early adopters and the GA release adds more on top.

Don't use it just to break up a long `up()` — keep one class per logical version.

## Sub-classing variants

A migration *can* extend another migration class for shared helpers (rare). More commonly, separate migrations share logic via a sibling helper class in the same `V{x}` directory — e.g. `V5_0_0/AttendanceHelper.php` is a plain non-`AbstractMigration` class used only by that version's migration.

---

# The `V3_3_3` exception — legacy SQL

`installer/Migration/V3_3_3/Migration.php` is **not the pattern to follow**. It loads two raw SQL dumps (`dbscript-1.sql`, `dbscript-2.sql`) that were exported from the old 4.x codebase to bootstrap the schema. The whole 5.x rewrite built on top of that snapshot, then evolves from there with Doctrine-based migrations only.

The map key `'3.3.3'` is hardcoded as the install starting point in `InstallOnNewDatabaseCommand` (`runMigrations('3.3.3', Config::PRODUCT_VERSION)`), so it always runs first on a fresh DB.

**For any new migration: use Doctrine DBAL via `SchemaHelper` and `createQueryBuilder()`. Never add new raw SQL dumps.**

---

# Bumping the version for a release

A release that ships migrations needs four files updated:

1. **The migration itself** — `installer/Migration/V5_9_0/Migration.php` (return `'5.9.0'` from `getVersion()`).
2. **`MIGRATIONS_MAP`** — append the new entry in `installer/Util/AppSetupUtility.php`.
3. **`build/build.xml`** — bump `<property name="version" value="5.9.0"/>`.
4. **`CHANGELOG.TXT`** — append the release notes.

For permission-only changes (no schema), the migration is the 15-line stub from the `authorization` skill — same four files updated. Missing any of the four is the usual cause of "I added a migration but it doesn't run / version doesn't appear in the upgrader dropdown."

---

# Debugging / recovery

## "Previous migration incomplete" on next run

The `StateContainer` session flag says the last migration threw. The DB is in an unknown intermediate state — no autopilot here. Options, in order of preference:

1. **Restore from a backup** (the upgrader's "use a copy of your DB" warning exists precisely for this).
2. **Inspect `ohrm_migration_log`** for the row with `finished_at IS NULL` — that's the failing version. Manually reverse whatever it partially did, then `StateContainer::clearMigrationCompleted()` (which happens automatically at the next `runMigrations()` call from the installer or upgrader entry points; for `migration:up` rebooting your local web/CLI session clears the session flag).
3. **Locally**, `php devTools/core/console.php instance:reset && instance:reinstall` to start over.

## "Invalid current version" on upgrade

The `currentVersion` passed to the upgrader isn't a key in `MIGRATIONS_MAP`. Most likely: someone upgraded an instance whose `instance.version` was hand-edited or set by a fork that introduced patch versions not present in upstream. Fix `instance.version` in `hs_hr_config` to the nearest valid upstream version, or add the missing patch entries to `MIGRATIONS_MAP`.

## Migration runs locally but not in CI / on a fresh install

You probably wrote it assuming an upgrade context (e.g. assumed a column from V5_7 exists when ALTERing). The installer runs your migration after V3_3_3 *plus* everything between — so it should work. But if the new migration's `up()` introspects a table state created in a *much earlier* migration, double-check that the earlier migration actually creates the things you assume.

Also: forgot to register in `MIGRATIONS_MAP`. Cardinal sin. The file is autoloaded fine but never executed.

## `set_time_limit` warning on the host

Some hardened PHP setups disable `set_time_limit`. The runtime logs `set_time_limit: fail` but proceeds. Symptom: long migration times out at the host's default. Fix at the PHP-config layer, not the migration.

---

# Quick reference — common tasks

## Add a new migration for a release (general shape)

- [ ] Create `installer/Migration/V<x_y_z>/Migration.php` extending `AbstractMigration`
- [ ] Implement `up(): void` using `getSchemaHelper()` / `createQueryBuilder()` / helper methods (never raw SQL dumps for new work)
- [ ] Return the exact version string from `getVersion()` (`'5.9.0'`, not `'5.9'`)
- [ ] Register in `AppSetupUtility::MIGRATIONS_MAP` at the end (or append to an existing array if multi-step)
- [ ] Bump `build/build.xml` version
- [ ] Append `CHANGELOG.TXT`
- [ ] Test locally with `php devTools/core/console.php migration:up "\OrangeHRM\Installer\Migration\V<x_y_z>\Migration"`
- [ ] Test the upgrader path with `instance:reset && instance:reinstall` — confirms full version-range execution

## Schema change (add column, change type, create table)

- [ ] Use `getSchemaHelper()` methods; avoid `executeStatement('ALTER TABLE ...')` unless DBAL can't express it
- [ ] Guard with `columnExists()` / `tableExists()` when the change might re-run (idempotency)
- [ ] If reshaping a column referenced by an FK: copy the FK-juggling pattern from `V5_8_0::correctingCurrencyIdColumnInconsistencies()`
- [ ] Verify on both MySQL 5.7 and MariaDB 10.3 if the change touches charset/collation — the dev-environment skill explains how to switch DB containers

## Data fix / backfill on existing rows

- [ ] Use `createQueryBuilder()->update(...)` (or `->insert(...)`, `->delete(...)`) — parameter-bind everything
- [ ] If the fix depends on prior state, guard with a `SELECT` first so re-runs don't double-apply

## Add new lang strings

- [ ] Drop `lang-string/<module>.yaml` (matching one of the i18n group names) in the migration dir
- [ ] Add a `getLangStringHelper()` lazy method to the migration class
- [ ] In `up()`: loop the relevant groups calling `insertOrUpdateLangStrings(__DIR__, $group)`
- [ ] Call `updateLangStringVersion($this->getVersion())` so the new strings carry the migration version

## Permission seeding

- [ ] See the `authorization` skill. Mechanically: drop `permission/api.yaml` and/or `permission/screens.yaml`, then in `up()` call `$this->getDataGroupHelper()->insertApiPermissions(__DIR__ . '/permission/api.yaml')` / `insertScreenPermissions(...)`.

## Multi-step version

- [ ] Create two (or more) `V<x_y_z_label>/` directories, each with its own `Migration.php`
- [ ] Register them as an ordered array under one map key:
  ```php
  '5.9' => [
      \OrangeHRM\Installer\Migration\V5_9_0_beta\Migration::class,
      \OrangeHRM\Installer\Migration\V5_9_0\Migration::class,
  ],
  ```
- [ ] Each class's `getVersion()` should return a *distinct* version string — they each write to `instance.version` independently

## Conditional / one-shot fix

- [ ] Introspect current schema (`getSchemaManager()->introspectTable(...)`) before applying — guard with the actual state, not just a version check
- [ ] Document the *why* of the guard in a brief PHPDoc on the method, since "we already fixed this in vX_Y" is not obvious from the code

## Recover from a half-applied migration

- [ ] Identify which version failed: `SELECT version FROM ohrm_migration_log WHERE finished_at IS NULL ORDER BY id DESC LIMIT 1`
- [ ] On dev: easiest is `instance:reset && instance:reinstall` (wipes everything, runs the whole chain fresh)
- [ ] In prod: restore from backup; never try to "fix forward" on the live DB without a backup in hand
