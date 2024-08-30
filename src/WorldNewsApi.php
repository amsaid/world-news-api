<?php

namespace Amsaid\WorldNewsApi;

use Amsaid\WorldNewsApi\Lib\Api\NewsApi;
use Illuminate\Support\Collection;


class WorldNewsApi extends NewsApi
{
    /**
     * Search News
     *
     * @param  string $query The text to match in the news content (at least 3 characters, maximum 100 characters). By default all query terms are expected, you can use an uppercase OR to search for any terms, e.g. tesla OR ford (optional)
     * @param  string $countries A comma-separated list of ISO 3166 country codes from which the news should originate. (optional)
     * @param  string $language The ISO 6391 language code of the news. (optional)
     * @param  float $min_sentiment The minimal sentiment of the news in range [-1,1]. (optional)
     * @param  float $max_sentiment The maximal sentiment of the news in range [-1,1]. (optional)
     * @param  string $min_date The news must have been published after this date. (optional)
     * @param  string $max_date The news must have been published before this date. (optional)
     * @param  string $news_sources A comma-separated list of news sources from which the news should originate. (optional)
     * @param  string $authors A comma-separated list of author names. Only news from any of the given authors will be returned. (optional)
     * @param  string $categories A comma-separated list of categories. Only news from any of the given categories will be returned. Possible categories are politics, sports, business, technology, entertainment, health, science, lifestyle, travel, culture, education, environment, other. (optional)
     * @param  string $entities Filter news by entities (see semantic types). (optional)
     * @param  string $location_filter Filter news by radius around a certain location. Format is \&quot;latitude,longitude,radius in kilometers\&quot;. Radius must be between 1 and 100 kilometers. (optional)
     * @param  string $sort The sorting criteria (publish-time). (optional)
     * @param  string $sort_direction Whether to sort ascending or descending (ASC or DESC). (optional)
     * @param  int $offset The number of news to skip in range [0,10000] (optional)
     * @param  int $number The number of news to return in range [1,100] (optional)
     *
     * @return \Illuminate\Support\Collection<TKey, TValue>
     */
    public function search(
        ?string $query = null,
        ?string $countries = null,
        ?string $language = null,
        ?float $min_sentiment = null,
        ?float $max_sentiment = null,
        ?string $min_date = null,
        ?string $max_date = null,
        ?string $news_sources = null,
        ?string $authors = null,
        ?string $categories = null,
        ?string $entities = null,
        ?string $location_filter = null,
        ?string $sort = null,
        ?string $sort_direction = null,
        ?int $offset = null,
        ?int $number = null
    ): Collection {
        return collect(
            $this->searchNews(
                text: $query,
                source_countries: $countries,
                language: $language,
                min_sentiment: $min_sentiment,
                max_sentiment: $max_sentiment,
                earliest_publish_date: $min_date,
                latest_publish_date: $max_date,
                news_sources: $news_sources,
                categories: $categories,
                entities: $entities,
                authors: $authors,
                location_filter: $location_filter,
                sort: $sort,
                sort_direction: $sort_direction,
                offset: $offset,
                number: $number
            )->getNews()
        )->map(function ($item) {
            return collect($item)->toArray();
        });
    }
}
