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
            ->hasMigrations(
                'add_blocked_at_column_to_users_table',
                'add_two_factor_secret_column_to_users_table'
            )
            ->hasRoute('nova-security');
    }

    /**
     * @throws InvalidPackage
     */
    public function register(): self
    {
        parent::register();

        $this->app->bind('nova-security', function ($app) {
            return new NovaSecurity();
        });

        $this->registerGoogle2FA();

        return $this;
    }

    /**
     */
    protected function registerGoogle2FA()
    {
        if (config('nova-security.google2fa.ignore_override', false)) {
            return;
        }

        if (! config('nova-security.google2fa.enabled', false)) {
            return;
        }

        $this->overrideConfiguration('google2fa', 'nova-security.google2fa');
        $this->app->register(Google2FAServiceProvider::class);
    }

    /**
     */
    protected function overrideConfiguration(string $overrideKey, $key): void
    {
        config()->set(
            $overrideKey,
            config($key, config($overrideKey, []))
        );
    }
}
