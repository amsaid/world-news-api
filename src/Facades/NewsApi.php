<?php

namespace Amsaid\WorldNewsApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection search(string $query, string $countries, string $language, float $min_sentiment, float $max_sentiment, string $min_date, string $max_date, string $news_sources, string $authors, string $categories, string $entities, string $location_filter, string $sort, string $sort_direction, int $offset, int $number)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response extractNews(string $url, bool $analyze, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response extractNewsLinks(string $url, bool $analyze, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response getGeoCoordinates(string $location, string $contentType)
 * @method static object newsWebsiteToRSSFeed(string $url, bool $analyze, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response retrieveNewsArticlesByIds(string $ids, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response retrieveNewspaperFrontPage(string $source_country, string $source_name, string $date, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response searchNews(string $text, string $source_countries, string $language, float $min_sentiment, float $max_sentiment, string $earliest_publish_date, string $latest_publish_date, string $news_sources, string $authors, string $categories, string $entities, string $location_filter, string $sort, string $sort_direction, int $offset, int $number, string $contentType)
 * @method static \Amsaid\WorldNewsApi\Lib\Model\TopNews200Response topNews(string $source_country, string $language, string $date, bool $headlines_only, string $contentType)
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
