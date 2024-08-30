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

$news = NewsApi::search(query: 'Messi', countries: 'us', language: 'en'); // returns a collection of news items
$newsItem = $news->first(); // returns a single news item collection

// All news items are Laravel collections
$newsItem->toJson(); /* returns
{
    "summary": "Joan Monfort never believed in fate...",
    "image": "https://a.espncdn.com/photo/2024/0710/r1356881_1296x729_16-9.jpg",
    "sentiment": -0.456,
    "author": "Sid Lowe",
    "language": "en",
    "video": null,
    "title": "Photographer didn't believe in destiny...",
    "url": "https://www.espn.com/soccer/story/_/id/4088...",
    "source_country": "US",
    "id": 254279758,
    "text": "...",
    "category": null,
    "publish_date": "2024-08-16 18:40:10",
    "authors": [
      "Sid Lowe"
    ]
  }
*/

// Access the summary of the first news item
$newsItem->get('summary'); // 'Joan Monfort never believed in fate...'

// Access the author of the first news item
$newsItem->get('author'); // 'Sid Lowe'

// Loop through each news item
foreach ($news as $item) {
    // Access the title of each news item
    echo $item->get('title');
}
```

## Credits

- [Said](https://github.com/amsaid)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
