<?php

namespace App\Services\Crypto;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BybitService extends AbstractCryptoService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.bybit.com']);
    }

    /**
     * @param string $pair
     * @return array|null
     */
    public function getPrice(string $pair): ?array
    {
        try {
            $pair = str_replace('/', '', $pair);
            $response = $this->client->get(sprintf('/v5/market/tickers?category=spot&symbol=%s', $pair));
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'price' => $data['result']['list'][0]['lastPrice'] ?? null,
            ];
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
        $response = $this->client->get('/v5/market/tickers?category=spot');
        $data = json_decode($response->getBody()->getContents(), true);

        return array_column($data['result']['list'], 'symbol');
    }
}
