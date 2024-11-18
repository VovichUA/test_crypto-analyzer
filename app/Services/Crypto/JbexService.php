<?php

namespace App\Services\Crypto;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use TheSeer\Tokenizer\Exception;

class JbexService extends AbstractCryptoService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.jbex.com',
            'timeout'  => 10.0
        ]);
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function getSymbols(): array
    {
        $response = $this->client->get('/openapi/v1/pairs');
        $data = json_decode($response->getBody()->getContents(), true);

        return array_column($data, 'symbol');
    }

    /**
     * @param string $pair
     * @return array|null
     */
    public function getPrice(string $pair): ?array
    {
        $pair = str_replace('/', '', $pair);
        try {
            $response = $this->client->get(sprintf('/openapi/quote/v1/ticker/price?symbol=%s', $pair));

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return null;
        }
    }
}
