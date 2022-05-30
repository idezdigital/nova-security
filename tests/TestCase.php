<?php

namespace Idez\NovaSecurity\Tests;

use Idez\NovaSecurity\NovaSecurityServiceProvider;
use Illuminate\Support\Facades\Schema;
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

    public function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $migrationBlock = include __DIR__.'/../database/migrations/add_blocked_at_column_to_users_table.php.stub';
        $migrationBlock->up();

        $migrationSecret = include __DIR__.'/../database/migrations/add_two_factor_secret_column_to_users_table.php.stub';
        $migrationSecret->up();
    }
}
