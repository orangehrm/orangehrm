---
name: testing
description: Reference for OrangeHRM's test layers — PHPUnit per-plugin testsuites declared in `phpunit.xml`, the test-DB lifecycle (`instance:create-test-db` builds a populated MySQL DB plus a `CoreFixtureService` dump that bootstrap restores per test), test base classes (`TestCase` for plain unit tests, `KernelTestCase` for tests that need the full framework + DI container, `EntityTestCase` for entity-only tests, `EndpointTestCase` and `EndpointIntegrationTestCase` for API endpoint tests with request mocking + exception expectations), the YAML fixture pattern (per-plugin `test/fixtures/<DaoName>.yml` + `TestDataService::populate($yamlPath)` in `setUp()`), Jest configuration for frontend unit tests (`@vue/cli-plugin-unit-jest/presets/typescript-and-babel`, `__tests__/` siblings), and Cypress for E2E (separate workspace under `src/test/functional/`). Use whenever the user is writing a test, deciding which base class to extend, debugging fixture loading, setting up the test DB, running a single test class, or trying to figure out why a test that worked locally fails in CI. Companion to `dev-environment` (`instance:create-test-db` setup), `migrations` (the test DB is a migrated fresh DB), `daos` (DAO tests are the most common kind), `rest-endpoints` (endpoint tests).
---

# Testing in OrangeHRM

OrangeHRM tests fall into four buckets:

1. **PHPUnit unit / integration tests** — `src/plugins/orangehrm{X}Plugin/test/` (per-plugin)
2. **Jest frontend unit tests** — `src/client/src/**/__tests__/*.spec.ts` (mostly util-function tests; component tests are rare)
3. **Cypress E2E** — `src/test/functional/cypress/` (separate workspace, browser-driven)
4. **Migration / installer / linting smoke tests** — covered by CI workflows (see `dev-environment` skill for the workflow list)

The strongest testing convention is **integration-style DAO tests with YAML fixtures hitting a real test database**. Pure unit tests with mocks are rarer — the codebase deliberately doesn't mock the database. This skill covers all four buckets but focuses on the PHPUnit patterns since that's where most code lives.

## The test database

`src/test/phpunit/Util/bootstrap.php` is PHPUnit's bootstrap. It:

1. Connects to the test DB (configured the same way as the main DB, but via the test env)
2. Bails out with "Run `php devTools/core/console.php i:create-test-db ...`" if the DB isn't ready
3. Uses `CoreFixtureService::isReady()` to check if the test fixtures have been seeded
4. Otherwise, every test class is responsible for loading its own fixtures via `TestDataService::populate()`

### Creating the test DB (one-time setup)

```bash
php devTools/core/console.php instance:create-test-db -p root --dump-options=--ssl=0
# or the shorthand alias:
php devTools/core/console.php i:create-test-db -p root --dump-options=--ssl=0
```

What it does:
1. Creates a fresh MySQL database (default name `ohrm_test`)
2. Runs the full migration chain from V3_3_3 through PRODUCT_VERSION (see `migrations` skill)
3. Seeds OrangeHRM core fixtures (countries, currencies, predefined roles, etc.) via `CoreFixtureService`
4. **Dumps the populated DB to a SQL file** in `src/test/phpunit/fixtures/` for fast restore in subsequent tests

The `--dump-options=--ssl=0` flag is for `mysqldump` compatibility — see `dev-environment` skill.

**Run this once before running tests for the first time, and whenever migrations change the schema.** CI runs it on every test build.

### CI matrix and `dev-environment`

The CI test matrix (in `.github/workflows/test.yml`) runs:
- MySQL 5.7
- MariaDB 10.3

On PHP 8.3. **Tests pass in CI but fail locally?** Often a DB-version-specific issue — try running locally against the matching DB version via the dev environment's `mariadb103` / `mysql57` containers.

## PHPUnit configuration — `phpunit.xml` at the repo root

