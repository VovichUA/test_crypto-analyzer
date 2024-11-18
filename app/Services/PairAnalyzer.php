<?php

namespace App\Services;

class PairAnalyzer
{
    private array $services;

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * @return array
     */
    public function getCommonPairs(): array
    {
        return array_map(fn($service) => $service->getSymbols(), $this->services);
    }

    public function getPriceDataForPair(string $pair): array
    {
        $prices = [];
        foreach ($this->services as $service) {
            $price = $service->getPrice($pair);
            if ($price) {
                $prices[] = [
                    'exchange' => get_class($service),
                    'price' => $price['price'] ?? null,
                ];
            }
        }

        return $prices;
    }

}
