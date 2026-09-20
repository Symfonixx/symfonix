<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->guardAgainstApplicationDatabase();
    }

    private function guardAgainstApplicationDatabase(): void
    {
        $connection = (string) config('database.default');
        $database = (string) config("database.connections.{$connection}.database");

        if ($database === ':memory:' || str_contains($database, 'test')) {
            return;
        }

        throw new RuntimeException(
            "Tests refused to use database [{$database}] on connection [{$connection}]. ".
            'PHPUnit must use the dedicated testing database (symfonix_testing).'
        );
    }
}
