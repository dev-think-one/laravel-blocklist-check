# Laravel blocklist.

![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-blocklist-check?color=%234dc71f)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-blocklist-check/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-blocklist-check/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-blocklist-check/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-blocklist-check/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-blocklist-check/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-blocklist-check/?branch=master)

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

```injectablephp
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