```xml
<testsuites>
  <testsuite name="Admin"><directory>src/plugins/orangehrmAdminPlugin/test</directory></testsuite>
  <testsuite name="Pim"><directory>src/plugins/orangehrmPimPlugin/test</directory></testsuite>
  <testsuite name="Leave"><directory>src/plugins/orangehrmLeavePlugin/test/Dao</directory><directory>src/plugins/orangehrmLeavePlugin/test</directory></testsuite>
  <!-- … one per plugin -->
</testsuites>
```

Each plugin gets its own testsuite. To run one:

```bash
./src/vendor/bin/phpunit --testsuite Admin
./src/vendor/bin/phpunit --testsuite Pim
./src/vendor/bin/phpunit --testsuite Core
```

To run a single file or method:

```bash
./src/vendor/bin/phpunit src/plugins/orangehrmAdminPlugin/test/Dao/JobTitleDaoTest.php
./src/vendor/bin/phpunit --filter testGetJobTitleList
./src/vendor/bin/phpunit --filter 'JobTitleDaoTest::testGetJobTitleList'
```

Bootstrap is `src/test/phpunit/Util/bootstrap.php` — explicitly set in `phpunit.xml`. PHPUnit's `convertErrorsToExceptions`, `convertNoticesToExceptions`, `convertWarningsToExceptions` are all enabled — **a stray PHP warning fails a test**. This is intentional.

## Per-plugin test layout

Mirror of the plugin's source layout:

```
src/plugins/orangehrm{X}Plugin/test/
  Api/                  ← API endpoint tests (extend EndpointTestCase)
    Model/              ← Model normalization tests
  Authorization/        ← Permission tests
  Controller/           ← Page controller tests (rare)
  Dao/                  ← DAO tests (the most common kind) (extend TestCase)
  Entity/               ← Entity tests (extend EntityTestCase)
  Service/              ← Service tests
  fixtures/             ← YAML fixture files (one per test class, typically)
    <DaoName>.yml
  testCases/            ← Data provider files
```

Tests under `Tests\<Plugin>\` namespace (from the `autoload-dev` in `src/composer.json`).

## Test base classes

All in `OrangeHRM\Tests\Util\`. Pick based on what you need.

### `TestCase` — the default

Plain PHPUnit `TestCase` extension. Use for **simple unit tests** that don't need the DI container or framework boot. Most DAO tests use this — they instantiate the DAO directly and let it talk to the test DB via the same EntityManager singleton.

```php
namespace OrangeHRM\Tests\Admin\Dao;

use OrangeHRM\Admin\Dao\JobTitleDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

class JobTitleDaoTest extends TestCase
{
    private $jobTitleDao;
    protected $fixture;

    protected function setUp(): void
    {
        $this->jobTitleDao = new JobTitleDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmAdminPlugin/test/fixtures/JobTitleDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetJobTitleList(): void
    {
        $jobTitles = $this->jobTitleDao->getJobTitleList();
        $this->assertCount(3, $jobTitles);
        // …
    }
}
```

Key patterns visible here:
- `setUp()` loads a YAML fixture via `TestDataService::populate($yamlPath)`
- Uses `Config::get(Config::PLUGINS_DIR)` to resolve the fixture path portably
- Tests instantiate the DAO with `new`, no DI

### `KernelTestCase` — full framework boot

When the test needs the framework to be running — services that depend on `Services::DOCTRINE` access via the DI container, subscribers, anything that calls `ServiceContainer::getContainer()->get(...)`.

```php
abstract class KernelTestCase extends TestCase
{
    use ServiceContainerTrait;

    public const OPTIONS_WITH_HELPER_SERVICES = 'withHelperServices';
    public const OPTIONS_WITH_BASE_SERVICES = 'withBaseServices';

    protected function tearDown(): void
    {
        $this->getEntityManager()->clear();
        $this->createKernel();        // ← re-create kernel between tests
    }

