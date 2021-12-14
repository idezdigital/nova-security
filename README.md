# Nova Security

[![Latest Version on Packagist](https://img.shields.io/packagist/v/idez/nova-security.svg?style=flat-square)](https://packagist.org/packages/idez/nova-security)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/idez/nova-security/run-tests?label=tests)](https://github.com/idez/nova-security/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/idez/nova-security/Check%20&%20fix%20styling?label=code%20style)](https://github.com/idez/nova-security/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/idez/nova-security.svg?style=flat-square)](https://packagist.org/packages/idez/nova-security)

This is a collection of different techniques and measures to make your laravel app more secure.

## Support us

Send email to arthur@idez.com.br or pedro@idez.com.br

## Installation

You can install the package via composer:

```bash
composer require idez/nova-security
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="nova-security-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="nova-security-config"
```

You can publish the translations files with:
```bash
php artisan vendor:publish --tag="nova-security-translations"
```

You can publish the views files with:
```bash
php artisan vendor:publish --tag="nova-security-views"
```

This is the contents of the published config file:

```php
return [
    'brute_force' => [
        'enabled' => true,
        'max_attempts' => 3,
        'ttl' => 3600,
        'protected_field' => 'email',
    ],


    '2fa' => [
        'enabled' => true,
        'invalid_attempts_limit'
    ]
];
```

## Usage

### Brute Force
Brute force protection is a Middleware, which can be registered in your application's NovaServiceProvider, override routes() method, as in the example below:

```php
use Idez\NovaSecurity\BruteForceProtection;

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        $middleware = [
            'web',
            BruteForceProtection::class,
        ];

        Nova::routes()
            ->withAuthenticationRoutes($middleware)
            ->withPasswordResetRoutes();
    }
```
Nothing prevents you from placing it elsewhere, such as `App\Http\Kernel` or `config/nova.php`


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Idez Digital](https://github.com/idezdigital)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
