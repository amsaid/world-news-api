<?php

namespace Amsaid\WorldNewsApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi extractNews(string $url, bool $analyze, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi extractNewsLinks(string $url, bool $analyze, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi getGeoCoordinates(string $location, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi newsWebsiteToRSSFeed(string $url, bool $analyze, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi retrieveNewsArticlesByIds(string $ids, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi retrieveNewspaperFrontPage(string $source_country, string $source_name, string $date, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi searchNews(string $text, string $source_countries, string $language, float $min_sentiment, float $max_sentiment, string $earliest_publish_date, string $latest_publish_date, string $news_sources, string $authors, string $categories, string $entities, string $location_filter, string $sort, string $sort_direction, int $offset, int $number, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Api\NewsApi topNews(string $source_country, string $language, string $date, bool $headlines_only, string $contentType)

 *
 * @see \Amsaid\WorldNewsApi\WorldNewsApi
 */
class NewsApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Amsaid\WorldNewsApi\WorldNewsApi::class;
    }
}
