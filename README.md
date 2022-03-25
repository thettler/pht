
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# PHT: Something like Typescript but for PHP  

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thettler/pht.svg?style=flat-square)](https://packagist.org/packages/thettler/pht)
[![Tests](https://github.com/thettler/pht/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/thettler/pht/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/thettler/pht.svg?style=flat-square)](https://packagist.org/packages/thettler/pht)

> :exclamation: :exclamation:  This is only a prototype and a Proof of concept. It is not meant to be used seriously and has a lot of bugs. You can read about it here [PHT: Building Typescript for PHP](https://bitbench.dev/blog/pht-building-typescript-for-php). But feel free to contribute and spin the idea even further :exclamation: :exclamation:

This is just a little proof of concept for everybody to play around. For a better explanation read the article on
[bitbench.dev - PHT: Building Typescript for PHP](https://bitbench.dev/blog/pht-building-typescript-for-php)

## Installation
You can install the package via composer:
```bash
composer require thettler/pht
```

## Usage
To start the watcher 
```bash
./vendor/bin/pht dev
```
by default, it will look inside the 'app' directory for any .pht files and compiles them into the '.pht' directory.

### Autoload
To enable auto-loading find your `autoload.php` require and call right after the `Thettler\Pht\PHT::autoload($loader)`.
```php
$loader = require __DIR__.'/../vendor/autoload.php';
Thettler\Pht\PHT::autoload($loader);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Tobias Hettler](https://github.com/thettler)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
