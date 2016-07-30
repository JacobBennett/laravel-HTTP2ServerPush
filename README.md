# A HTTP2 Server Push Middleware for Laravel 5

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jacobbennett/laravel-Http2ServerPush.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/jacobbennett/laravel-http2serverpush)
[![Travis](https://img.shields.io/travis/JacobBennett/laravel-HTTP2ServerPush.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/JacobBennett/laravel-HTTP2ServerPush)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/jacobbennett/laravel-http2serverpush.svg?style=flat-square)](https://packagist.org/packages/jacobbennett/laravel-http2serverpush)

Server Push is a HTTP/2 concept which allows the server to speculatively start sending resources to the client. This can potentially speed up initial page load times: the browser doesn't have to parse the HTML page and find out which other resources to load, instead the server can start sending them immediately. [(source)](http://blog.xebia.com/http2-server-push/)

This package aims to provide the _easiest_ experience for adding Server Push to your responses. 
Simply route your requests through the `AddHttp2ServerPush` middleware and it will automatically create and attach the `Link` headers necessary to implement Server Push for your CSS and JS assets.

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

Thanks to the [https://github.com/spatie/laravel-pjax](https://github.com/spatie/laravel-pjax) package for providing a great starting point for testing Middlewares.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
