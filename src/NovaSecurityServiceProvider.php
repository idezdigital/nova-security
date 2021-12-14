<?php

namespace Idez\NovaSecurity;

use PragmaRX\Google2FALaravel\ServiceProvider as Google2FAServiceProvider;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
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
            ->hasTranslations()
            ->hasViews()
            ->hasMigration('add_blocked_at_column_to_users_table');
    }

    /**
     * @throws InvalidPackage
     */
    public function register(): self
    {
        parent::register();

        $this->app->bind('nova-security', function($app) {
            return new NovaSecurity;
        });

        $this->app->register(Google2FAServiceProvider::class);

        return $this;
    }
}
