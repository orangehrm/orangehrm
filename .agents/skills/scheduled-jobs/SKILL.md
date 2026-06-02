---
name: scheduled-jobs
description: Reference for OrangeHRM's task scheduling — the `orangehrm:run-schedule` command that operators run from system cron (every minute), the `SchedulerConfigurationInterface` that plugins implement to register tasks at framework boot, the `Schedule` / `Task` / `CommandInfo` value objects (Task extends `Crunz\Event`, so all Crunz fluent scheduling methods like `->cron()`, `->everyMinute()`, `->hourly()`, `->daily()`, `->weekdays()`, `->between()`, etc. are available), and the `TaskSchedulerLog` entity that records every task execution. Use whenever the user is adding a recurring background task, wiring up cron, debugging a job that should have run but didn't, or asking about setting up the host-level cron entry. Companion to `console-commands` (scheduled jobs are console commands underneath), `events` (the same plugin-config-implements-interface pattern), `mail` (a common scheduled task is draining queued emails on hosts where it can't happen via TERMINATE).
---

# Scheduled jobs

OrangeHRM has its own task scheduler built on top of Crunz. The flow:

```
System cron (host-level)
  → runs `php bin/console orangehrm:run-schedule` every minute

orangehrm:run-schedule (RunScheduleCommand)
  → iterates every plugin
  → if plugin implements SchedulerConfigurationInterface, calls plugin->schedule($schedule)
  → plugin adds Task objects to the Schedule with cron expressions
  → command computes which tasks are "due" right now
  → for each due task: run it, log to ohrm_task_scheduler_log table
```

The framework abstracts Crunz behind the `Schedule`, `Task`, `CommandInfo` value objects. Plugin code defines *what to schedule*; the framework handles *which tasks are due now*.

This skill covers the full pattern. For the underlying console command mechanics, see `console-commands`.

## The host-level cron entry

**The operator sets up one system cron entry**, called every minute:

```
* * * * * www-data php /var/www/html/bin/console orangehrm:run-schedule >> /dev/null 2>&1
```

That's it. The framework decides per minute which tasks are due. Operators don't add new cron lines when developers add new tasks — they configure it once and it picks up everything plugins register.

For Docker setups, the dev environment's compose file may include a separate cron container or a `*/1 * * * *` task running in the PHP container. **In dev**, you can invoke it manually:

```bash
docker exec -it os_dev_php83 php /var/www/<ohrm-checkout>/bin/console orangehrm:run-schedule --verbose
```

The `--verbose` flag flips it from `NullOutput` to actual output so you can see what's running.

## `SchedulerConfigurationInterface` — the plugin contract

Same pattern as `ConsoleConfigurationInterface` and `PluginConfigurationInterface`. The plugin's `<Plugin>PluginConfiguration` class implements it in addition to whatever else:

```php
namespace OrangeHRM\Framework\Console\Scheduling;

interface SchedulerConfigurationInterface
{
    public function schedule(Schedule $schedule): void;
}
```

`RunScheduleCommand::execute()` iterates all plugin configurations and calls `schedule()` on any that implement this interface:

```php
foreach ($pluginConfigs as $pluginConfig) {
    $configClass = new $pluginConfig['classname']();
    if ($configClass instanceof SchedulerConfigurationInterface) {
        $configClass->schedule($schedule);                       // ← plugin registers tasks
    }
}
$dueTasks = $schedule->getDueTasks(new DateTimeZone('UTC'));
foreach ($dueTasks as $task) {
    $task->start();                                              // ← run each due task
    // … log to ohrm_task_scheduler_log
}
```

## The real example — LDAP sync

`LDAPAuthenticationPluginConfiguration` implements all three interfaces (`PluginConfigurationInterface`, `ConsoleConfigurationInterface`, `SchedulerConfigurationInterface`):

```php
class LDAPAuthenticationPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    public function initialize(Request $request): void { /* … */ }

    public function registerCommands(Console $console): void
    {
        $console->add(new LDAPSyncUserCommand());                // see console-commands skill
    }

    public function schedule(Schedule $schedule): void
    {
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        if ($ldapSettings instanceof LDAPSetting && $ldapSettings->isEnable()) {
            $interval = 1;
            if ($ldapSettings->getSyncInterval() <= 23 && $ldapSettings->getSyncInterval() >= 1) {
                $interval = $ldapSettings->getSyncInterval();
            }

            $schedule->add(new CommandInfo('orangehrm:ldap-sync-user'))
                ->cron("0 */$interval * * *");                   // every N hours, at minute 0
        }
    }
}
```

Three things to notice:

1. **`registerCommands` adds the command** (so `bin/console orangehrm:ldap-sync-user` runs it manually).
2. **`schedule` registers the SAME command on a cron schedule** — the scheduler doesn't need a separate "scheduled task" class. It schedules an existing command.
3. **The schedule is conditional** — only registered if LDAP is enabled in config. Disabled features don't waste cron evaluations.

The two parts (registerCommands + schedule) are independent. You can have a command that's only ever scheduled (no manual invocation needed), or a command that's only manual (never scheduled). The most useful pattern is "scheduled but also manually invokable" — like LDAP sync.

## The fluent scheduling API — `Task extends Crunz\Event`

`Task` extends `Crunz\Event`, so **every method on Crunz's `Event` is available** when defining a schedule:

```php
$schedule->add(new CommandInfo('widget:cleanup'))
    ->daily()                                                    // run once per day at 00:00 UTC
    ->at('02:30');                                               // change time to 02:30 UTC
```

Common scheduling methods:

| Method | Schedule |
|---|---|
| `->cron('* * * * *')` | Raw cron expression (5-field) |
| `->everyMinute()` | Every minute |
| `->everyFiveMinutes()` / `->everyTenMinutes()` / `->everyThirtyMinutes()` | At fixed intervals |
| `->hourly()` | Every hour at minute 0 |
| `->daily()` | Every day at 00:00 |
| `->dailyAt('14:30')` | Every day at 14:30 |
| `->weekly()` | Every Sunday at 00:00 |
| `->monthly()` | First of the month at 00:00 |
| `->yearly()` | January 1st at 00:00 |
| `->weekdays()` | Mon-Fri (combine with one of the above) |
| `->weekends()` | Sat-Sun |
| `->mondays()`, `->tuesdays()`, etc. | Specific days |
| `->between('09:00', '17:00')` | Only between these times of day |
| `->timezone('Asia/Colombo')` | Interpret the schedule in this timezone |

**Combine** to build the schedule:

```php
$schedule->add(new CommandInfo('widget:report'))
    ->weekdays()
    ->dailyAt('09:00')
    ->timezone('Asia/Colombo');
```

For the full Crunz vocabulary, see `vendor/crunzphp/crunz` — it's a well-documented library.

### Passing arguments to a scheduled command

Use `CommandInfo`'s second argument:

```php
use OrangeHRM\Framework\Console\ArrayInput;
use OrangeHRM\Framework\Console\Scheduling\CommandInfo;

$schedule->add(new CommandInfo('widget:cleanup', new ArrayInput(['--days' => '30'])))
    ->daily();
```

The `ArrayInput` is OHRM's thin wrapper that preserves the raw parameters for logging in `ohrm_task_scheduler_log`.

## Time zone — UTC matters

`RunScheduleCommand::execute()` calls `getDueTasks(new DateTimeZone('UTC'))` — **scheduling is evaluated in UTC by default.** A `->dailyAt('09:00')` runs at 09:00 UTC, not 09:00 local time.

To run a task at a specific local time, set the timezone on the task:

```php
$schedule->add(new CommandInfo('widget:cleanup'))
    ->dailyAt('09:00')
    ->timezone('Asia/Colombo');                                  // 09:00 in Colombo = 03:30 UTC
```

This matters because the cron runs `RunScheduleCommand` every minute, but each task's "due" check uses its own timezone. The scheduler is timezone-aware per task.

## Logging — `ohrm_task_scheduler_log`

Every task execution is logged in `ohrm_task_scheduler_log`:

```sql
SELECT id, command, input, started_at, finished_at, status FROM ohrm_task_scheduler_log
WHERE command = 'orangehrm:ldap-sync-user' ORDER BY id DESC LIMIT 5;
```

Columns:
- `command` — the command name that ran
- `input` — raw parameters (the `ArrayInput::getRawParameters()` output)
- `started_at`, `finished_at` — UTC timestamps
- `status` — the command's exit code (0 = SUCCESS, non-zero = FAILURE)

**Use this for audit and debugging.** "Did the email queue drain last night?" → query the log.

Rows are kept indefinitely — no automatic cleanup. If your instance accumulates a lot of scheduled runs, consider a cleanup task that itself runs daily and prunes old log rows (meta!).

## What happens on errors

If a scheduled task throws:

```php
try {
    return $this->consoleCommand->run($input, $this->commandOutput);
} catch (Throwable $e) {
    $logger = LoggerFactory::getLogger('scheduler');
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
    return Command::FAILURE;
}
```

The exception is caught, logged to `src/log/scheduler.log`, and the task is recorded as FAILURE in `ohrm_task_scheduler_log`. **Other tasks in the same `run-schedule` invocation still run.** One failing task doesn't block the rest.

The plugin's `schedule()` method is also wrapped:

```php
try {
    $configClass->schedule($schedule);
} catch (Throwable $e) {
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
}
```

A plugin throwing during `schedule()` (e.g. its config service threw) doesn't break scheduling for other plugins.

## Common scheduled tasks in the codebase

| Plugin | Command | Schedule | Use |
|---|---|---|---|
| LDAP | `orangehrm:ldap-sync-user` | Hourly (interval configurable) | Sync users from the LDAP server |
| Various | (others added as features need) | | |

Most current-codebase scheduling is LDAP-focused. The pattern is reusable for anything: nightly reports, attendance roll-ups, queue draining, data exports, cleanup jobs.

---

# Recipes

## Recipe 1 — Schedule an existing command to run nightly

You already have a `widget:cleanup` command (see `console-commands` skill). To schedule it nightly at 02:00 UTC:

```php
class XPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    use ConfigServiceTrait;

    public function initialize(Request $request): void { /* … */ }

    public function registerCommands(Console $console): void
    {
        $console->add(new CleanupWidgetsCommand());
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->add(new CommandInfo('widget:cleanup'))
            ->dailyAt('02:00');
    }
}
```

That's the entire recipe — add `SchedulerConfigurationInterface` to the implements list, add the `schedule()` method, and add a Task with a Crunz fluent schedule.

## Recipe 2 — Schedule conditional on config

When a task should only run if a feature is enabled:

```php
public function schedule(Schedule $schedule): void
{
    if (!$this->getConfigService()->isWidgetCleanupEnabled()) {
        return;
    }
    $schedule->add(new CommandInfo('widget:cleanup'))
        ->dailyAt('02:00')
        ->timezone('UTC');
}
```

This is the LDAP pattern — `schedule()` checks config and only registers the task if it's enabled. **No task = no cron evaluation = no log row when disabled.**

## Recipe 3 — Schedule with arguments

```php
public function schedule(Schedule $schedule): void
{
    $schedule->add(new CommandInfo(
        'widget:cleanup',
        new ArrayInput(['--days' => '90', '--force' => true]),
    ))->weekly();
}
```

The `ArrayInput` is logged as the command's `input` field in `ohrm_task_scheduler_log`, so you can trace exactly what each scheduled invocation called with.

## Recipe 4 — Multiple tasks from one plugin

```php
public function schedule(Schedule $schedule): void
{
    $schedule->add(new CommandInfo('widget:cleanup'))
        ->dailyAt('02:00');

    $schedule->add(new CommandInfo('widget:report-weekly'))
        ->mondays()
        ->dailyAt('06:00');

    $schedule->add(new CommandInfo('widget:sync-external'))
        ->everyTenMinutes();
}
```

Each `$schedule->add(...)` produces a separate Task with its own due-time calculation. They run independently.

## Recipe 5 — Drain the email queue on hosts where TERMINATE doesn't fire

The mail skill mentions `MailerSubscriber` drains on `KernelEvents::TERMINATE`. **CLI invocations don't go through the kernel** — so emails queued by a console command never drain via the subscriber.

A scheduled task can flush the queue periodically:

```php
public function schedule(Schedule $schedule): void
{
    $schedule->add(new CommandInfo('orangehrm:flush-email-queue'))
        ->everyFiveMinutes();
}
```

Pair with a custom `FlushEmailQueueCommand` that calls `EmailService::sendQueuedEmails()` directly. Not currently shipped, but the pattern is straightforward.

---

# Checklists

## Schedule an existing command

- [ ] Plugin's config class adds `SchedulerConfigurationInterface` to its `implements` list
- [ ] Add `public function schedule(Schedule $schedule): void` method
- [ ] Inside: `$schedule->add(new CommandInfo('command:name'))->dailyAt(...)` (or other Crunz fluent method)
- [ ] If conditional, wrap with a Config check
- [ ] Test locally: `docker exec ... php bin/console orangehrm:run-schedule --verbose` — your task should appear if it's due now, or you can temporarily change the schedule to `->everyMinute()` to force a run
- [ ] Confirm a row appears in `ohrm_task_scheduler_log` after running

## Add a new schedulable task (command + schedule)

- [ ] Write the `Command` class (see `console-commands` skill)
- [ ] In `<Plugin>PluginConfiguration::registerCommands()`, add the command so it's manually invokable
- [ ] In `<Plugin>PluginConfiguration::schedule()`, add the task with its schedule
- [ ] Run `bin/console <command:name>` to verify manual invocation works
- [ ] Run `bin/console orangehrm:run-schedule --verbose` to verify it's picked up by the scheduler

## Set up the host cron entry (one-time, per deployment)

- [ ] Add the line: `* * * * * www-data php /path/to/orangehrm/bin/console orangehrm:run-schedule >> /dev/null 2>&1`
- [ ] Adjust user (`www-data` on Apache, `nginx` on Nginx setups) to whoever owns the application files
- [ ] Adjust path to the actual deployment location
- [ ] Verify cron is running (`systemctl status cron` or equivalent)
- [ ] Wait 1-2 minutes, check `ohrm_task_scheduler_log` for the first auto-run

## Debug "my scheduled task didn't run"

- [ ] **Was the host cron line running?** Check system mail / `/var/log/cron` for the cron entry firing
- [ ] **Did `orangehrm:run-schedule` execute?** Run it manually with `--verbose` and see if it reports "Event count: 0" — that means no tasks were due, not that yours wasn't registered
- [ ] **Is the plugin config registered?** Verify the plugin's `PluginConfiguration` class implements `SchedulerConfigurationInterface` — only then does `RunScheduleCommand` call its `schedule()` method
- [ ] **Is the schedule conditional?** If wrapped in `if ($enabled)`, verify the config value
- [ ] **Time zone issue?** A `->dailyAt('09:00')` with no `->timezone()` runs at 09:00 UTC. If you wanted local time, add `->timezone('Region/City')`
- [ ] **Plugin error during `schedule()`?** Check `src/log/scheduler.log` — exceptions during plugin scheduling are caught and logged but don't show in the command's stdout
- [ ] **Task error during run?** Check `ohrm_task_scheduler_log` for the row — `status` column has the exit code; non-zero means failure. Then `src/log/scheduler.log` for the trace

## Things that bite

- **`orangehrm:run-schedule` itself is filtered out** — `Schedule::add()` throws if you try to schedule it. Don't try to self-schedule the runner.
- **All scheduling is evaluated in UTC by default.** A `->dailyAt('09:00')` is 09:00 UTC. Set `->timezone()` per task for local times.
- **CLI invocations don't trigger `KernelEvents::TERMINATE`** — the mail queue drain via `MailerSubscriber` won't run from a scheduled task. Use a dedicated drain command if you need email-from-scheduled-tasks.
- **The host cron entry is operator responsibility** — devs adding a scheduled task don't change anything on the host. If the cron isn't set up, no scheduled task runs. Check this first when "scheduled" features mysteriously don't fire.
- **`schedule()` runs once per `orangehrm:run-schedule` invocation** (i.e., every minute). Don't do expensive work in it — only register tasks. Heavy logic belongs in the command itself.
- **Multiple `orangehrm:run-schedule` runs in parallel** (if the cron entry is mis-configured to overlap) can cause duplicate task executions. Crunz has overlap-prevention but it's per-process and host-level — the safest bet is a single cron entry, every minute, no `&` background.
- **Plugin `schedule()` exceptions are caught and logged**, but they're silent in the command's output unless `--verbose`. A typo'd config service call won't crash anything visible — just won't schedule the task. Check `src/log/scheduler.log`.
- **`ohrm_task_scheduler_log` grows indefinitely.** No built-in pruning. If your scheduler runs frequently, expect this table to bloat over months/years. Consider adding a scheduled task that prunes old log rows.
