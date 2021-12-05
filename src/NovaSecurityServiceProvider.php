<?php

namespace Idez\NovaSecurity;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NovaSecurityServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('nova-security')
            ->hasConfigFile()
            ->hasMigration('add_blocked_at_column_to_users_table');
    }
}
