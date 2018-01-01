<?php

namespace PascalWoerde\CoinMarketCap;

use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Class ClientTest.
 */
class ClientTest extends TestCase
{
    /**
     * @return array
     */
    public function endpointProvider(): array
    {
        return [
            [
                'ticker',
                [10, 100, 'USD'],
                'https://example.com/ticker/?start=10&limit=100&convert=USD',
            ],
            [
                'tickerSpecificCurrency',
                ['bitcoin', 10, 100, 'USD'],
                'https://example.com/ticker/bitcoin/?start=10&limit=100&convert=USD',
            ],
            [
                'globalData',
                ['USD'],
                'https://example.com/global/?convert=USD',
            ],
        ];
    }

    /**
     * Ensure that the called endpoint has pointed to the right URI.
     *
     * @param string $method
     * @param array  $parameters
     * @param string $expectedUri
     *
     * @dataProvider endpointProvider
     */
    public function testEndpoints(string $method, array $parameters, string $expectedUri)
    {
        $httpClientMock = $this->getMockBuilder(HttpClient::class)
            ->setMethods(['sendRequest'])
            ->getMock();

        $httpClientMock->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($expectedUri) {
                return $request->getUri()->__toString() === $expectedUri;
            }))
            ->willReturn(new Response());

        $client = new Client($httpClientMock, null, 'https://example.com/');
        call_user_func([$client, $method], ...$parameters);
    }
}
