# Duon CMS Testing Guide

This guide explains how to set up and run tests for the Duon CMS project.

## Test Architecture

The test suite combines **unit tests** for business logic and **integration tests** for database interactions:

- **Unit Tests**: Fast tests for isolated components (lexer, parser, utilities, field capabilities)
- **Integration Tests**: Tests that interact with a real PostgreSQL database

### Key Principles

1. **No Mocks in Integration Tests**: Integration tests use real database connections and actual data
2. **Transaction Isolation**: Each integration test runs in a transaction that's rolled back after completion
3. **Fixture-Based**: Tests use SQL fixtures and helper methods for consistent test data
4. **Hybrid Setup**: Database schema is initialized once per test run, then transactions provide isolation

## Prerequisites

### 1. PostgreSQL Setup

The test suite requires a PostgreSQL database. Ensure PostgreSQL is installed and running:

```bash
# Check PostgreSQL status
sudo systemctl status postgresql

# Start if not running
sudo systemctl start postgresql
```

### 2. Database User

Create a PostgreSQL user for testing:

```bash
# Create user with CREATEDB privilege
sudo -u postgres createuser -d duoncms

# Set password
sudo -u postgres psql -c "ALTER USER duoncms WITH PASSWORD 'duoncms';"
```

### 3. Initialize Test Database

Create and initialize the test database:

```bash
# Create the database
./run recreate-db

# Apply all migrations
./run migrate --apply
```

The `recreate-db` command:
- Terminates existing connections to the database
- Drops the database if it exists
- Creates a fresh database
- Sets the owner to `duoncms`

## Running Tests

### Run All Tests

```bash
vendor/bin/phpunit
```

### Run Specific Test Suite

```bash
# Run a specific test file
vendor/bin/phpunit tests/NodeIntegrationTest.php

# Run a specific test method
vendor/bin/phpunit --filter testCreateAndRetrieveNode tests/NodeIntegrationTest.php
```

### Run Only Unit Tests

```bash
vendor/bin/phpunit --exclude-group integration
```

### Run Only Integration Tests

```bash
vendor/bin/phpunit --group integration
```

*Note: Currently tests are not tagged with groups. This is a future enhancement.*

### Generate Coverage Report

```bash
vendor/bin/phpunit --coverage-html coverage
```

Open `coverage/index.html` in your browser to view the report.

## Test Structure

### Directory Layout

```
tests/
├── Setup/
│   ├── TestCase.php              # Base class for all tests
│   └── IntegrationTestCase.php   # Base class for integration tests
├── Fixtures/
│   ├── data/                     # SQL fixture files
│   │   ├── basic-types.sql       # Common content types
│   │   ├── test-users.sql        # Test user accounts
│   │   └── sample-nodes.sql      # Sample content nodes
│   └── Node/                     # Test node classes
│       ├── TestDocument.php
│       └── TestMediaDocument.php
├── *Test.php                     # Unit test files
└── *IntegrationTest.php          # Integration test files
```

### Base Test Classes

#### `TestCase`

Base class for all tests, provides:
- **Database helpers**: `conn()`, `db()`, `config()`, `registry()`
- **HTTP helpers**: `request()`, `psrRequest()`, `setMethod()`, `setRequestUri()`
- **Fixture loading**: `loadFixtures(...$fixtures)`
- **Test data creation**: `createTestType()`, `createTestNode()`, `createTestUser()`
- **Database initialization**: Checks schema exists on first test class run
- **Transaction support**: Optionally wraps tests in transactions (controlled by `$useTransactions`)

#### `IntegrationTestCase`

Extends `TestCase` for integration tests, provides:
- **Automatic transaction isolation**: Sets `$useTransactions = true`
- **Context creation**: `createContext($localeId = 'en')`
- **Finder creation**: `createFinder($localeId = 'en')`

## Writing Tests

### Unit Test Example

```php
<?php

namespace Duon\Cms\Tests;

use Duon\Cms\Tests\Setup\TestCase;

final class PasswordTest extends TestCase
{
    public function testPasswordHashing(): void
    {
        $password = 'secret123';
        $hash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertTrue(password_verify($password, $hash));
    }
}
```

### Integration Test Example

```php
<?php

namespace Duon\Cms\Tests;

use Duon\Cms\Tests\Setup\IntegrationTestCase;

final class MyIntegrationTest extends IntegrationTestCase
{
    public function testNodeCreation(): void
    {
        // Arrange
        $typeId = $this->createTestType('my-test-type', 'page');

        // Act
        $nodeId = $this->createTestNode([
            'type' => $typeId,
            'content' => ['title' => ['type' => 'text', 'value' => ['en' => 'Test']]],
        ]);

        // Assert
        $node = $this->db()->execute(
            'SELECT * FROM cms.nodes WHERE node = :id',
            ['id' => $nodeId]
        )->one();

        $this->assertNotNull($node);
        $this->assertEquals($typeId, $node['type']);
    }
}
```