    protected function createKernel(): Framework { /* … */ }
    protected function getHttpRequest(/* … */): Request { /* … */ }
}
```

`createKernel()` boots a full `Framework` instance (the HttpKernel subclass — see `doctrine-bootstrap`) with the DI container, all plugins initialized, all subscribers registered. The container is fresh per test (via `tearDown`).

Two options on the kernel test:
- `OPTIONS_WITH_HELPER_SERVICES` — registers `DateTimeHelperService`, `NumberHelperService`, `TextHelperService`, etc.
- `OPTIONS_WITH_BASE_SERVICES` — registers base infrastructure services

Use this when your code path involves traits like `ConfigServiceTrait` or `DateTimeHelperTrait` that fetch from the DI container.

### `EntityTestCase` — entity validation only

For tests that verify entity getters/setters, validation, and relations without needing the framework. Lighter than `KernelTestCase`. Used in `*/test/Entity/` directories.

### `EndpointTestCase` — REST endpoint tests with request mocking

`OrangeHRM\Tests\Util\EndpointTestCase` extends `KernelTestCase` and adds API-test conveniences:

```php
abstract class EndpointTestCase extends KernelTestCase
{
    use ValidatorTrait;

    protected function getRequest(array $query = [], array $request = [], array $attributes = []): Request
    {
        // builds a Core\Api\V2\Request with the given params
    }

    protected function getApiEndpointMockBuilder(string $apiClassName, array $requestParams = []): MockBuilder
    {
        // builds a mock of the endpoint with a real request
    }

    protected function expectNotImplementedException(): void { /* … */ }
    protected function expectRecordNotFoundException(): void { /* … */ }
    protected function expectBadRequestException(): void { /* … */ }
    protected function expectForbiddenException(): void { /* … */ }
    protected function expectInvalidParamException(): void { /* … */ }
}
```

Use for tests of API endpoint classes. The `expectXxxException()` helpers wrap PHPUnit's `expectException` for the common API exception types (see `rest-endpoints` skill for the full list).

### `EndpointIntegrationTestCase` — full request-cycle endpoint tests

`OrangeHRM\Tests\Util\EndpointIntegrationTestCase`. The heaviest — runs the request through the full HTTP kernel, including all subscribers (auth, authorization, exception handling). Used when you need to test the integration as a whole, not just the endpoint method.

Tests using this are slower but verify the auth + authorization + serialization layers together. Pair with the `Integration/TestCaseParams.php` data-provider helper.

## YAML fixtures and `TestDataService`

Fixtures are YAML files representing rows of data:

```yaml
# src/plugins/orangehrmAdminPlugin/test/fixtures/JobTitleDao.yml
JobTitle:
  -
    id: 1
    jobTitleName: 'Software Engineer'
    jobDescription: 'Develops software'
    isDeleted: false
  -
    id: 2
    jobTitleName: 'Project Manager'
    isDeleted: false
  -
    id: 3
    jobTitleName: 'Old Title'
    isDeleted: true     # soft-deleted
```

`TestDataService::populate($yamlPath)`:
1. Parses the YAML
2. For each entity type, **truncates the corresponding table**
3. Inserts the rows
4. Returns

**The truncate is full** — calling `populate()` wipes other test data of the same entity type. Each test class typically populates exactly what it needs in `setUp()`.

Fixture per test class is the convention. Don't share one fixture file across multiple tests unless they really do need the same data and you've thought through the truncate semantics.

### Core fixtures — pre-loaded

`CoreFixtureService` (run by `instance:create-test-db`) seeds:
- All countries, currencies, nationalities (the lookup tables)
- All user roles (Admin, ESS, Supervisor, etc.)
- All data groups + role permissions (see `authorization` skill — these come from the migrations)
- All screens + role permissions
- All workflow state machine rows (see `workflow` skill — also from migrations)
- All i18n lang strings + groups

These are present in the test DB at boot and **stay between tests**. Your YAML fixtures add domain data on top.

## Writing a DAO test (the most common kind)

```php
namespace OrangeHRM\Tests\X\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\X\Dao\WidgetDao;
use OrangeHRM\X\Dto\WidgetSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group X
 * @group Dao
 */
class WidgetDaoTest extends TestCase
{
    private WidgetDao $dao;
    private string $fixture;

