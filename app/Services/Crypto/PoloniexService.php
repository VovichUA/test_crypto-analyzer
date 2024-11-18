<?php

namespace App\Services\Crypto;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class PoloniexService extends AbstractCryptoService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.poloniex.com',
            'timeout'  => 10.0,
        ]);
    }

    /**
     * @param string $pair
     * @return array|null
     */
    public function getPrice(string $pair): ?array
    {
        try {
            $pair = str_replace('/', '_', $pair);
            $response = $this->client->get(sprintf('/markets/%s/price', $pair));
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getSymbols(): array
    {
        $response = $this->client->get('/markets');
        $data = json_decode($response->getBody()->getContents(), true);

        return array_column($data, 'symbol');
    }
}