### Using Fixtures

```php
public function testWithFixtures(): void
{
    // Load SQL fixtures
    $this->loadFixtures('basic-types', 'sample-nodes');

    // Use Finder to query fixture data
    $finder = $this->createFinder();
    $nodes = $finder->nodes()->types('test-page')->get();

    $this->assertNotEmpty($nodes);
}
```

## Test Database Workflow

### How Transaction Isolation Works

1. **First test class runs** → Database schema is checked (one-time)
2. **Test begins** → Transaction starts (`BEGIN`)
3. **Test executes** → All database operations happen in transaction
4. **Test completes** → Transaction rolls back (`ROLLBACK`)
5. **Next test begins** → Clean database state (transaction starts)

This ensures:
- ✅ Each test has a clean database state
- ✅ No test data persists between tests
- ✅ Tests can run in any order
- ✅ Fast execution (no database recreation)

### When to Recreate the Database

Recreate the test database when:
- Migrations have been added or modified
- Database structure has changed
- Tests are failing due to schema issues
- You want a completely fresh start

```bash
./run recreate-db && ./run migrate --apply
```

## Troubleshooting

### "Test database not initialized"

**Error:**
```
RuntimeException: Test database not initialized. Run: ./run recreate-db && ./run migrate --apply
```

**Solution:**
```bash
./run recreate-db && ./run migrate --apply
```

### "Migrations not applied"

**Error:**
```
RuntimeException: Migrations not applied to test database. Run: ./run migrate --apply
```

**Solution:**
```bash
./run migrate --apply
```

### "Authentication failed"

**Error:**
```
PDOException: SQLSTATE[28000] authentication failed for user "duoncms"
```

**Solution:**
Ensure the database user exists with the correct password:

```bash
sudo -u postgres createuser -d duoncms
sudo -u postgres psql -c "ALTER USER duoncms WITH PASSWORD 'duoncms';"
```

### "Permission denied to create database"

**Error:**
```
PDOException: permission denied to create database
```

**Solution:**
Grant CREATEDB privilege to the user:

```bash
sudo -u postgres psql -c "ALTER USER duoncms CREATEDB;"
```

### Database Connection Configuration

Test database credentials are configured in `tests/Setup/TestCase.php`:

```php
// Database: duoncms
// User: duoncms
// Password: duoncms
// Host: localhost
```

To use different credentials, modify the `conn()` method in `TestCase.php`.

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      postgres:
        image: postgres:16
        env:
          POSTGRES_DB: duoncms
          POSTGRES_USER: duoncms
          POSTGRES_PASSWORD: duoncms
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
        ports:
          - 5432:5432

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: pdo, pdo_pgsql, pgsql

      - name: Install dependencies
        run: composer install

      - name: Initialize test database
        run: |
          ./run recreate-db
          ./run migrate --apply

      - name: Run tests
        run: vendor/bin/phpunit

      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage.xml
```

## Best Practices

### DO

- ✅ Extend `IntegrationTestCase` for database tests
- ✅ Use `createTestType()`, `createTestNode()` helpers for test data
- ✅ Load fixtures in `setUp()` when needed across all test methods
- ✅ Use descriptive test names (`testFinderReturnsNodesOfSpecificType`)
- ✅ Follow Arrange-Act-Assert pattern
- ✅ Clean up the database is automatic (via transactions)

### DON'T

- ❌ Use mocks for database interactions in integration tests
- ❌ Rely on test execution order
- ❌ Share state between tests (use transactions instead)
- ❌ Commit transactions in tests (they should auto-rollback)
- ❌ Create permanent test data outside of transactions

## Performance

Expected test execution times:
- **Unit tests**: < 1 second
- **Integration tests**: 5-15 seconds (depending on fixture data)
- **Full test suite**: ~10-20 seconds

To optimize:
- Minimize fixture data (only load what's needed)
- Use helper methods instead of loading large SQL files
- Consider splitting large integration tests into smaller focused tests

## Future Enhancements

- [ ] Tag tests with `@group integration` for filtering
- [ ] Add authentication integration tests
- [ ] Add full-text search integration tests
- [ ] Add URL path resolution tests
- [ ] Database seeder for realistic test data
- [ ] Parallel test execution