    protected function setUp(): void
    {
        $this->dao = new WidgetDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmXPlugin/test/fixtures/WidgetDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetWidgetById(): void
    {
        $widget = $this->dao->getWidgetById(1);
        $this->assertNotNull($widget);
        $this->assertEquals('Test widget', $widget->getName());
    }

    public function testGetWidgetListFiltered(): void
    {
        $params = new WidgetSearchFilterParams();
        $params->setName('Test');
        $widgets = $this->dao->getWidgetList($params);
        $this->assertCount(2, $widgets);
    }

    public function testSaveWidget(): void
    {
        $widget = new Widget();
        $widget->setName('New');
        $saved = $this->dao->saveWidget($widget);
        $this->assertNotNull($saved->getId());

        $retrieved = $this->dao->getWidgetById($saved->getId());
        $this->assertEquals('New', $retrieved->getName());
    }
}
```

Conventions:
- `@group <Plugin>` + `@group <Layer>` (Dao / Service / Api / Entity) — lets `phpunit --group Dao` run all DAO tests across plugins
- `setUp()` always loads a fresh fixture
- Constructor uses `new` directly (no DI)
- Real DB operations — no mocking the EM

## Writing a Service test

Services are tested two ways:

### 1. Unit-style with DAO mocking

```php
class WidgetServiceTest extends TestCase
{
    public function testSaveWidgetDispatchesEvent(): void
    {
        $mockDao = $this->createMock(WidgetDao::class);
        $mockDao->expects($this->once())->method('saveWidget')->willReturn(new Widget());

        $service = new WidgetService();
        $service->setWidgetDao($mockDao);                    // ← test-injection setter

        // … assert event was dispatched, etc.
    }
}
```

The `setXxxDao()` setter on every service (see `services` skill) exists exactly for this — inject a mock to isolate the service from the DAO.

### 2. Integration-style with real DB

When the service composes several DAOs or fires events that have to be observed, the integration-style test is cleaner:

```php
class WidgetServiceIntegrationTest extends KernelTestCase
{
    public function testSaveTriggersEvent(): void
    {
        $this->createKernel();
        TestDataService::populate(/* … */);

        $captured = null;
        $this->getEventDispatcher()->addListener(WidgetEvents::WIDGET_SAVED, function ($event) use (&$captured) {
            $captured = $event;
        });

        $service = new WidgetService();
        $service->saveWidget(new Widget(/* … */));

        $this->assertInstanceOf(WidgetSavedEvent::class, $captured);
    }
}
```

`KernelTestCase` gives you a real event dispatcher to subscribe to.

## Writing an Endpoint test

```php
class WidgetAPITest extends EndpointTestCase
{
    public function testGetOneReturnsWidget(): void
    {
        TestDataService::populate(/* … */);

        $endpoint = new WidgetAPI($this->getRequest(
            [],                                              // query
            [],                                              // body
            [CommonParams::PARAMETER_ID => 1],               // attributes
        ));

        $result = $endpoint->getOne();
        $data = $result->normalize();

        $this->assertEquals(1, $data['data']['id']);
    }

    public function testGetOneNotFoundThrows(): void
    {
        $endpoint = new WidgetAPI($this->getRequest(
            [], [], [CommonParams::PARAMETER_ID => 99999]
        ));
        $this->expectRecordNotFoundException();
        $endpoint->getOne();
    }

    public function testValidationRuleForCreate(): void
    {
        $rules = (new WidgetAPI($this->getRequest()))->getValidationRuleForCreate();
        $this->expectInvalidParamException();
        $this->validate(['name' => ''], $rules);            // empty name → fail
    }
}
```

The `validate()` from `ValidatorTrait` runs the same validator the REST framework runs (see `rest-validation` skill). Use it to test that validation rule collections produce the expected pass/fail behavior.

## Writing an Entity test

```php
class WidgetTest extends EntityTestCase
{
    public function testSetGetName(): void
    {
        $widget = new Widget();
        $widget->setName('Test');
        $this->assertEquals('Test', $widget->getName());
    }

