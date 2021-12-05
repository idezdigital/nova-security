<?php

namespace Idez\NovaSecurity;

use Idez\NovaSecurity\Commands\NovaSecurityCommand;
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
            ->hasViews()
            ->hasMigration('create_nova-security_table')
            ->hasCommand(NovaSecurityCommand::class);
    }
}
