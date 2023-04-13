# Sent easy request to your comindwork

[![Latest Version on Packagist](https://img.shields.io/packagist/v/oneplusone/cmwquery.svg?style=flat-square)](https://packagist.org/packages/oneplusone/cmwquery)
<!-- [![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/oneplusone/cmwquery/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/oneplusone/cmwquery/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/oneplusone/cmwquery/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/oneplusone/cmwquery/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/oneplusone/cmwquery.svg?style=flat-square)](https://packagist.org/packages/oneplusone/cmwquery) -->



## Support us

<!-- [<img src="https://github-ads.s3.eu-central-1.amazonaws.com/:package_name.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/:package_name) -->

We invest a lot of resources into creating [best in class open source packages](https://oneplusone.solutions/contacts/). You can support us by [buying one of our paid products](https://oneplusone.solutions/contacts/).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://oneplusone.solutions/about-us/). We publish all received postcards on [our virtual postcard wall](https://oneplusone.solutions/our-work/).

## Installation

You can install the package via composer:

```bash
composer require oneplusone/cmwquery
```

You can publish and run the migrations with:

<!-- ```bash
php artisan vendor:publish --tag="cmw-query-migrations"
php artisan migrate
``` -->

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cmwquery-config"
```

This is the contents of the published config file:

```php
return [
	'cmw-query.php'
];
```

<!-- Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="cmwquery-views"
``` -->

## Usage

To use request automticaly for some Model insert on top of Model file 
```php
use OnePlusOne\CMWQuery\Traits\SendCMWRequest;
```
Indert in model body 
```php
use SendCMWRequest;
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


<!-- ## Security Vulnerabilities -->

<!-- Please review [our security policy](../../security/policy) on how to report security vulnerabilities. -->

## Credits

- [GalinaBublik](https://github.com/GalinaBublik)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