    public function testCollectionInitialized(): void
    {
        $widget = new Widget();
        $this->assertInstanceOf(ArrayCollection::class, $widget->getTags());
    }
}
```

Used for entity-level invariants — getter/setter symmetry, constructor initialization, computed properties on entities. Doesn't need DB.

## Frontend testing — Jest

`jest.config.js`:

```js
module.exports = {
    preset: '@vue/cli-plugin-unit-jest/presets/typescript-and-babel',
    transform: {
        '^.+\\.vue$': '@vue/vue3-jest',
    },
    coverageReporters: ['html'],
};
```

Tests live in `__tests__/` siblings to the file being tested:

```
src/client/src/core/util/
  helper/
    datefns.ts
    __tests__/
      datefns.spec.ts
  validation/
    rules.ts
    __tests__/
      rules.spec.ts
```

Run:
```bash
cd src/client
yarn test:unit                                # all
yarn test:unit path/to/file.spec.ts           # one file
yarn test:unit --coverage                     # with coverage report
```

**The frontend testing surface is light.** Most tests cover util functions (validation rules, date helpers, file size, URL builders, year-range). Vue component tests exist but are rare. **Don't propose adding component tests unless asked** — the precedent in the codebase is to extract testable logic into util functions and unit-test those.

Sample util test:

```ts
import {required, shouldNotExceedCharLength} from '../rules';

describe('validation rules', () => {
    test('required passes for non-empty string', () => {
        expect(required('hello')).toBe(true);
    });

    test('required fails for empty string', () => {
        expect(typeof required('')).toBe('string');           // returns error message string
    });

    test('shouldNotExceedCharLength', () => {
        expect(shouldNotExceedCharLength(5)('hello')).toBe(true);
        expect(typeof shouldNotExceedCharLength(5)('too long')).toBe('string');
    });
});
```

## End-to-end testing — Cypress

`src/test/functional/` is a **separate yarn workspace** with its own `package.json`:

```bash
cd src/test/functional
yarn install                                 # one-time
yarn test                                    # headless run
yarn open                                    # interactive Cypress UI
yarn lint                                    # ESLint
```

Cypress 13. Tests live in `cypress/e2e/`. Page objects in `cypress/support/`. Custom commands in `cypress/support/commands.ts`.

E2E tests require:
- A running OrangeHRM instance (locally served)
- The instance to be installed and seeded with known credentials
- The Cypress config pointing at the right base URL

**Run E2E locally rarely** — they're slow and fragile compared to PHPUnit tests. CI runs them on a schedule, not on every PR.

## What's tested vs. not — observed convention

| Layer | Test density | Style |
|---|---|---|
| DAOs | Heavy | Integration, with YAML fixtures, real DB |
| Services | Medium | Mix of mocked DAOs and integration |
| API endpoints | Medium | EndpointTestCase, request param mocking |
| Validators (custom rules) | Heavy | Direct rule instantiation + value-based assertions |
| Entities | Light | Mostly getter/setter symmetry |
| Decorators | Light | Mostly happy-path |
| Migrations | None | Verified by running them in CI; no direct test |
| Event subscribers | Light | Mostly via service integration tests |
| Page controllers | Very light | Mostly indirectly via E2E |
| Vue components | Very light | Util functions, not components |
| Cron/scheduled tasks | None | Verified by running the underlying command's tests |

**When adding code, follow the precedent** — if you're adding a DAO method, write a DAO test with a YAML fixture. If you're adding a service method that orchestrates events, write an integration test with `KernelTestCase` + real dispatcher.

---

# Recipes

## Recipe 1 — Set up the test environment (first time)

```bash
# 1. Make sure the test DB is created (one-time per OS or after migrations change)
php devTools/core/console.php instance:create-test-db -p root --dump-options=--ssl=0

# 2. Run all tests
./src/vendor/bin/phpunit

# 3. Run just one plugin's tests
./src/vendor/bin/phpunit --testsuite Admin

# 4. Run just one test class
./src/vendor/bin/phpunit src/plugins/orangehrmAdminPlugin/test/Dao/JobTitleDaoTest.php

# 5. Run just one method
./src/vendor/bin/phpunit --filter testGetJobTitleList
```

In Docker dev environment (see `dev-environment` skill), run all of these inside the PHP container:

```bash
docker exec -it os_dev_php83 bash -c "cd /var/www/<ohrm-checkout> && php devTools/core/console.php i:create-test-db -p root"
docker exec -it os_dev_php83 bash -c "cd /var/www/<ohrm-checkout> && ./src/vendor/bin/phpunit --testsuite Admin"
```

## Recipe 2 — Write a DAO test with fixtures

Create `src/plugins/orangehrmXPlugin/test/fixtures/WidgetDao.yml`:

```yaml
Widget:
  -
    id: 1
    name: 'Widget Alpha'
    isActive: true
  -
    id: 2
    name: 'Widget Beta'
    isActive: false
```

Then the test:

```php
namespace OrangeHRM\Tests\X\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\X\Dao\WidgetDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group X
 * @group Dao
 */
class WidgetDaoTest extends TestCase
{
    private WidgetDao $dao;
    private string $fixture;

