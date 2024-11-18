<?php

namespace App\Services\Crypto;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class WhitebitService extends AbstractCryptoService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://whitebit.com']);
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getSymbols(): array
    {
        $response = $this->client->get('/api/v4/public/markets');
        $data = json_decode($response->getBody()->getContents(), true);

        return array_column($data, 'name');
    }

    /**
     * @param string $pair
     * @return null[]|null
     */
    public function getPrice(string $pair): ?array
    {
        try {
            $pair = str_replace('/', '_', $pair);
            $response = $this->client->get(sprintf('/api/v1/public/ticker?market=%s', $pair));
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'price' => $data['result']['last'] ?? null
            ];
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
