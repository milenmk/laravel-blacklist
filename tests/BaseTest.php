<?php

declare(strict_types=1);

namespace Milenmk\LaravelBlacklist\Tests;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Milenmk\LaravelBlacklist\BlacklistServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class BaseTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BlacklistServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
