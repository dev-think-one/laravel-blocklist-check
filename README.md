# Laravel blocklist.

![Packagist License](https://img.shields.io/packagist/l/think.studio/laravel-blocklist-check?color=%234dc71f)
[![Packagist Version](https://img.shields.io/packagist/v/think.studio/laravel-blocklist-check)](https://packagist.org/packages/think.studio/laravel-blocklist-check)
[![Total Downloads](https://img.shields.io/packagist/dt/think.studio/laravel-blocklist-check)](https://packagist.org/packages/think.studio/laravel-blocklist-check)
[![Build Status](https://scrutinizer-ci.com/g/dev-think-one/laravel-blocklist-check/badges/build.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/laravel-blocklist-check/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/dev-think-one/laravel-blocklist-check/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/laravel-blocklist-check/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dev-think-one/laravel-blocklist-check/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/laravel-blocklist-check/?branch=main)

Add model to blocklist/allowlist,

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-blocklist-check
```

Optionally you can publish the config file with:

```bash
php artisan vendor:publish --provider="LaraBlockList\ServiceProvider" --tag="config"
```

## Usage

Update your model:

```php
use LaraBlockList\Contracts\CanBeInBlocklist;
use LaraBlockList\Models\HasBlocklist;

class User /* ... */ implements CanBeInBlocklist
{
    use HasBlocklist;

    /**
     * @inheritDoc
     */
    public function getBlocklistProcessor(array $args = []): BlocklistProcessor
    {
        return new BlocklistProcessor([
            new RegexChecker([ '/\.ru$/', ], [ 'email' ]),
            new RegexChecker([ /* contain cyrillic */ '/[А-Яа-яЁё]+/u', ], [ 'name', ]),
        ]);
    }

    // ...
}
```

Now you can run checks:

```shell
php artisan blocklist:check "\App\Models\User"
# or
php artisan blocklist:check "\App\Models\User" 123
# or
php artisan blocklist:check "\App\Models\User" --from=2020-01-01 --queue=default
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/) 