    protected function setUp(): void
    {
        $this->dao = new WidgetDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmXPlugin/test/fixtures/WidgetDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetByIdReturnsActiveWidget(): void
    {
        $w = $this->dao->getWidgetById(1);
        $this->assertNotNull($w);
        $this->assertEquals('Widget Alpha', $w->getName());
    }
}
```

Run:
```bash
./src/vendor/bin/phpunit src/plugins/orangehrmXPlugin/test/Dao/WidgetDaoTest.php
```

## Recipe 3 — Test a validation rule

```php
namespace OrangeHRM\Tests\X\Api;

use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Tests\Util\TestCase;

class WidgetValidationTest extends TestCase
{
    public function testEmailRulePasses(): void
    {
        $rule = new Rule(Rules::EMAIL);
        $validator = new ($rule->getClass())(...$rule->getConstructorArgs());
        $this->assertTrue($validator->validate('test@example.com'));
    }

    public function testEmailRuleFailsForInvalid(): void
    {
        $rule = new Rule(Rules::EMAIL);
        $validator = new ($rule->getClass())(...$rule->getConstructorArgs());
        $this->assertFalse($validator->validate('not-an-email'));
    }
}
```

Each rule class has a `validate($input): bool` method (see `rest-validation` skill). Test directly without needing the full validator pipeline.

## Recipe 4 — Test an API endpoint method

```php
namespace OrangeHRM\Tests\X\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\X\Api\WidgetAPI;

class WidgetAPITest extends EndpointTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TestDataService::populate(/* path to fixture */);
    }

    public function testGetOneReturnsWidgetById(): void
    {
        $endpoint = new WidgetAPI($this->getRequest(
            [],                                                  // query
            [],                                                  // body
            [CommonParams::PARAMETER_ID => 1],                   // attributes (path params)
        ));

        $result = $endpoint->getOne();
        $normalized = $result->normalize();

        $this->assertEquals(1, $normalized['data']['id']);
    }

