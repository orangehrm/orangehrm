---
name: console-commands
description: Reference for OrangeHRM's two Symfony Console entry points — `bin/console` (production console, lightweight, plugin-registered commands like `cache:clear` / `orm:generate-proxies` / `orangehrm:run-schedule`) and `devTools/core/console.php` (developer-only with its own `composer.json`, registers commands like `php-cs-fix`, `instance:create-test-db`, `instance:reset`, `instance:reinstall`, `add-data-group`, `add-role-permission`, `migration:up`, `generate-open-api-doc`). Covers the `OrangeHRM\Framework\Console\Command` base class (with `getCommandName()`, `getIO()` returning a `SymfonyStyle`), the `ConsoleConfigurationInterface::registerCommands()` plugin hook for surfacing commands in `bin/console`, conditional registration (e.g. only in non-prod), and the helper-trait composition pattern shared with services. Use whenever the user is adding a new console command, deciding which console it belongs in, debugging "why doesn't my command appear in `bin/console`", or wiring up commands that need to talk to services/DAOs. Companion to `services` (commands often invoke services), `scheduled-jobs` (cron tasks are commands), `dev-environment` / `migrations` (existing dev-tool commands).
---

# Console commands

OrangeHRM has **two console entry points**, each serving a different audience:

| Entry point | Audience | Path | Composer dependencies |
|---|---|---|---|
| `bin/console` | Production / runtime ops | repo root | Uses `src/vendor/autoload.php` (the main app) |
| `devTools/core/console.php` | Developer-only | `devTools/core/` | Has its own `composer.json` + `vendor/` |

