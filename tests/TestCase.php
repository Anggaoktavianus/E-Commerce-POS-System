<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Safety check: Prevent tests from running against production database
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // CRITICAL SAFETY CHECK: Ensure we're using test database
        $dbConnection = config('database.default');
        $dbDatabase = config('database.connections.' . $dbConnection . '.database');
        
        // Only allow SQLite in-memory database for tests
        if ($dbConnection !== 'sqlite' || $dbDatabase !== ':memory:') {
            throw new \Exception(
                'SECURITY ERROR: Tests must use SQLite in-memory database. ' .
                'Current: ' . $dbConnection . ' -> ' . $dbDatabase . '. ' .
                'This prevents accidental deletion of production/local database.'
            );
        }
        
        // Additional check: Ensure APP_ENV is testing
        if (app()->environment() !== 'testing') {
            throw new \Exception(
                'SECURITY ERROR: Tests must run in testing environment. ' .
                'Current: ' . app()->environment()
            );
        }
    }
}
