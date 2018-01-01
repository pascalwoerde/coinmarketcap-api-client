<?php

namespace PascalWoerde\CoinMarketCap;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 *
 * @author Pascal Woerde
 */
class Client
{
    /** @var HttpClient */
    protected $httpClient;

    /** @var MessageFactory */
    protected $messageFactory;

    /** @var string */
    protected $baseUri;

    /**
     * Client constructor.
     *
     * @param HttpClient|null $httpClient
     * @param MessageFactory  $messageFactory
     * @param string          $baseUri
     */
    public function __construct(
        HttpClient $httpClient = null,
        MessageFactory $messageFactory = null,
        string $baseUri = 'https://api.coinmarketcap.com/v1/'
    ) {
        $this->httpClient = $httpClient ?? HttpClientDiscovery::find();
        $this->messageFactory = $messageFactory ?? MessageFactoryDiscovery::find();
        $this->baseUri = $baseUri;
    }

    /**
     * @param string $path
     *
     * @return ResponseInterface
     */
    protected function get(string $path): ResponseInterface
    {
        $request = $this->messageFactory->createRequest('get', $this->baseUri.$path);

        return $this->httpClient->sendRequest($request);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return mixed
     */
    protected function parseJsonResponse(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();
        if ('application/json' === $response->getHeaderLine('Content-Type')) {
            return json_decode($content, true);
        }

        return null;
    }

    /**
     * Ticker.
     *
     * @see https://coinmarketcap.com/api/
     *
     * @param int         $start   return results from rank [start] and above
     * @param int         $limit   return a maximum of [limit] results (use 0 to return all results)
     * @param string|null $convert return price, 24h volume, and market cap in terms of another currency.
     *                             Valid values are:
     *                             "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF",
     *                             "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN",
     *                             "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     *
     * @return array
     */
    public function ticker(int $start = 0, int $limit = 0, string $convert = 'USD'): array
    {
        $parameters = ['start' => $start, 'limit' => $limit, 'convert' => $convert];

        $response = $this->get(sprintf('ticker/?%s', http_build_query($parameters)));

        return (array) $this->parseJsonResponse($response);
    }

    /**
     * Ticker (Specific Currency).
     *
     * @see https://coinmarketcap.com/api/
     *
     * @param string      $id      return price, 24h volume, and market cap in terms of another currency.
     *                             Valid values are:
     *                             "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF",
     *                             "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN",
     *                             "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     * @param int         $start   return results from rank [start] and above
     * @param int         $limit   return a maximum of [limit] results (use 0 to return all results)
     * @param string|null $convert return price, 24h volume, and market cap in terms of another currency.
     *                             Valid values are:
     *                             "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF",
     *                             "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN",
     *                             "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     *
     * @return mixed
     */
    public function tickerSpecificCurrency(string $id, int $start = 0, int $limit = 0, string $convert = 'USD'): array
    {
        $parameters = ['start' => $start, 'limit' => $limit, 'convert' => $convert];

        $response = $this->get(sprintf('ticker/%s/?%s', $id, http_build_query($parameters)));

        return (array) $this->parseJsonResponse($response);
    }

    /**
     * Global Data.
     *
     * @see https://coinmarketcap.com/api/
     *
     * @param string|null $convert return price, 24h volume, and market cap in terms of another currency.
     *                             Valid values are:
     *                             "AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF",
     *                             "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN",
     *                             "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR"
     *
     * @return array
     */
    public function globalData(string $convert = 'USD'): array
    {
        $parameters = ['convert' => $convert];

        $response = $this->get(sprintf('global/?%s', http_build_query($parameters)));

        return (array) $this->parseJsonResponse($response);
    }
}