Both extend `Symfony\Component\Console\Application` (via OHRM's thin `OrangeHRM\Framework\Console\Console` subclass). They wire up commands differently — see "Where commands live" below.

This skill covers writing new commands, registering them, and choosing the right console. For specific existing commands, see `migrations` (`instance:*`, `migration:up`), `dev-environment` (`i:create-test-db`), `rest-openapi` (`generate-open-api-doc`), `scheduled-jobs` (`orangehrm:run-schedule`).

## The two consoles

### `bin/console` — production runtime

`bin/console` lives at the repo root and only depends on the main app's autoload. It's safe to run anywhere the app is deployed.

```php
// bin/console (simplified)
require_once $pathToAutoload;
new Framework($env, $debug);                                       // boot the framework

$console = new Console();
foreach ($pluginConfigs as $pluginConfig) {                        // iterate registered plugins
    $configClass = new $pluginConfig['classname']();
    $configClass->initialize($request);
    if ($configClass instanceof ConsoleConfigurationInterface) {
        $configClass->registerCommands($console);                  // ← plugins surface their commands
    }
}
$console->add(new CacheClearCommand());
$console->add(new GenerateDoctrineProxiesCommand());
$console->run();
```

Commands available in `bin/console` come from two sources:
1. Plugins that implement `ConsoleConfigurationInterface` and surface commands in `registerCommands(Console $console)`.
2. The hardcoded `CacheClearCommand` and `GenerateDoctrineProxiesCommand` that `bin/console` adds directly (used by composer's `post-autoload-dump`).

```bash
php bin/console list                    # see all commands
php bin/console cache:clear
php bin/console orm:generate-proxies
php bin/console orangehrm:run-schedule      # cron scheduler — see scheduled-jobs skill
```

### `devTools/core/console.php` — developer tools

`devTools/core/` is a **separate composer project** with its own `composer.json` and `vendor/`. It depends on the main app's autoload at runtime but isolates dev dependencies (PHP-CS-Fixer, swagger-php).

```bash
php devTools/core/console.php list
php devTools/core/console.php php-cs-fix
php devTools/core/console.php instance:create-test-db -p root
php devTools/core/console.php instance:reset
php devTools/core/console.php instance:reinstall
php devTools/core/console.php migration:up "\OrangeHRM\Installer\Migration\V5_9_0\Migration"
php devTools/core/console.php add-data-group
php devTools/core/console.php add-role-permission
php devTools/core/console.php generate-open-api-doc --throw
php devTools/core/console.php event-dispatcher:debug
```

Commands live in `devTools/core/src/Command/` and are registered directly in `devTools/core/console.php` (no plugin discovery). **This console isn't deployed to production** — `devTools/` is excluded from the build artifacts.

### Which console to put a new command in

| If your command… | Use |
|---|---|
| Is needed in production (ops, scheduled jobs, cache management, migration assistance) | `bin/console` |
| Is for developers only (code generation, test setup, lint, doc generation) | `devTools/core/console.php` |
| Is invoked by `composer install`'s `post-autoload-dump` | `bin/console` (it's the one composer reaches) |
| Should be available in an offline / CI environment without dev deps | `bin/console` |
| Touches dev-only dependencies (PHPUnit fixtures, swagger-php, php-cs-fixer) | `devTools/core/console.php` |

When in doubt: **`devTools/core/` for tools the team uses while developing, `bin/console` for tools that production ops needs**.

## The `Command` base class

`OrangeHRM\Framework\Console\Command` is a thin wrapper around Symfony's `Command`:

```php
abstract class Command extends \Symfony\Component\Console\Command\Command
{
    protected ?SymfonyStyle $io = null;

    public function __construct()
    {
        parent::__construct($this->getCommandName());          // ← name comes from abstract method
    }

    protected function getIO(): SymfonyStyle
    {
        return $this->io;
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $this->setIO($input, $output);                          // ← auto-sets $this->io before execute()
        return parent::run($input, $output);
    }

    abstract public function getCommandName(): string;          // ← subclasses implement
}
```

Three things to note:

1. **`getCommandName(): string`** is abstract — every subclass provides its name. Symfony's `setName()` is set automatically from this.
2. **`$this->getIO()`** returns a `SymfonyStyle` ready-to-use — no need to construct one yourself in `execute()`. It's set up automatically before `execute()` runs.
3. Symfony's standard `Command` is still the parent — `configure()`, `execute(InputInterface, OutputInterface)`, `addArgument`, `addOption` all work as usual.

## Anatomy of a typical command

```php
<?php
namespace OrangeHRM\X\Command;

use OrangeHRM\Framework\Console\Command;
use OrangeHRM\X\Traits\Service\WidgetServiceTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupWidgetsCommand extends Command
{
    use WidgetServiceTrait;                                      // ← compose like a service

    public function getCommandName(): string
    {
        return 'widget:cleanup';                                 // ← <namespace>:<action>
    }

    protected function configure(): void
    {
        $this->setDescription('Delete orphaned widgets')
            ->addArgument('olderThan', InputArgument::OPTIONAL, 'In days', '30')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Preview without deleting');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $olderThan = (int) $input->getArgument('olderThan');
        $dryRun    = (bool) $input->getOption('dry-run');

        $count = $this->getWidgetService()->countOrphaned($olderThan);
        $this->getIO()->info("Found $count orphaned widgets older than $olderThan days");

        if ($dryRun) {
            $this->getIO()->note('Dry run — nothing deleted');
            return self::SUCCESS;
        }

        if (!$this->getIO()->confirm('Delete them?', false)) {
            $this->getIO()->warning('Cancelled');
            return self::INVALID;
        }

        $deleted = $this->getWidgetService()->deleteOrphaned($olderThan);
        $this->getIO()->success("Deleted $deleted widgets");
        return self::SUCCESS;
    }
}
```

Standard pattern observed across all OHRM commands:
- Extends `OrangeHRM\Framework\Console\Command`
- `getCommandName()` returns the user-typed name (e.g. `widget:cleanup`)
- `configure()` declares arguments, options, description
- `execute()` reads input, calls services via Traits, uses `$this->getIO()` for output, returns `self::SUCCESS` / `self::FAILURE` / `self::INVALID`
- Compose traits exactly like services do — `WidgetServiceTrait`, `EventDispatcherTrait`, `LoggerTrait`, etc.

### Naming conventions

Commands use Symfony's standard `namespace:action` shape. Conventional namespaces in OHRM:

| Namespace | Use |
|---|---|
| `cache:*` | Cache management (`cache:clear`) |
| `orm:*` | Doctrine operations (`orm:generate-proxies`) |
| `instance:*` | DB/app instance management (`instance:reset`, `instance:reinstall`, `instance:create-test-db`). `i:*` is the shorthand alias. |
| `migration:*` | Migration utilities (`migration:up`) |
| `orangehrm:*` | Cron scheduler (`orangehrm:run-schedule`) |
| `<plugin>:<action>` | Plugin-specific commands (`leave:export`, `ldap:sync-user`) |
| Single word | Top-level commands like `php-cs-fix`, `add-data-group`, `add-role-permission`, `generate-open-api-doc`, `event-dispatcher:debug` — used in `devTools/core/console.php` where there's no need for a plugin namespace |

When choosing, **match an existing namespace if your command logically fits there**; reach for a new one only for genuinely new domains.

## Registering commands for `bin/console`

Commands surface in `bin/console` via the **plugin's `<Plugin>PluginConfiguration` class implementing `ConsoleConfigurationInterface`** in addition to `PluginConfigurationInterface`.

```php
namespace OrangeHRM\X;

use OrangeHRM\Framework\Console\Console;
use OrangeHRM\Framework\Console\ConsoleConfigurationInterface;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\X\Command\CleanupWidgetsCommand;

class XPluginConfiguration implements PluginConfigurationInterface, ConsoleConfigurationInterface
{
    public function initialize(Request $request): void
    {
        // … normal service registration …
    }

    public function registerCommands(Console $console): void
    {
        $console->add(new CleanupWidgetsCommand());
    }
}
```

Then:

```bash
php bin/console widget:cleanup
```

Multiple commands? Multiple `$console->add()` calls.

### Conditional registration (e.g. dev-only)

Wrap in a Config check:

```php
public function registerCommands(Console $console): void
{
    $console->add(new RunScheduleCommand());                     // always available
    if (Config::PRODUCT_MODE !== Config::MODE_PROD) {
        $console->add(new EnableTestLanguagePackCommand());      // dev only
    }
}
```

The core plugin's `CorePluginConfiguration::registerCommands()` does this for the test-language-pack command. Commands that depend on dev-only state should be gated this way (or moved to `devTools/core/`).

## Registering commands for `devTools/core/console.php`

Different mechanism — no plugin scan. Edit `devTools/core/console.php` directly and add to the `$application->add(...)` calls. Same pattern as `bin/console`, just without the plugin abstraction.

```php
// devTools/core/console.php (representative)
$application = new Console();
$application->add(new PHPFixCodingStandardsCommand());
$application->add(new GenerateOpenApiDocCommand());
$application->add(new CreateTestDatabaseCommand());
$application->add(new ResetInstallationCommand());
$application->add(new ReInstallCommand());
$application->add(new AddDataGroupCommand());
$application->add(new AddRolePermissionCommand());
$application->add(new RunMigrationClassCommand());
$application->add(new EventDispatcherDebugCommand());
$application->run();
```

For a new dev-tool command, drop the class in `devTools/core/src/Command/` and add one line to `devTools/core/console.php`.

## SymfonyStyle helpers — `$this->getIO()`

`SymfonyStyle` (returned by `getIO()`) has rich output helpers:

```php
$this->getIO()->title('Cleanup widgets');
$this->getIO()->section('Phase 1: discovery');

$this->getIO()->success('Done');                                 // green box
$this->getIO()->error('Failed');                                 // red box
$this->getIO()->warning('Risk');                                 // yellow box
$this->getIO()->note('FYI');                                     // grey box
$this->getIO()->info('FYI');                                     // blue
$this->getIO()->caution('Heads up');                             // red banner

$this->getIO()->writeln('Plain text line');
$this->getIO()->write('No newline');

// Interactive
$value = $this->getIO()->ask('Enter value', 'default');
$value = $this->getIO()->ask('Enter value', null, fn ($v) => $v ?: throw new \InvalidArgumentException('Required'));
$password = $this->getIO()->askHidden('Password');
$yes = $this->getIO()->confirm('Proceed?', false);

$choice = $this->getIO()->choice('Pick', ['a', 'b', 'c']);

$this->getIO()->table(['Col1', 'Col2'], [['a', 1], ['b', 2]]);
$this->getIO()->listing(['item 1', 'item 2', 'item 3']);

$bar = $this->getIO()->createProgressBar(100);
// $bar->advance(); $bar->finish();
```

Use these consistently — they match how every other OHRM command formats output. Don't roll your own colored output via `<info>` tags unless `SymfonyStyle` doesn't cover what you need.

### Interactive vs non-interactive

Commands often need to support both interactive use (devs at terminal) and non-interactive (CI, scripts). Symfony provides `$input->isInteractive()`:

```php
if ($input->isInteractive() && !$input->hasOption('confirmed')) {
    if (!$this->getIO()->confirm('Are you sure?', false)) {
        return self::INVALID;
    }
}
```

The installer commands (`install:on-new-database`, `upgrade:run`) check this — interactive mode prompts, non-interactive mode requires every option as a flag.

## Composing helpers in commands

Commands can `use` any of the trait helpers that services and subscribers use — `LoggerTrait`, `ConfigServiceTrait`, `EventDispatcherTrait`, plugin-specific `*ServiceTrait`. Same DI container, same lazy-getter mechanics.

```php
class SyncCommand extends Command
{
    use EmployeeServiceTrait;
    use ConfigServiceTrait;
    use LoggerTrait;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getLogger()->info('Sync started');
        $batchSize = (int) $this->getConfigService()->getConfigValue('sync.batch_size', '100');
        $employees = $this->getEmployeeService()->getActiveEmployees();
        // …
    }
}
```

See `services` skill for the full trait pattern.

## Existing OHRM dev commands (devTools/core)

Already documented in their respective skills, but the catalog:

| Command | Skill it lives in |
|---|---|
| `php-cs-fix` | (linting; mentioned in CLAUDE.md) |
| `generate-open-api-doc --throw` | `rest-openapi` |
| `instance:create-test-db` (`i:create-test-db`) | `dev-environment`, `testing` |
| `instance:reset` (`i:reset`) | `migrations` |
| `instance:reinstall` (`i:reinstall`) | `migrations` |
| `migration:up <Class>` | `migrations` |
| `add-data-group` | `authorization` |
| `add-role-permission` | `authorization` |
| `event-dispatcher:debug` | this skill (it lists all subscriber registrations — useful for debugging "did my subscriber register?") |

## Existing OHRM bin/console commands

From the core plugin's `registerCommands()`:

| Command | What it does |
|---|---|
| `cache:clear` | Clears the symfony Cache adapters (orangehrm, doctrine_metadata, doctrine_queries) |
| `orm:generate-proxies` | Regenerates Doctrine proxy classes (see `doctrine-bootstrap`) |
| `orangehrm:run-schedule` | Runs all scheduled tasks for the current time (see `scheduled-jobs`) |
| `orangehrm:enable-test-lang-pack` | Dev-only — enables a test language pack |

Plus per-plugin commands like `ldap:sync-user` from the LDAP plugin.

---

# Recipes

## Recipe 1 — A new bin/console command for a feature plugin

```php
<?php
namespace OrangeHRM\X\Command;

use OrangeHRM\Framework\Console\Command;
use OrangeHRM\X\Traits\Service\WidgetServiceTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WidgetExportCommand extends Command
{
    use WidgetServiceTrait;

    public function getCommandName(): string
    {
        return 'widget:export';
    }

    protected function configure(): void
    {
        $this->setDescription('Export widgets to CSV')
             ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Output file', 'widgets.csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getOption('path');
        $count = $this->getWidgetService()->exportToCsv($path);
        $this->getIO()->success("Exported $count widgets to $path");
        return self::SUCCESS;
    }
}
```

Register in `XPluginConfiguration`:

```php
class XPluginConfiguration implements PluginConfigurationInterface, ConsoleConfigurationInterface
{
    public function registerCommands(Console $console): void
    {
        $console->add(new WidgetExportCommand());
    }
}
```

Run:

```bash
php bin/console widget:export --path=/tmp/out.csv
```

## Recipe 2 — A dev-only command in devTools

```php
<?php
namespace OrangeHRM\DevTools\Command;

use OrangeHRM\Framework\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedDevDataCommand extends Command
{
    public function getCommandName(): string
    {
        return 'dev:seed';
    }

    protected function configure(): void
    {
        $this->setDescription('Seed development fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // … insert dev fixtures …
        $this->getIO()->success('Seeded');
        return self::SUCCESS;
    }
}
```

Edit `devTools/core/console.php`:

```php
$application->add(new SeedDevDataCommand());
```

Run:

```bash
php devTools/core/console.php dev:seed
```

## Recipe 3 — Interactive command with arg validation

```php
class ImportFileCommand extends Command
{
    public function getCommandName(): string
    {
        return 'data:import';
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::OPTIONAL, 'CSV file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');

        if (!$path && $input->isInteractive()) {
            $path = $this->getIO()->ask('Path to CSV', null, function ($value) {
                if (!is_file($value)) {
                    throw new \InvalidArgumentException("File not found: $value");
                }
                return $value;
            });
        }

        if (!$path) {
            $this->getIO()->error('--path is required in non-interactive mode');
            return self::INVALID;
        }

        // … process …
        return self::SUCCESS;
    }
}
```

The pattern: optional argument, prompt for it interactively if missing, fail with a clear message if missing and non-interactive.

## Recipe 4 — Command that runs another command (sub-command invocation)

```php
class FullResetCommand extends Command
{
    public function getCommandName(): string
    {
        return 'dev:full-reset';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $resetCmd = $this->getApplication()->find('instance:reset');
        $reinstallCmd = $this->getApplication()->find('instance:reinstall');

        $resetCmd->run(new \Symfony\Component\Console\Input\ArrayInput([]), $output);
        $reinstallCmd->run(new \Symfony\Component\Console\Input\ArrayInput([]), $output);

        return self::SUCCESS;
    }
}
```

Use `$this->getApplication()->find('command:name')->run()` to invoke another command. Each gets its own `ArrayInput` for arguments.

---

# Checklists

## Add a new bin/console command

- [ ] Class in `src/plugins/orangehrm{X}Plugin/Command/<Name>Command.php` extending `OrangeHRM\Framework\Console\Command`
- [ ] `getCommandName()` returns the user-typed name (`<namespace>:<action>` convention)
- [ ] `configure()` for arguments + options + description
- [ ] `execute()` reads input, uses `$this->getIO()` for output, returns `self::SUCCESS|FAILURE|INVALID`
- [ ] Compose any services/helpers via Traits (`WidgetServiceTrait`, `LoggerTrait`, etc.)
- [ ] Plugin's `<Plugin>PluginConfiguration` implements `ConsoleConfigurationInterface` and registers the command in `registerCommands(Console $console)`
- [ ] Test by running `php bin/console list` — your command should appear

## Add a new devTools command

- [ ] Class in `devTools/core/src/Command/<Name>Command.php` extending `OrangeHRM\Framework\Console\Command`
- [ ] Standard `getCommandName`, `configure`, `execute`
- [ ] Add `$application->add(new <Name>Command())` to `devTools/core/console.php`
- [ ] Test: `php devTools/core/console.php list`

## Things that bite

- **Forgetting `ConsoleConfigurationInterface`** — the plugin must implement both `PluginConfigurationInterface` AND `ConsoleConfigurationInterface` for `registerCommands()` to be called. Implementing only the second silently fails (the framework only checks for both).
- **Forgetting to register in `devTools/core/console.php`** — for dev-tool commands, there's no plugin scan. The class file alone isn't enough; the `$application->add(...)` line is mandatory.
- **Mixing dev dependencies into a `bin/console` command** — if your command pulls in something only present in `devTools/core/vendor/`, it'll fail on production deploys. Keep `bin/console` commands using only what's in `src/vendor/`.
- **`$this->getIO()` returns null before `execute()`** — the framework sets it up in `run()`, but if your `configure()` tries to use it, you'll get a TypeError. `configure()` runs without IO; do all IO inside `execute()`.
- **`composer install`'s `post-autoload-dump` runs `cache:clear` and `orm:generate-proxies`.** If you break either of those commands, every `composer install` afterwards fails. Test these specifically when modifying them.
- **Commands run with no HTTP request context** — composables expecting `getCurrentInstance()` or session won't work. Most things do (DI container, EM, traits) but some Vue-side helpers and request-coupled subscribers might not.
- **`getCommandName()` typos are silent.** If you misspell `widget:expott`, `bin/console list` shows it as-is and `bin/console widget:export` says "command not found." The typed name and the constant should match.
- **In dev mode the cache is `ArrayAdapter`** — `cache:clear` clears nothing visible because nothing was persisted. The command still reports success. Use it in prod where it actually matters.
