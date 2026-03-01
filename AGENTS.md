# OrangeHRM Development Guide for AI Coding Agents

This document provides essential information for AI coding agents working on the OrangeHRM codebase.

## Project Overview

OrangeHRM is a comprehensive HR Management System built with:
- **Backend**: PHP 7.4+/8.0+ with custom Symfony-based framework, Doctrine ORM
- **Frontend**: Vue 3 + TypeScript, custom component library (@ohrm/oxd)
- **Architecture**: Plugin-based modular system with 22 HR module plugins
- **License**: GPL v3

## Build, Test & Lint Commands

### PHP Backend

```bash
# Install dependencies
composer install -d src

# Run all PHPUnit tests
phpunit

# Run tests for specific plugin/suite
phpunit --testsuite Admin
phpunit --testsuite Leave
phpunit --testsuite Core

# Run a specific test file
phpunit src/plugins/orangehrmAdminPlugin/test/Api/EducationAPITest.php

# Run a specific test method
phpunit --filter testGetAll src/plugins/orangehrmAdminPlugin/test/Api/EducationAPITest.php

# Check PHP code style
bin/console php-cs-fix --dry-run

# Fix PHP code style
bin/console php-cs-fix

# Generate Doctrine proxies
bin/console orm:generate-proxies

# Clear cache
bin/console cache:clear
```

### Vue/TypeScript Frontend

```bash
# Install dependencies (use Yarn, not npm)
cd src/client
yarn install

# Development server
yarn serve

# Development build with watch mode
yarn dev

# Production build
yarn build

# Run Jest unit tests
yarn test:unit

# Lint (fails on any warnings)
yarn lint

# For installer client
cd installer/client
yarn install
yarn dev / yarn build / yarn lint
```

### E2E Tests (Cypress)

```bash
cd src/test/functional
yarn install

# Run Cypress tests headless
yarn test

# Open Cypress interactive mode
yarn open

# Lint functional tests
yarn lint
```

## Code Style Guidelines

### PHP Standards

**File Headers**: All PHP files must include GPL v3 license header (see example files)

**PSR-12 Compliance**: Code follows PSR-12 standard with these additions:
- Array syntax: Use short syntax `[]` not `array()`
- No unused imports
- Nullable type declarations for default null values: `?Type $var = null`
- Doctrine annotation indentation enabled

**Namespacing**:
- Production code: `OrangeHRM\{PluginName}\{Layer}\{ClassName}`
- Test code: `OrangeHRM\Tests\{PluginName}\{Layer}\{ClassName}`
- Examples:
  - `OrangeHRM\Admin\Service\CompanyStructureService`
  - `OrangeHRM\Core\Api\V2\Endpoint`
  - `OrangeHRM\Tests\Admin\Api\EducationAPITest`

**Naming Conventions**:
- Classes: PascalCase (e.g., `CompanyStructureService`, `EducationAPI`)
- Methods: camelCase (e.g., `getSubunitById`, `saveSubunit`)
- Properties: camelCase with type hints
- Constants: UPPER_SNAKE_CASE
- Test files: `{ClassName}Test.php`

**Type Declarations**:
- Always use type hints for parameters and return types
- Use nullable types: `?Type` or union types where appropriate
- Use typed properties with visibility: `private ?CompanyStructureDao $dao = null;`

**Documentation**:
- DocBlocks for all public/protected methods with `@param` and `@return` tags
- Class-level DocBlocks for complex classes
- No inline comments unless explaining complex logic

**Error Handling**:
- Use custom exceptions from `OrangeHRM\Core\Api\V2\Exception\*`
- Common exceptions: `RecordNotFoundException`, `InvalidParamException`, `ForbiddenException`, `BadRequestException`
- Validation uses `OrangeHRM\Core\Api\V2\Validator\*` classes

### TypeScript/Vue Standards

**File Headers**: All .vue and .ts files must include GPL v3 license header in comments

**ESLint Configuration**:
- Extends: Vue 3 recommended, TypeScript recommended, Prettier
- Max warnings: 0 (no warnings allowed)
- ECMAScript: 2020

**Prettier Configuration**:
```javascript
{
  bracketSpacing: false,
  jsxBracketSameLine: true,
  singleQuote: true,
  trailingComma: 'all',
}
```

**Naming Conventions**:
- Components: PascalCase filenames (e.g., `SubmitButton.vue`)
- Files: kebab-case for non-components (e.g., `datefns.ts`)
- Test files: `{name}.spec.ts` in `__tests__/` directory
- Variables/functions: camelCase
- Constants: UPPER_SNAKE_CASE
- Types/Interfaces: PascalCase

**Vue 3 Patterns**:
- Use `<script setup>` or Options API (both acceptable, check existing code in module)
- Component names must be PascalCase
- Props: Use TypeScript types or PropType
- Emits: Declare all emits explicitly
- Composables: Use `use*` prefix (e.g., `useForm`, `useDateFormat`)

**Import Organization**:
1. Vue/external libraries
2. Internal dependencies from `@ohrm/*` or `@/*` aliases
3. Relative imports
4. Type imports should use `import type`

