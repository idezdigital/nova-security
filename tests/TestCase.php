<?php

namespace Idez\NovaSecurity\Tests;

use Idez\NovaSecurity\NovaSecurityServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            NovaSecurityServiceProvider::class,
        ];
    }

    public function refreshServiceProvider(): void
    {
        (new NovaSecurityServiceProvider($this->app))->packageBooted();
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
    }
}