    public function testGetOneThrowsForMissing(): void
    {
        $endpoint = new WidgetAPI($this->getRequest([], [], [
            CommonParams::PARAMETER_ID => 999999,
        ]));
        $this->expectRecordNotFoundException();
        $endpoint->getOne();
    }
}
```

## Recipe 5 — Mock a DAO inside a service test

```php
class WidgetServiceTest extends TestCase
{
    public function testSaveCallsDaoAndDispatches(): void
    {
        $widget = new Widget();
        $widget->setName('Test');

        $mockDao = $this->createMock(WidgetDao::class);
        $mockDao->expects($this->once())
            ->method('saveWidget')
            ->with($this->isInstanceOf(Widget::class))
            ->willReturnCallback(function (Widget $w) {
                $w->setId(42);
                return $w;
            });

        $service = new WidgetService();
        $service->setWidgetDao($mockDao);                       // ← key — every service has setDao()

        $result = $service->saveWidget($widget);
        $this->assertEquals(42, $result->getId());
    }
}
```

The `setXxxDao()` pattern is one of the reasons services exist as plain classes with lazy-getter setters (see `services` skill) — it makes unit tests cheap.

---

# Checklists

## Set up for testing

- [ ] One-time: run `instance:create-test-db` (see `dev-environment` skill)
- [ ] Re-run after schema changes (after a new migration)
- [ ] Make sure you can run `./src/vendor/bin/phpunit --testsuite Core` without errors before writing new tests
- [ ] In Docker: run all phpunit commands inside the PHP container, not the host

## Write a DAO test

- [ ] Test class in `src/plugins/orangehrm{X}Plugin/test/Dao/<Name>DaoTest.php` under namespace `OrangeHRM\Tests\<Plugin>\Dao`
- [ ] Extends `OrangeHRM\Tests\Util\TestCase` (not the framework KernelTestCase — DAO tests don't need the DI container)
- [ ] `@group <Plugin>` + `@group Dao` annotations
- [ ] `setUp()` instantiates the DAO with `new` and populates a YAML fixture
- [ ] Fixture at `test/fixtures/<DaoName>.yml`
- [ ] Tests use the DAO's real methods against the test DB

## Write an Endpoint test

- [ ] Test class extends `OrangeHRM\Tests\Util\EndpointTestCase`
- [ ] Use `$this->getRequest($query, $body, $attributes)` to build a Request
- [ ] Instantiate the endpoint with the request, call its methods
- [ ] Use `expectRecordNotFoundException()` / `expectBadRequestException()` / `expectInvalidParamException()` for error cases
- [ ] Use `$this->validate($values, $rules)` from `ValidatorTrait` to test validation rule collections

## Write a Service test

- [ ] Decide unit-style (mock DAO via `setXxxDao()`) or integration-style (`KernelTestCase` + real DB)
- [ ] For events: subscribe via `addListener` on the test dispatcher in setUp to capture them
- [ ] For external dependencies (other services), use the `setXxxService()` setter to inject a mock

## Debug "test passes locally but fails in CI"

- [ ] Run against the matching DB version locally (`mariadb103` or `mysql57` container — see `dev-environment` skill)
- [ ] Check `phpunit.xml` — strict error/notice/warning conversion is on; a notice in your code = fail
- [ ] Check if your test relies on data from a previous test that ran in a different order — every test should `populate` its own fixture in `setUp`
- [ ] Time-zone differences — many tests use `DateTime` without a TZ; CI runs in UTC, you might not. Use `DateTimeHelperService::TIMEZONE_UTC` explicitly.

## Things that bite

- **`TestDataService::populate()` truncates the table** before inserting. If your test depends on data from a previous test, it's gone. Always re-populate in `setUp()`.
- **Test DB stays seeded across tests** — `CoreFixtureService` data (countries, roles, permissions) is shared. Your fixtures **must not** insert rows that conflict with the core data (e.g. inserting a `UserRole` with id=1 will collide with the Admin role).
- **Strict mode is on** — `convertErrorsToExceptions`, `convertNoticesToExceptions`, `convertWarningsToExceptions`. A `Notice: Undefined index` in production code makes tests fail.
- **Bootstrap refuses to run** without `instance:create-test-db` having been called. The error message is explicit; if you ignore it, every test fails to load.
- **`@group` annotations are aspirational** — they let you filter (`phpunit --group Dao`) but don't change test ordering or isolation.
- **`KernelTestCase::tearDown` clears the EM and re-creates the kernel.** That makes each test start clean but adds overhead. For DAO tests that don't need the kernel, **use plain `TestCase`** — it's faster.
- **Frontend tests for Vue components are rare.** The convention is "extract logic into util functions, test those." Don't propose testing components unless explicitly asked.
- **Cypress E2E is slow + fragile.** Don't add E2E tests for simple functionality that PHPUnit + an endpoint test can cover — reserve E2E for true end-to-end user journeys.
- **The CI test matrix** runs on MySQL 5.7 AND MariaDB 10.3 + PHP 8.3. SQL or PHP that works in only one of these will fail CI. Most issues stem from charset / collation differences between MySQL and MariaDB.
- **No mocking of EntityManager** — the codebase deliberately tests DAOs against the real DB. Don't try to mock `Doctrine\ORM\EntityManager` — it has too many methods and the mocks fall out of sync with reality. Use the test DB.
