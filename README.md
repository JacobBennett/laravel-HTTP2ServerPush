![](https://raw.githubusercontent.com/jacobbennett/laravel-http2serverpush/master/server-push.png)

# Server Push Middleware for Laravel 5

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jacobbennett/laravel-Http2ServerPush.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/jacobbennett/laravel-http2serverpush)
[![Travis](https://img.shields.io/travis/JacobBennett/laravel-HTTP2ServerPush.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/JacobBennett/laravel-HTTP2ServerPush)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/jacobbennett/laravel-http2serverpush.svg?style=flat-square)](https://packagist.org/packages/jacobbennett/laravel-http2serverpush)

Server Push is a HTTP/2 concept which allows the server to speculatively start sending resources to the client. This can potentially speed up initial page load times: the browser doesn't have to parse the HTML page and find out which other resources to load, instead the server can start sending them immediately. [(source)](http://blog.xebia.com/http2-server-push/)

This package aims to provide the _easiest_ experience for adding Server Push to your responses. 
Simply route your requests through the `AddHttp2ServerPush` middleware and it will automatically create and attach the `Link` headers necessary to implement Server Push for your CSS, JS and Image assets.

## Requirements

- Laravel 5
- PHP 7

## Installation

You can install the package via composer:
``` bash
$ composer require jacobbennett/laravel-http2serverpush
```

Next you must add the `\JacobBennett\Http2ServerPush\Middleware\AddHttp2ServerPush`-middleware to the kernel. Adding it to the web group is recommeneded as API's do not have assets to push.
```php
// app/Http/Kernel.php

...
protected $middlewareGroups = [
    'web' => [
        ...
        \JacobBennett\Http2ServerPush\Middleware\AddHttp2ServerPush::class,
        ...
    ],
    ...
];
```

## Publish config

```php
php artisan vendor:publish --provider="JacobBennett\Http2ServerPush\ServiceProvider"
```


## Usage

When you route a request through the `AddHttp2ServerPush` middleware, the response is scanned for any `link`, `script` or `img` tags that could benefit from being loaded using Server Push. 
These assets will be added to the `Link` header before sending the response to the client. Easy!

**Note:** To push an image asset, it must have one of the following extensions: `bmp`, `gif`, `jpg`, `jpeg`, `png`, `tiff` or `svg`.

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
