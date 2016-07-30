# A HTTP2 Server Push Middleware for Laravel 5

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jacobbennett/laravel-http2serverpush.svg?style=flat-square)](https://packagist.org/packages/jacobbennett/laravel-http2serverpush)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JacobBennett/laravel-HTTP2ServerPush/master.svg?style=flat-square)](https://travis-ci.org/jacobbennett/laravel-http2serverpush)
[![Quality Score](https://img.shields.io/scrutinizer/g/jacobbennett/laravel-http2serverpush.svg?style=flat-square)](https://scrutinizer-ci.com/g/jacobbennett/laravel-http2serverpush)
[![Total Downloads](https://img.shields.io/packagist/dt/jacobbennett/laravel-http2serverpush.svg?style=flat-square)](https://packagist.org/packages/jacobbennett/laravel-http2serverpush)

Something here explaining what ServerPush is

## Installation

You can install the package via composer:
``` bash
$ composer require jacobbennett/laravel-http2serverpush
```

Next you must add the `\JacobBennett\Http2ServerPush\Middleware\AddHttp2ServerPush`-middleware to the kernel.
```php
// app/Http/Kernel.php

...
protected $middleware = [
    ...
    \JacobBennett\Http2ServerPush\Middleware\AddHttp2ServerPush::class,
];
```

## Usage

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email me@jakebennett.net instead of using the issue tracker.

## Credits

- [Jacob Bennett](https://github.com/jacobbennett)
- [All Contributors](../../contributors)

Something here about spatie helping a ton with tests and what not

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
