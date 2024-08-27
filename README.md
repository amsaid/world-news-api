# Laravel client for world news api

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amsaid/world-news-api.svg?style=flat-square)](https://packagist.org/packages/amsaid/world-news-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/amsaid/world-news-api/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/amsaid/world-news-api/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/amsaid/world-news-api/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/amsaid/world-news-api/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/amsaid/world-news-api.svg?style=flat-square)](https://packagist.org/packages/amsaid/world-news-api)


## Support us

If you like this package, consider supporting us by donating to support our development. Your support is greatly appreciated! 🙏

<a href="https://www.buymeacoffee.com/amsaid" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>

## Installation

You can install the package via composer:

```bash
composer require amsaid/world-news-api
```

You can publish the config file and the service provider with:

```bash
php artisan world-news-api:install

```

This is the contents of the published config file:

```php
return [
    'apikey' => env('NEWS_API_KEY', 'your-api-key'),
];
```


## Usage

```php
use Amsaid\WorldNewsApi\Facades\NewsApi;

$news = NewsApi::searchNews('Messi', 'US', 'en');
```

## Credits

- [Said](https://github.com/amsaid)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
