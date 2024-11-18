<?php

namespace App\Services\Crypto;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class BinanceService extends AbstractCryptoService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://api.binance.com']);
    }

    /**
     * @param string $pair
     * @return array|null
     */
    public function getPrice(string $pair): ?array
    {
        try {
            $pair = str_replace('/', '', $pair);
            $response = $this->client->get(sprintf('/api/v3/ticker/price?symbol=%s', $pair));
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    /**
     * @throws GuzzleException
     */
    public function getSymbols(): array
    {
        $response = $this->client->get('/api/v3/exchangeInfo');
        $data = json_decode($response->getBody()->getContents(), true);
        return array_column($data['symbols'], 'symbol');
    }
}
