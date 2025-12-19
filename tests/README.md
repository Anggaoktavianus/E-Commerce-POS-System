# Testing Guide

## ⚠️ CRITICAL SAFETY WARNINGS ⚠️

### 🚨 NEVER DELETE DATA FROM PRODUCTION/LOCAL DATABASE 🚨

**IMPORTANT:** Tests are configured to **NEVER** touch your MySQL/PostgreSQL database!

### Database Safety
- **Tests use SQLite in-memory database ONLY** (`:memory:`)
- **Tests NEVER touch your production/local MySQL database**
- **Your `.env` database settings are completely ignored during tests**
- Tests are configured in `phpunit.xml` to **FORCE** SQLite in-memory
- Multiple safety checks in `TestCase.php` prevent accidental production database access
- If tests try to use wrong database, they will **STOP** with clear error

**See [SAFETY_WARNING.md](./SAFETY_WARNING.md) for detailed safety information.**

### How It Works
1. When running tests, PHPUnit forces:
   - `APP_ENV=testing`
   - `DB_CONNECTION=sqlite`
   - `DB_DATABASE=:memory:`
2. All test data is created in memory and destroyed after tests
3. Your `.env` database settings are **completely ignored** during tests

### Running Tests Safely

```bash
# This is SAFE - uses SQLite in-memory only
php artisan test

# This is SAFE - uses SQLite in-memory only
php artisan test --filter=Pos

# This is SAFE - uses SQLite in-memory only
vendor/bin/phpunit
```

### What Tests Do
- Create test data in SQLite in-memory database
- Run tests
- Destroy all test data automatically
- **NEVER touch your MySQL database**

### If You See Database Issues
If you accidentally ran tests and see database issues:

1. **Check your `.env` file** - Your database settings should be intact
2. **Check your actual database** - Tests don't modify it
3. **The issue might be from something else** - Not from running tests

### Test Configuration
See `phpunit.xml` for test environment configuration:
- Forces SQLite in-memory database
- Forces testing environment
- Prevents production database access

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test Suite
```bash
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Specific Test
```bash
php artisan test --filter=PosTransactionFlowTest
php artisan test --filter=it_can_complete_full_transaction_flow
```

### With Coverage
```bash
php artisan test --coverage
```

## Test Structure

### Unit Tests (`tests/Unit/`)
- Test individual models and services
- Fast, isolated tests
- Examples:
  - `PosShiftTest.php`
  - `PosTransactionTest.php`
  - `PosServiceTest.php`

### Feature Tests (`tests/Feature/`)
- Test complete workflows
- Integration tests
- Examples:
  - `PosTransactionFlowTest.php`
  - `PosShiftFlowTest.php`
  - `PosPaymentFlowTest.php`
  - `PosInventoryFlowTest.php`
  - `PosRefundFlowTest.php`
  - `PosReportTest.php`
  - `PosReceiptTest.php`
  - `PosCustomerTest.php`
  - `PosSettingsTest.php`
  - `PosCashMovementTest.php`
  - `PosReceiptTemplateTest.php`

## Writing Tests

### Basic Test Structure
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase; // Uses SQLite in-memory, safe!

    /** @test */
    public function it_does_something()
    {
        // Your test code here
        // All data is in SQLite in-memory, not your MySQL database
    }
}
```

### Creating Test Data
```php
// Create test data (in SQLite in-memory)
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
    'role' => 'cashier'
]);
```

## Troubleshooting

### "Database connection error"
- Check `phpunit.xml` - should use SQLite
- Check `TestCase.php` - safety checks should pass
- Ensure SQLite extension is installed

### "Factory not found"
- Use `Model::create()` instead of `Model::factory()->create()`
- Or create factories in `database/factories/`

### "Migration error"
- Some migrations might not be SQLite-compatible
- Check migration files for SQLite-specific issues
- Consider using `DatabaseTransactions` instead of `RefreshDatabase` for specific tests

## Safety Guarantees

✅ Tests use SQLite in-memory database  
✅ Tests never access your MySQL database  
✅ Tests never modify production data  
✅ Safety checks prevent accidental production access  
✅ All test data is automatically destroyed after tests  
