<?php

/**
 * NewsApi

 */


namespace Amsaid\WorldNewsApi\Lib\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Amsaid\WorldNewsApi\Lib\ApiException;
use Amsaid\WorldNewsApi\Lib\Configuration;
use Amsaid\WorldNewsApi\Lib\HeaderSelector;
use Amsaid\WorldNewsApi\Lib\ObjectSerializer;

/**
 * NewsApi Class Doc Comment
 */
class NewsApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'extractNews' => [
            'application/json',
        ],
        'extractNewsLinks' => [
            'application/json',
        ],
        'getGeoCoordinates' => [
            'application/json',
        ],
        'newsWebsiteToRSSFeed' => [
            'application/json',
        ],
        'retrieveNewsArticlesByIds' => [
            'application/json',
        ],
        'retrieveNewspaperFrontPage' => [
            'application/json',
        ],
        'searchNews' => [
            'application/json',
        ],
        'topNews' => [
            'application/json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation extractNews
     *
     * Extract News
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNews'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response
     */
    public function extractNews($url, $analyze, string $contentType = self::contentTypes['extractNews'][0])
    {
        list($response) = $this->extractNewsWithHttpInfo($url, $analyze, $contentType);
        return $response;
    }

    /**
     * Operation extractNewsWithHttpInfo
     *
     * Extract News
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNews'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function extractNewsWithHttpInfo($url, $analyze, string $contentType = self::contentTypes['extractNews'][0])
    {
        $request = $this->extractNewsRequest($url, $analyze, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation extractNewsAsync
     *
     * Extract News
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function extractNewsAsync($url, $analyze, string $contentType = self::contentTypes['extractNews'][0])
    {
        return $this->extractNewsAsyncWithHttpInfo($url, $analyze, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation extractNewsAsyncWithHttpInfo
     *
     * Extract News
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function extractNewsAsyncWithHttpInfo($url, $analyze, string $contentType = self::contentTypes['extractNews'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\ExtractNews200Response';
        $request = $this->extractNewsRequest($url, $analyze, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'extractNews'
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function extractNewsRequest($url, $analyze, string $contentType = self::contentTypes['extractNews'][0])
    {

        // verify the required parameter 'url' is set
        if ($url === null || (is_array($url) && count($url) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $url when calling extractNews'
            );
        }
        if (strlen($url) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$url" when calling NewsApi.extractNews, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/./", $url)) {
            throw new \InvalidArgumentException("invalid value for \"url\" when calling NewsApi.extractNews, must conform to the pattern /./.");
        }

        // verify the required parameter 'analyze' is set
        if ($analyze === null || (is_array($analyze) && count($analyze) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $analyze when calling extractNews'
            );
        }


        $resourcePath = '/extract-news';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $url,
            'url', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $analyze,
            'analyze', // param base name
            'boolean', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation extractNewsLinks
     *
     * Extract News Links
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNewsLinks'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response
     */
    public function extractNewsLinks($url, $analyze, string $contentType = self::contentTypes['extractNewsLinks'][0])
    {
        list($response) = $this->extractNewsLinksWithHttpInfo($url, $analyze, $contentType);
        return $response;
    }

    /**
     * Operation extractNewsLinksWithHttpInfo
     *
     * Extract News Links
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNewsLinks'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function extractNewsLinksWithHttpInfo($url, $analyze, string $contentType = self::contentTypes['extractNewsLinks'][0])
    {
        $request = $this->extractNewsLinksRequest($url, $analyze, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation extractNewsLinksAsync
     *
     * Extract News Links
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNewsLinks'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function extractNewsLinksAsync($url, $analyze, string $contentType = self::contentTypes['extractNewsLinks'][0])
    {
        return $this->extractNewsLinksAsyncWithHttpInfo($url, $analyze, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation extractNewsLinksAsyncWithHttpInfo
     *
     * Extract News Links
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNewsLinks'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function extractNewsLinksAsyncWithHttpInfo($url, $analyze, string $contentType = self::contentTypes['extractNewsLinks'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\ExtractNewsLinks200Response';
        $request = $this->extractNewsLinksRequest($url, $analyze, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'extractNewsLinks'
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['extractNewsLinks'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function extractNewsLinksRequest($url, $analyze, string $contentType = self::contentTypes['extractNewsLinks'][0])
    {

        // verify the required parameter 'url' is set
        if ($url === null || (is_array($url) && count($url) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $url when calling extractNewsLinks'
            );
        }
        if (strlen($url) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$url" when calling NewsApi.extractNewsLinks, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/./", $url)) {
            throw new \InvalidArgumentException("invalid value for \"url\" when calling NewsApi.extractNewsLinks, must conform to the pattern /./.");
        }

        // verify the required parameter 'analyze' is set
        if ($analyze === null || (is_array($analyze) && count($analyze) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $analyze when calling extractNewsLinks'
            );
        }


        $resourcePath = '/extract-news-links';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $url,
            'url', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $analyze,
            'analyze', // param base name
            'boolean', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation getGeoCoordinates
     *
     * Get Geo Coordinates
     *
     * @param  string $location The address or name of the location. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getGeoCoordinates'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response
     */
    public function getGeoCoordinates($location, string $contentType = self::contentTypes['getGeoCoordinates'][0])
    {
        list($response) = $this->getGeoCoordinatesWithHttpInfo($location, $contentType);
        return $response;
    }

    /**
     * Operation getGeoCoordinatesWithHttpInfo
     *
     * Get Geo Coordinates
     *
     * @param  string $location The address or name of the location. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getGeoCoordinates'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function getGeoCoordinatesWithHttpInfo($location, string $contentType = self::contentTypes['getGeoCoordinates'][0])
    {
        $request = $this->getGeoCoordinatesRequest($location, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation getGeoCoordinatesAsync
     *
     * Get Geo Coordinates
     *
     * @param  string $location The address or name of the location. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getGeoCoordinates'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getGeoCoordinatesAsync($location, string $contentType = self::contentTypes['getGeoCoordinates'][0])
    {
        return $this->getGeoCoordinatesAsyncWithHttpInfo($location, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation getGeoCoordinatesAsyncWithHttpInfo
     *
     * Get Geo Coordinates
     *
     * @param  string $location The address or name of the location. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getGeoCoordinates'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getGeoCoordinatesAsyncWithHttpInfo($location, string $contentType = self::contentTypes['getGeoCoordinates'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\GetGeoCoordinates200Response';
        $request = $this->getGeoCoordinatesRequest($location, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'getGeoCoordinates'
     *
     * @param  string $location The address or name of the location. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['getGeoCoordinates'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function getGeoCoordinatesRequest($location, string $contentType = self::contentTypes['getGeoCoordinates'][0])
    {

        // verify the required parameter 'location' is set
        if ($location === null || (is_array($location) && count($location) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $location when calling getGeoCoordinates'
            );
        }
        if (strlen($location) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$location" when calling NewsApi.getGeoCoordinates, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/./", $location)) {
            throw new \InvalidArgumentException("invalid value for \"location\" when calling NewsApi.getGeoCoordinates, must conform to the pattern /./.");
        }


        $resourcePath = '/geo-coordinates';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $location,
            'location', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation newsWebsiteToRSSFeed
     *
     * News Website to RSS Feed
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['newsWebsiteToRSSFeed'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return object
     */
    public function newsWebsiteToRSSFeed($url, $analyze, string $contentType = self::contentTypes['newsWebsiteToRSSFeed'][0])
    {
        list($response) = $this->newsWebsiteToRSSFeedWithHttpInfo($url, $analyze, $contentType);
        return $response;
    }

    /**
     * Operation newsWebsiteToRSSFeedWithHttpInfo
     *
     * News Website to RSS Feed
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['newsWebsiteToRSSFeed'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of object, HTTP status code, HTTP response headers (array of strings)
     */
    public function newsWebsiteToRSSFeedWithHttpInfo($url, $analyze, string $contentType = self::contentTypes['newsWebsiteToRSSFeed'][0])
    {
        $request = $this->newsWebsiteToRSSFeedRequest($url, $analyze, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('object' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('object' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, 'object', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = 'object';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        'object',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation newsWebsiteToRSSFeedAsync
     *
     * News Website to RSS Feed
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['newsWebsiteToRSSFeed'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function newsWebsiteToRSSFeedAsync($url, $analyze, string $contentType = self::contentTypes['newsWebsiteToRSSFeed'][0])
    {
        return $this->newsWebsiteToRSSFeedAsyncWithHttpInfo($url, $analyze, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation newsWebsiteToRSSFeedAsyncWithHttpInfo
     *
     * News Website to RSS Feed
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['newsWebsiteToRSSFeed'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function newsWebsiteToRSSFeedAsyncWithHttpInfo($url, $analyze, string $contentType = self::contentTypes['newsWebsiteToRSSFeed'][0])
    {
        $returnType = 'object';
        $request = $this->newsWebsiteToRSSFeedRequest($url, $analyze, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'newsWebsiteToRSSFeed'
     *
     * @param  string $url The url of the news. (required)
     * @param  bool $analyze Whether to analyze the news (extract entities etc.) (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['newsWebsiteToRSSFeed'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function newsWebsiteToRSSFeedRequest($url, $analyze, string $contentType = self::contentTypes['newsWebsiteToRSSFeed'][0])
    {

        // verify the required parameter 'url' is set
        if ($url === null || (is_array($url) && count($url) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $url when calling newsWebsiteToRSSFeed'
            );
        }
        if (strlen($url) > 1000) {
            throw new \InvalidArgumentException('invalid length for "$url" when calling NewsApi.newsWebsiteToRSSFeed, must be smaller than or equal to 1000.');
        }
        if (!preg_match("/./", $url)) {
            throw new \InvalidArgumentException("invalid value for \"url\" when calling NewsApi.newsWebsiteToRSSFeed, must conform to the pattern /./.");
        }

        // verify the required parameter 'analyze' is set
        if ($analyze === null || (is_array($analyze) && count($analyze) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $analyze when calling newsWebsiteToRSSFeed'
            );
        }


        $resourcePath = '/feed.rss';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $url,
            'url', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $analyze,
            'analyze', // param base name
            'boolean', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/xml',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation retrieveNewsArticlesByIds
     *
     * Retrieve News Articles by Ids
     *
     * @param  string $ids A comma separated list of news ids. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewsArticlesByIds'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response
     */
    public function retrieveNewsArticlesByIds($ids, string $contentType = self::contentTypes['retrieveNewsArticlesByIds'][0])
    {
        list($response) = $this->retrieveNewsArticlesByIdsWithHttpInfo($ids, $contentType);
        return $response;
    }

    /**
     * Operation retrieveNewsArticlesByIdsWithHttpInfo
     *
     * Retrieve News Articles by Ids
     *
     * @param  string $ids A comma separated list of news ids. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewsArticlesByIds'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function retrieveNewsArticlesByIdsWithHttpInfo($ids, string $contentType = self::contentTypes['retrieveNewsArticlesByIds'][0])
    {
        $request = $this->retrieveNewsArticlesByIdsRequest($ids, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation retrieveNewsArticlesByIdsAsync
     *
     * Retrieve News Articles by Ids
     *
     * @param  string $ids A comma separated list of news ids. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewsArticlesByIds'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function retrieveNewsArticlesByIdsAsync($ids, string $contentType = self::contentTypes['retrieveNewsArticlesByIds'][0])
    {
        return $this->retrieveNewsArticlesByIdsAsyncWithHttpInfo($ids, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation retrieveNewsArticlesByIdsAsyncWithHttpInfo
     *
     * Retrieve News Articles by Ids
     *
     * @param  string $ids A comma separated list of news ids. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewsArticlesByIds'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function retrieveNewsArticlesByIdsAsyncWithHttpInfo($ids, string $contentType = self::contentTypes['retrieveNewsArticlesByIds'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewsArticlesByIds200Response';
        $request = $this->retrieveNewsArticlesByIdsRequest($ids, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'retrieveNewsArticlesByIds'
     *
     * @param  string $ids A comma separated list of news ids. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewsArticlesByIds'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function retrieveNewsArticlesByIdsRequest($ids, string $contentType = self::contentTypes['retrieveNewsArticlesByIds'][0])
    {

        // verify the required parameter 'ids' is set
        if ($ids === null || (is_array($ids) && count($ids) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $ids when calling retrieveNewsArticlesByIds'
            );
        }
        if (strlen($ids) > 10000) {
            throw new \InvalidArgumentException('invalid length for "$ids" when calling NewsApi.retrieveNewsArticlesByIds, must be smaller than or equal to 10000.');
        }
        if (!preg_match("/./", $ids)) {
            throw new \InvalidArgumentException("invalid value for \"ids\" when calling NewsApi.retrieveNewsArticlesByIds, must conform to the pattern /./.");
        }


        $resourcePath = '/retrieve-news';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $ids,
            'ids', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation retrieveNewspaperFrontPage
     *
     * Retrieve Newspaper Front Page
     *
     * @param  string $source_country The ISO 3166 country code of the newspaper publication. (optional)
     * @param  string $source_name The identifier of the publication see attached list. (optional)
     * @param  string $date The date for which the front page should be retrieved. You can also go into the past, the earliest date is 2024-07-09. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewspaperFrontPage'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response
     */
    public function retrieveNewspaperFrontPage($source_country = null, $source_name = null, $date = null, string $contentType = self::contentTypes['retrieveNewspaperFrontPage'][0])
    {
        list($response) = $this->retrieveNewspaperFrontPageWithHttpInfo($source_country, $source_name, $date, $contentType);
        return $response;
    }

    /**
     * Operation retrieveNewspaperFrontPageWithHttpInfo
     *
     * Retrieve Newspaper Front Page
     *
     * @param  string $source_country The ISO 3166 country code of the newspaper publication. (optional)
     * @param  string $source_name The identifier of the publication see attached list. (optional)
     * @param  string $date The date for which the front page should be retrieved. You can also go into the past, the earliest date is 2024-07-09. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewspaperFrontPage'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function retrieveNewspaperFrontPageWithHttpInfo($source_country = null, $source_name = null, $date = null, string $contentType = self::contentTypes['retrieveNewspaperFrontPage'][0])
    {
        $request = $this->retrieveNewspaperFrontPageRequest($source_country, $source_name, $date, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation retrieveNewspaperFrontPageAsync
     *
     * Retrieve Newspaper Front Page
     *
     * @param  string $source_country The ISO 3166 country code of the newspaper publication. (optional)
     * @param  string $source_name The identifier of the publication see attached list. (optional)
     * @param  string $date The date for which the front page should be retrieved. You can also go into the past, the earliest date is 2024-07-09. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewspaperFrontPage'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function retrieveNewspaperFrontPageAsync($source_country = null, $source_name = null, $date = null, string $contentType = self::contentTypes['retrieveNewspaperFrontPage'][0])
    {
        return $this->retrieveNewspaperFrontPageAsyncWithHttpInfo($source_country, $source_name, $date, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation retrieveNewspaperFrontPageAsyncWithHttpInfo
     *
     * Retrieve Newspaper Front Page
     *
     * @param  string $source_country The ISO 3166 country code of the newspaper publication. (optional)
     * @param  string $source_name The identifier of the publication see attached list. (optional)
     * @param  string $date The date for which the front page should be retrieved. You can also go into the past, the earliest date is 2024-07-09. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewspaperFrontPage'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function retrieveNewspaperFrontPageAsyncWithHttpInfo($source_country = null, $source_name = null, $date = null, string $contentType = self::contentTypes['retrieveNewspaperFrontPage'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\RetrieveNewspaperFrontPage200Response';
        $request = $this->retrieveNewspaperFrontPageRequest($source_country, $source_name, $date, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'retrieveNewspaperFrontPage'
     *
     * @param  string $source_country The ISO 3166 country code of the newspaper publication. (optional)
     * @param  string $source_name The identifier of the publication see attached list. (optional)
     * @param  string $date The date for which the front page should be retrieved. You can also go into the past, the earliest date is 2024-07-09. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['retrieveNewspaperFrontPage'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function retrieveNewspaperFrontPageRequest($source_country = null, $source_name = null, $date = null, string $contentType = self::contentTypes['retrieveNewspaperFrontPage'][0])
    {

        if ($source_country !== null && strlen($source_country) > 2) {
            throw new \InvalidArgumentException('invalid length for "$source_country" when calling NewsApi.retrieveNewspaperFrontPage, must be smaller than or equal to 2.');
        }
        if ($source_country !== null && !preg_match("/./", $source_country)) {
            throw new \InvalidArgumentException("invalid value for \"source_country\" when calling NewsApi.retrieveNewspaperFrontPage, must conform to the pattern /./.");
        }

        if ($source_name !== null && strlen($source_name) > 100) {
            throw new \InvalidArgumentException('invalid length for "$source_name" when calling NewsApi.retrieveNewspaperFrontPage, must be smaller than or equal to 100.');
        }
        if ($source_name !== null && !preg_match("/./", $source_name)) {
            throw new \InvalidArgumentException("invalid value for \"source_name\" when calling NewsApi.retrieveNewspaperFrontPage, must conform to the pattern /./.");
        }

        if ($date !== null && strlen($date) > 10) {
            throw new \InvalidArgumentException('invalid length for "$date" when calling NewsApi.retrieveNewspaperFrontPage, must be smaller than or equal to 10.');
        }
        if ($date !== null && !preg_match("/./", $date)) {
            throw new \InvalidArgumentException("invalid value for \"date\" when calling NewsApi.retrieveNewspaperFrontPage, must conform to the pattern /./.");
        }


        $resourcePath = '/retrieve-front-page';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $source_country,
            'source-country', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $source_name,
            'source-name', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $date,
            'date', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation searchNews
     *
     * Search News
     *
     * @param  string $text The text to match in the news content (at least 3 characters, maximum 100 characters). By default all query terms are expected, you can use an uppercase OR to search for any terms, e.g. tesla OR ford (optional)
     * @param  string $source_countries A comma-separated list of ISO 3166 country codes from which the news should originate. (optional)
     * @param  string $language The ISO 6391 language code of the news. (optional)
     * @param  float $min_sentiment The minimal sentiment of the news in range [-1,1]. (optional)
     * @param  float $max_sentiment The maximal sentiment of the news in range [-1,1]. (optional)
     * @param  string $earliest_publish_date The news must have been published after this date. (optional)
     * @param  string $latest_publish_date The news must have been published before this date. (optional)
     * @param  string $news_sources A comma-separated list of news sources from which the news should originate. (optional)
     * @param  string $authors A comma-separated list of author names. Only news from any of the given authors will be returned. (optional)
     * @param  string $categories A comma-separated list of categories. Only news from any of the given categories will be returned. Possible categories are politics, sports, business, technology, entertainment, health, science, lifestyle, travel, culture, education, environment, other. (optional)
     * @param  string $entities Filter news by entities (see semantic types). (optional)
     * @param  string $location_filter Filter news by radius around a certain location. Format is \&quot;latitude,longitude,radius in kilometers\&quot;. Radius must be between 1 and 100 kilometers. (optional)
     * @param  string $sort The sorting criteria (publish-time). (optional)
     * @param  string $sort_direction Whether to sort ascending or descending (ASC or DESC). (optional)
     * @param  int $offset The number of news to skip in range [0,10000] (optional)
     * @param  int $number The number of news to return in range [1,100] (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchNews'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response
     */
    public function searchNews($text = null, $source_countries = null, $language = null, $min_sentiment = null, $max_sentiment = null, $earliest_publish_date = null, $latest_publish_date = null, $news_sources = null, $authors = null, $categories = null, $entities = null, $location_filter = null, $sort = null, $sort_direction = null, $offset = null, $number = null, string $contentType = self::contentTypes['searchNews'][0])
    {
        list($response) = $this->searchNewsWithHttpInfo($text, $source_countries, $language, $min_sentiment, $max_sentiment, $earliest_publish_date, $latest_publish_date, $news_sources, $authors, $categories, $entities, $location_filter, $sort, $sort_direction, $offset, $number, $contentType);
        return $response;
    }

    /**
     * Operation searchNewsWithHttpInfo
     *
     * Search News
     *
     * @param  string $text The text to match in the news content (at least 3 characters, maximum 100 characters). By default all query terms are expected, you can use an uppercase OR to search for any terms, e.g. tesla OR ford (optional)
     * @param  string $source_countries A comma-separated list of ISO 3166 country codes from which the news should originate. (optional)
     * @param  string $language The ISO 6391 language code of the news. (optional)
     * @param  float $min_sentiment The minimal sentiment of the news in range [-1,1]. (optional)
     * @param  float $max_sentiment The maximal sentiment of the news in range [-1,1]. (optional)
     * @param  string $earliest_publish_date The news must have been published after this date. (optional)
     * @param  string $latest_publish_date The news must have been published before this date. (optional)
     * @param  string $news_sources A comma-separated list of news sources from which the news should originate. (optional)
     * @param  string $authors A comma-separated list of author names. Only news from any of the given authors will be returned. (optional)
     * @param  string $categories A comma-separated list of categories. Only news from any of the given categories will be returned. Possible categories are politics, sports, business, technology, entertainment, health, science, lifestyle, travel, culture, education, environment, other. (optional)
     * @param  string $entities Filter news by entities (see semantic types). (optional)
     * @param  string $location_filter Filter news by radius around a certain location. Format is \&quot;latitude,longitude,radius in kilometers\&quot;. Radius must be between 1 and 100 kilometers. (optional)
     * @param  string $sort The sorting criteria (publish-time). (optional)
     * @param  string $sort_direction Whether to sort ascending or descending (ASC or DESC). (optional)
     * @param  int $offset The number of news to skip in range [0,10000] (optional)
     * @param  int $number The number of news to return in range [1,100] (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchNews'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function searchNewsWithHttpInfo($text = null, $source_countries = null, $language = null, $min_sentiment = null, $max_sentiment = null, $earliest_publish_date = null, $latest_publish_date = null, $news_sources = null, $authors = null, $categories = null, $entities = null, $location_filter = null, $sort = null, $sort_direction = null, $offset = null, $number = null, string $contentType = self::contentTypes['searchNews'][0])
    {
        $request = $this->searchNewsRequest($text, $source_countries, $language, $min_sentiment, $max_sentiment, $earliest_publish_date, $latest_publish_date, $news_sources, $authors, $categories, $entities, $location_filter, $sort, $sort_direction, $offset, $number, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation searchNewsAsync
     *
     * Search News
     *
     * @param  string $text The text to match in the news content (at least 3 characters, maximum 100 characters). By default all query terms are expected, you can use an uppercase OR to search for any terms, e.g. tesla OR ford (optional)
     * @param  string $source_countries A comma-separated list of ISO 3166 country codes from which the news should originate. (optional)
     * @param  string $language The ISO 6391 language code of the news. (optional)
     * @param  float $min_sentiment The minimal sentiment of the news in range [-1,1]. (optional)
     * @param  float $max_sentiment The maximal sentiment of the news in range [-1,1]. (optional)
     * @param  string $earliest_publish_date The news must have been published after this date. (optional)
     * @param  string $latest_publish_date The news must have been published before this date. (optional)
     * @param  string $news_sources A comma-separated list of news sources from which the news should originate. (optional)
     * @param  string $authors A comma-separated list of author names. Only news from any of the given authors will be returned. (optional)
     * @param  string $categories A comma-separated list of categories. Only news from any of the given categories will be returned. Possible categories are politics, sports, business, technology, entertainment, health, science, lifestyle, travel, culture, education, environment, other. (optional)
     * @param  string $entities Filter news by entities (see semantic types). (optional)
     * @param  string $location_filter Filter news by radius around a certain location. Format is \&quot;latitude,longitude,radius in kilometers\&quot;. Radius must be between 1 and 100 kilometers. (optional)
     * @param  string $sort The sorting criteria (publish-time). (optional)
     * @param  string $sort_direction Whether to sort ascending or descending (ASC or DESC). (optional)
     * @param  int $offset The number of news to skip in range [0,10000] (optional)
     * @param  int $number The number of news to return in range [1,100] (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function searchNewsAsync($text = null, $source_countries = null, $language = null, $min_sentiment = null, $max_sentiment = null, $earliest_publish_date = null, $latest_publish_date = null, $news_sources = null, $authors = null, $categories = null, $entities = null, $location_filter = null, $sort = null, $sort_direction = null, $offset = null, $number = null, string $contentType = self::contentTypes['searchNews'][0])
    {
        return $this->searchNewsAsyncWithHttpInfo($text, $source_countries, $language, $min_sentiment, $max_sentiment, $earliest_publish_date, $latest_publish_date, $news_sources, $authors, $categories, $entities, $location_filter, $sort, $sort_direction, $offset, $number, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation searchNewsAsyncWithHttpInfo
     *
     * Search News
     *
     * @param  string $text The text to match in the news content (at least 3 characters, maximum 100 characters). By default all query terms are expected, you can use an uppercase OR to search for any terms, e.g. tesla OR ford (optional)
     * @param  string $source_countries A comma-separated list of ISO 3166 country codes from which the news should originate. (optional)
     * @param  string $language The ISO 6391 language code of the news. (optional)
     * @param  float $min_sentiment The minimal sentiment of the news in range [-1,1]. (optional)
     * @param  float $max_sentiment The maximal sentiment of the news in range [-1,1]. (optional)
     * @param  string $earliest_publish_date The news must have been published after this date. (optional)
     * @param  string $latest_publish_date The news must have been published before this date. (optional)
     * @param  string $news_sources A comma-separated list of news sources from which the news should originate. (optional)
     * @param  string $authors A comma-separated list of author names. Only news from any of the given authors will be returned. (optional)
     * @param  string $categories A comma-separated list of categories. Only news from any of the given categories will be returned. Possible categories are politics, sports, business, technology, entertainment, health, science, lifestyle, travel, culture, education, environment, other. (optional)
     * @param  string $entities Filter news by entities (see semantic types). (optional)
     * @param  string $location_filter Filter news by radius around a certain location. Format is \&quot;latitude,longitude,radius in kilometers\&quot;. Radius must be between 1 and 100 kilometers. (optional)
     * @param  string $sort The sorting criteria (publish-time). (optional)
     * @param  string $sort_direction Whether to sort ascending or descending (ASC or DESC). (optional)
     * @param  int $offset The number of news to skip in range [0,10000] (optional)
     * @param  int $number The number of news to return in range [1,100] (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function searchNewsAsyncWithHttpInfo($text = null, $source_countries = null, $language = null, $min_sentiment = null, $max_sentiment = null, $earliest_publish_date = null, $latest_publish_date = null, $news_sources = null, $authors = null, $categories = null, $entities = null, $location_filter = null, $sort = null, $sort_direction = null, $offset = null, $number = null, string $contentType = self::contentTypes['searchNews'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\SearchNews200Response';
        $request = $this->searchNewsRequest($text, $source_countries, $language, $min_sentiment, $max_sentiment, $earliest_publish_date, $latest_publish_date, $news_sources, $authors, $categories, $entities, $location_filter, $sort, $sort_direction, $offset, $number, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'searchNews'
     *
     * @param  string $text The text to match in the news content (at least 3 characters, maximum 100 characters). By default all query terms are expected, you can use an uppercase OR to search for any terms, e.g. tesla OR ford (optional)
     * @param  string $source_countries A comma-separated list of ISO 3166 country codes from which the news should originate. (optional)
     * @param  string $language The ISO 6391 language code of the news. (optional)
     * @param  float $min_sentiment The minimal sentiment of the news in range [-1,1]. (optional)
     * @param  float $max_sentiment The maximal sentiment of the news in range [-1,1]. (optional)
     * @param  string $earliest_publish_date The news must have been published after this date. (optional)
     * @param  string $latest_publish_date The news must have been published before this date. (optional)
     * @param  string $news_sources A comma-separated list of news sources from which the news should originate. (optional)
     * @param  string $authors A comma-separated list of author names. Only news from any of the given authors will be returned. (optional)
     * @param  string $categories A comma-separated list of categories. Only news from any of the given categories will be returned. Possible categories are politics, sports, business, technology, entertainment, health, science, lifestyle, travel, culture, education, environment, other. (optional)
     * @param  string $entities Filter news by entities (see semantic types). (optional)
     * @param  string $location_filter Filter news by radius around a certain location. Format is \&quot;latitude,longitude,radius in kilometers\&quot;. Radius must be between 1 and 100 kilometers. (optional)
     * @param  string $sort The sorting criteria (publish-time). (optional)
     * @param  string $sort_direction Whether to sort ascending or descending (ASC or DESC). (optional)
     * @param  int $offset The number of news to skip in range [0,10000] (optional)
     * @param  int $number The number of news to return in range [1,100] (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['searchNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function searchNewsRequest($text = null, $source_countries = null, $language = null, $min_sentiment = null, $max_sentiment = null, $earliest_publish_date = null, $latest_publish_date = null, $news_sources = null, $authors = null, $categories = null, $entities = null, $location_filter = null, $sort = null, $sort_direction = null, $offset = null, $number = null, string $contentType = self::contentTypes['searchNews'][0])
    {

        if ($text !== null && strlen($text) > 100) {
            throw new \InvalidArgumentException('invalid length for "$text" when calling NewsApi.searchNews, must be smaller than or equal to 100.');
        }
        if ($text !== null && !preg_match("/./", $text)) {
            throw new \InvalidArgumentException("invalid value for \"text\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($source_countries !== null && strlen($source_countries) > 100) {
            throw new \InvalidArgumentException('invalid length for "$source_countries" when calling NewsApi.searchNews, must be smaller than or equal to 100.');
        }
        if ($source_countries !== null && !preg_match("/./", $source_countries)) {
            throw new \InvalidArgumentException("invalid value for \"source_countries\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($language !== null && strlen($language) > 2) {
            throw new \InvalidArgumentException('invalid length for "$language" when calling NewsApi.searchNews, must be smaller than or equal to 2.');
        }
        if ($language !== null && !preg_match("/./", $language)) {
            throw new \InvalidArgumentException("invalid value for \"language\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($min_sentiment !== null && $min_sentiment > 1) {
            throw new \InvalidArgumentException('invalid value for "$min_sentiment" when calling NewsApi.searchNews, must be smaller than or equal to 1.');
        }
        if ($min_sentiment !== null && $min_sentiment < -1) {
            throw new \InvalidArgumentException('invalid value for "$min_sentiment" when calling NewsApi.searchNews, must be bigger than or equal to -1.');
        }

        if ($max_sentiment !== null && $max_sentiment > 1) {
            throw new \InvalidArgumentException('invalid value for "$max_sentiment" when calling NewsApi.searchNews, must be smaller than or equal to 1.');
        }
        if ($max_sentiment !== null && $max_sentiment < -1) {
            throw new \InvalidArgumentException('invalid value for "$max_sentiment" when calling NewsApi.searchNews, must be bigger than or equal to -1.');
        }

        if ($earliest_publish_date !== null && strlen($earliest_publish_date) > 19) {
            throw new \InvalidArgumentException('invalid length for "$earliest_publish_date" when calling NewsApi.searchNews, must be smaller than or equal to 19.');
        }
        if ($earliest_publish_date !== null && !preg_match("/./", $earliest_publish_date)) {
            throw new \InvalidArgumentException("invalid value for \"earliest_publish_date\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($latest_publish_date !== null && strlen($latest_publish_date) > 19) {
            throw new \InvalidArgumentException('invalid length for "$latest_publish_date" when calling NewsApi.searchNews, must be smaller than or equal to 19.');
        }
        if ($latest_publish_date !== null && !preg_match("/./", $latest_publish_date)) {
            throw new \InvalidArgumentException("invalid value for \"latest_publish_date\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($news_sources !== null && strlen($news_sources) > 10000) {
            throw new \InvalidArgumentException('invalid length for "$news_sources" when calling NewsApi.searchNews, must be smaller than or equal to 10000.');
        }
        if ($news_sources !== null && !preg_match("/./", $news_sources)) {
            throw new \InvalidArgumentException("invalid value for \"news_sources\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($authors !== null && strlen($authors) > 300) {
            throw new \InvalidArgumentException('invalid length for "$authors" when calling NewsApi.searchNews, must be smaller than or equal to 300.');
        }
        if ($authors !== null && !preg_match("/./", $authors)) {
            throw new \InvalidArgumentException("invalid value for \"authors\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($categories !== null && strlen($categories) > 300) {
            throw new \InvalidArgumentException('invalid length for "$categories" when calling NewsApi.searchNews, must be smaller than or equal to 300.');
        }
        if ($categories !== null && !preg_match("/./", $categories)) {
            throw new \InvalidArgumentException("invalid value for \"categories\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($entities !== null && strlen($entities) > 10000) {
            throw new \InvalidArgumentException('invalid length for "$entities" when calling NewsApi.searchNews, must be smaller than or equal to 10000.');
        }
        if ($entities !== null && !preg_match("/./", $entities)) {
            throw new \InvalidArgumentException("invalid value for \"entities\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($location_filter !== null && strlen($location_filter) > 100) {
            throw new \InvalidArgumentException('invalid length for "$location_filter" when calling NewsApi.searchNews, must be smaller than or equal to 100.');
        }
        if ($location_filter !== null && !preg_match("/./", $location_filter)) {
            throw new \InvalidArgumentException("invalid value for \"location_filter\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($sort !== null && strlen($sort) > 100) {
            throw new \InvalidArgumentException('invalid length for "$sort" when calling NewsApi.searchNews, must be smaller than or equal to 100.');
        }
        if ($sort !== null && !preg_match("/./", $sort)) {
            throw new \InvalidArgumentException("invalid value for \"sort\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($sort_direction !== null && strlen($sort_direction) > 4) {
            throw new \InvalidArgumentException('invalid length for "$sort_direction" when calling NewsApi.searchNews, must be smaller than or equal to 4.');
        }
        if ($sort_direction !== null && !preg_match("/./", $sort_direction)) {
            throw new \InvalidArgumentException("invalid value for \"sort_direction\" when calling NewsApi.searchNews, must conform to the pattern /./.");
        }

        if ($offset !== null && $offset > 10000) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling NewsApi.searchNews, must be smaller than or equal to 10000.');
        }
        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('invalid value for "$offset" when calling NewsApi.searchNews, must be bigger than or equal to 0.');
        }

        if ($number !== null && $number > 100) {
            throw new \InvalidArgumentException('invalid value for "$number" when calling NewsApi.searchNews, must be smaller than or equal to 100.');
        }
        if ($number !== null && $number < 1) {
            throw new \InvalidArgumentException('invalid value for "$number" when calling NewsApi.searchNews, must be bigger than or equal to 1.');
        }


        $resourcePath = '/search-news';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $text,
            'text', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $source_countries,
            'source-countries', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $language,
            'language', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $min_sentiment,
            'min-sentiment', // param base name
            'number', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $max_sentiment,
            'max-sentiment', // param base name
            'number', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $earliest_publish_date,
            'earliest-publish-date', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $latest_publish_date,
            'latest-publish-date', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $news_sources,
            'news-sources', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $authors,
            'authors', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $categories,
            'categories', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $entities,
            'entities', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $location_filter,
            'location-filter', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $sort,
            'sort', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $sort_direction,
            'sort-direction', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $offset,
            'offset', // param base name
            'integer', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $number,
            'number', // param base name
            'integer', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation topNews
     *
     * Top News
     *
     * @param  string $source_country The ISO 3166 country code of the country for which top news should be retrieved. (required)
     * @param  string $language The ISO 6391 language code of the top news. The language must be one spoken in the source-country. (required)
     * @param  string $date The date for which the top news should be retrieved. If no date is given, the current day is assumed. (optional)
     * @param  bool $headlines_only Whether to only return basic information such as id, title, and url of the news. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['topNews'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amsaid\WorldNewsApi\Lib\Model\TopNews200Response
     */
    public function topNews($source_country, $language, $date = null, $headlines_only = null, string $contentType = self::contentTypes['topNews'][0])
    {
        list($response) = $this->topNewsWithHttpInfo($source_country, $language, $date, $headlines_only, $contentType);
        return $response;
    }

    /**
     * Operation topNewsWithHttpInfo
     *
     * Top News
     *
     * @param  string $source_country The ISO 3166 country code of the country for which top news should be retrieved. (required)
     * @param  string $language The ISO 6391 language code of the top news. The language must be one spoken in the source-country. (required)
     * @param  string $date The date for which the top news should be retrieved. If no date is given, the current day is assumed. (optional)
     * @param  bool $headlines_only Whether to only return basic information such as id, title, and url of the news. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['topNews'] to see the possible values for this operation
     *
     * @throws \Amsaid\WorldNewsApi\Lib\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amsaid\WorldNewsApi\Lib\Model\TopNews200Response, HTTP status code, HTTP response headers (array of strings)
     */
    public function topNewsWithHttpInfo($source_country, $language, $date = null, $headlines_only = null, string $contentType = self::contentTypes['topNews'][0])
    {
        $request = $this->topNewsRequest($source_country, $language, $date, $headlines_only, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch ($statusCode) {
                case 200:
                    if ('\Amsaid\WorldNewsApi\Lib\Model\TopNews200Response' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amsaid\WorldNewsApi\Lib\Model\TopNews200Response' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amsaid\WorldNewsApi\Lib\Model\TopNews200Response', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\Amsaid\WorldNewsApi\Lib\Model\TopNews200Response';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];
        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amsaid\WorldNewsApi\Lib\Model\TopNews200Response',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation topNewsAsync
     *
     * Top News
     *
     * @param  string $source_country The ISO 3166 country code of the country for which top news should be retrieved. (required)
     * @param  string $language The ISO 6391 language code of the top news. The language must be one spoken in the source-country. (required)
     * @param  string $date The date for which the top news should be retrieved. If no date is given, the current day is assumed. (optional)
     * @param  bool $headlines_only Whether to only return basic information such as id, title, and url of the news. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['topNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function topNewsAsync($source_country, $language, $date = null, $headlines_only = null, string $contentType = self::contentTypes['topNews'][0])
    {
        return $this->topNewsAsyncWithHttpInfo($source_country, $language, $date, $headlines_only, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation topNewsAsyncWithHttpInfo
     *
     * Top News
     *
     * @param  string $source_country The ISO 3166 country code of the country for which top news should be retrieved. (required)
     * @param  string $language The ISO 6391 language code of the top news. The language must be one spoken in the source-country. (required)
     * @param  string $date The date for which the top news should be retrieved. If no date is given, the current day is assumed. (optional)
     * @param  bool $headlines_only Whether to only return basic information such as id, title, and url of the news. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['topNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function topNewsAsyncWithHttpInfo($source_country, $language, $date = null, $headlines_only = null, string $contentType = self::contentTypes['topNews'][0])
    {
        $returnType = '\Amsaid\WorldNewsApi\Lib\Model\TopNews200Response';
        $request = $this->topNewsRequest($source_country, $language, $date, $headlines_only, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'topNews'
     *
     * @param  string $source_country The ISO 3166 country code of the country for which top news should be retrieved. (required)
     * @param  string $language The ISO 6391 language code of the top news. The language must be one spoken in the source-country. (required)
     * @param  string $date The date for which the top news should be retrieved. If no date is given, the current day is assumed. (optional)
     * @param  bool $headlines_only Whether to only return basic information such as id, title, and url of the news. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['topNews'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function topNewsRequest($source_country, $language, $date = null, $headlines_only = null, string $contentType = self::contentTypes['topNews'][0])
    {

        // verify the required parameter 'source_country' is set
        if ($source_country === null || (is_array($source_country) && count($source_country) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $source_country when calling topNews'
            );
        }
        if (strlen($source_country) > 2) {
            throw new \InvalidArgumentException('invalid length for "$source_country" when calling NewsApi.topNews, must be smaller than or equal to 2.');
        }
        if (!preg_match("/./", $source_country)) {
            throw new \InvalidArgumentException("invalid value for \"source_country\" when calling NewsApi.topNews, must conform to the pattern /./.");
        }

        // verify the required parameter 'language' is set
        if ($language === null || (is_array($language) && count($language) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $language when calling topNews'
            );
        }
        if (strlen($language) > 2) {
            throw new \InvalidArgumentException('invalid length for "$language" when calling NewsApi.topNews, must be smaller than or equal to 2.');
        }
        if (!preg_match("/./", $language)) {
            throw new \InvalidArgumentException("invalid value for \"language\" when calling NewsApi.topNews, must conform to the pattern /./.");
        }

        if ($date !== null && strlen($date) > 10) {
            throw new \InvalidArgumentException('invalid length for "$date" when calling NewsApi.topNews, must be smaller than or equal to 10.');
        }
        if ($date !== null && !preg_match("/./", $date)) {
            throw new \InvalidArgumentException("invalid value for \"date\" when calling NewsApi.topNews, must conform to the pattern /./.");
        }



        $resourcePath = '/top-news';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $source_country,
            'source-country', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $language,
            'language', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            true // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $date,
            'date', // param base name
            'string', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $headlines_only,
            'headlines-only', // param base name
            'boolean', // openApiType
            'form', // style
            false, // explode
            false // required
        ) ?? []);




        $headers = $this->headerSelector->selectHeaders(
            ['application/json',],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);
            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('api-key');
        if ($apiKey !== null) {
            $queryParams['api-key'] = $apiKey;
        }
        // this endpoint requires API key authentication
        $apiKey = $this->config->getApiKeyWithPrefix('x-api-key');
        if ($apiKey !== null) {
            $headers['x-api-key'] = $apiKey;
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
