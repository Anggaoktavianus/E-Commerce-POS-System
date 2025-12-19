<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * CRITICAL SAFETY CHECK: Prevent tests from running against production/local database
     * 
     * ⚠️ NEVER DELETE DATA FROM PRODUCTION/LOCAL DATABASE ⚠️
     * 
     * This method ensures that:
     * 1. Tests ONLY use SQLite in-memory database (:memory:)
     * 2. Tests NEVER touch your MySQL/PostgreSQL database
     * 3. All test data is created in memory and destroyed after tests
     * 4. Your production/local database is 100% safe
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // CRITICAL SAFETY CHECK #1: Ensure we're using SQLite in-memory database
        $dbConnection = config('database.default');
        $dbDatabase = config('database.connections.' . $dbConnection . '.database');
        
        if ($dbConnection !== 'sqlite' || $dbDatabase !== ':memory:') {
            throw new \Exception(
                "\n" .
                "╔════════════════════════════════════════════════════════════════╗\n" .
                "║  🚨 CRITICAL SECURITY ERROR 🚨                                 ║\n" .
                "╠════════════════════════════════════════════════════════════════╣\n" .
                "║  Tests MUST use SQLite in-memory database ONLY!                ║\n" .
                "║  Current database: {$dbConnection} -> {$dbDatabase}            ║\n" .
                "║                                                                ║\n" .
                "║  ⚠️  This prevents accidental deletion of your database!       ║\n" .
                "║  ⚠️  Tests will NEVER touch your MySQL/PostgreSQL database!   ║\n" .
                "╚════════════════════════════════════════════════════════════════╝\n"
            );
        }
        
        // CRITICAL SAFETY CHECK #2: Ensure APP_ENV is testing
        if (app()->environment() !== 'testing') {
            throw new \Exception(
                "\n" .
                "╔════════════════════════════════════════════════════════════════╗\n" .
                "║  🚨 CRITICAL SECURITY ERROR 🚨                                 ║\n" .
                "╠════════════════════════════════════════════════════════════════╣\n" .
                "║  Tests MUST run in 'testing' environment ONLY!                ║\n" .
                "║  Current environment: " . app()->environment() . "                                    ║\n" .
                "║                                                                ║\n" .
                "║  ⚠️  This prevents accidental deletion of your database!       ║\n" .
                "╚════════════════════════════════════════════════════════════════╝\n"
            );
        }
        
        // CRITICAL SAFETY CHECK #3: Double-check MySQL connection is NOT used
        $mysqlConnections = ['mysql', 'mariadb', 'pgsql'];
        if (in_array($dbConnection, $mysqlConnections)) {
            throw new \Exception(
                "\n" .
                "╔════════════════════════════════════════════════════════════════╗\n" .
                "║  🚨 CRITICAL SECURITY ERROR 🚨                                 ║\n" .
                "╠════════════════════════════════════════════════════════════════╣\n" .
                "║  Tests CANNOT use MySQL/PostgreSQL database!                 ║\n" .
                "║  Current connection: {$dbConnection}                           ║\n" .
                "║                                                                ║\n" .
                "║  ⚠️  This would DELETE your production/local database!        ║\n" .
                "║  ⚠️  Tests MUST use SQLite in-memory database ONLY!          ║\n" .
                "╚════════════════════════════════════════════════════════════════╝\n"
            );
        }
    }
}