**TypeScript**:
- Strict mode enabled
- Avoid `any` type - use proper types or `unknown`
- Use interfaces for object shapes
- Path aliases: `@/*` and `@ohrm/*` resolve to `src/*`

## Plugin Architecture

OrangeHRM has 22 plugins in `src/plugins/orangehrm{Name}Plugin/`. Standard plugin structure:

```
orangehrm{Name}Plugin/
├── Api/                    # REST API endpoints (V2)
│   └── V2/
├── Controller/             # Web controllers
├── Dao/                    # Data Access Objects
├── Service/                # Business logic layer
├── entity/                 # Doctrine entities
├── Dto/                    # Data Transfer Objects
├── Traits/                 # Shared traits
├── config/                 # Plugin configuration
├── Menu/                   # Menu configurations
└── test/                   # Unit tests
```

**Key Patterns**:
- **Service Layer**: Business logic in `Service/` classes, injected via DI
- **DAO Pattern**: Database access in `Dao/` classes
- **API Layer**: REST endpoints extend `Endpoint`, `CrudEndpoint`, `CollectionEndpoint`, or `ResourceEndpoint`
- **Entities**: Doctrine entities in `entity/` directory with annotations
- **Validation**: Use `ParamRule` and validation rules from `Core\Api\V2\Validator\Rules`

## Testing Patterns

### PHPUnit Tests

- Location: `src/plugins/{plugin}/test/`
- Extend: `EndpointIntegrationTestCase` for API tests, `TestCase` for unit tests
- Fixtures: YAML files alongside test files (e.g., `EducationAPITest.yml`)
- Test data: Use `TestDataService` for database setup
- Groups: Use `@group` annotations (e.g., `@group Admin`, `@group APIv2`)
- Data providers: Name with `dataProviderFor{TestName}` pattern
- Method naming: `test{MethodName}` (e.g., `testGetAll`, `testCreate`)

### Jest Tests

- Location: `src/client/src/**/__tests__/*.spec.ts`
- Test structure: `describe()` blocks for grouping, `test()` or `it()` for cases
- Vue Testing: Use `@vue/test-utils` for component tests
- Coverage: HTML reports generated

### Cypress Tests

- Location: `src/test/functional/cypress/e2e/`
- Database helpers: Custom tasks for `db:reset`, `db:snapshot`, `db:restore`, `db:truncate`
- Snapshots enable test isolation with database savepoints

## Common Tasks

### Adding a New API Endpoint

1. Create class in `src/plugins/{plugin}/Api/V2/{Name}API.php`
2. Extend `CrudEndpoint`, `CollectionEndpoint`, or `ResourceEndpoint`
3. Implement required methods: `getOne`, `getAll`, `create`, `update`, `delete`
4. Add validation rules in `get{Method}ValidationRules()` methods
5. Register in plugin configuration
6. Add test class in `src/plugins/{plugin}/test/Api/{Name}APITest.php`

### Adding a New Service

1. Create class in `src/plugins/{plugin}/Service/{Name}Service.php`
2. Add DAO dependency with getter/setter pattern
3. Implement business logic methods
4. Add type hints for all methods
5. Register in DI container if needed
6. Add unit tests in `test/Service/{Name}ServiceTest.php`

### Adding a Vue Component

1. Create `.vue` file in appropriate directory (e.g., `src/client/src/core/components/`)
2. Add GPL license header in comment block
3. Use PascalCase for component name
4. Add TypeScript types for props/emits
5. Follow existing component patterns (check @ohrm/oxd components)
6. Add unit test in `__tests__/{name}.spec.ts`

## Important Notes

- **No multi-word warning**: Vue multi-word component name rule is disabled
- **Zero warnings policy**: All linting must pass with `--max-warnings=0`
- **Strict typing**: Both PHP and TypeScript use strict type checking
- **GPL v3**: All files must include GPL v3 license header
- **Yarn only**: Use Yarn 4.1.0 for package management, not npm
- **PSR-4 autoloading**: Follow namespace conventions strictly
- **Test coverage**: Maintain test coverage for all new code
- **Database migrations**: Use migration system for schema changes (see `installer/Migration/`)

## Console Commands Reference

```bash
bin/console list                        # List all commands
bin/console orm:generate-proxies        # Generate Doctrine proxies
bin/console cache:clear                 # Clear application cache
bin/console i:create-test-db            # Create test database
bin/console i:reinstall                 # Reinstall application
bin/console php-cs-fix                  # Fix PHP coding standards
bin/console generate-open-api-doc       # Generate API documentation
```

## File Locations Quick Reference

- PHP source: `src/plugins/{plugin}/`
- Vue source: `src/client/src/`
- PHP tests: `src/plugins/{plugin}/test/`
- Vue tests: `src/client/src/**/__tests__/`
- E2E tests: `src/test/functional/cypress/e2e/`
- Entities: `src/plugins/{plugin}/entity/`
- Migrations: `installer/Migration/`
- Config: `src/config/`
- Web root: `web/`
- Built assets: `web/dist/` (gitignored)

## CI/CD

GitHub Actions run:
- PHP CS Fixer (code style)
- PHPUnit (with coverage)
- Jest unit tests
- ESLint for all Vue/TS code
- Cypress E2E tests
- API documentation generation

All checks must pass before merging.
