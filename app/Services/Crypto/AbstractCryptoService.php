<?php

namespace App\Services\Crypto;

abstract class AbstractCryptoService
{
    abstract public function getSymbols(): array;

    abstract public function getPrice(string $pair): ?array;
}
