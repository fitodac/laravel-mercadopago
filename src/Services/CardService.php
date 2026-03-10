<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;

class CardService
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function create(string $customerId, array $payload): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Customer\\CustomerCardClient',
        ]);

        return $this->clientFactory->callFirstAvailable(
            $client,
            ['create', 'createCard'],
            $customerId,
            $payload,
        );
    }

    public function delete(string $customerId, string $cardId): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Customer\\CustomerCardClient',
        ]);

        return $this->clientFactory->callFirstAvailable(
            $client,
            ['delete'],
            $customerId,
            $cardId,
        );
    }
}
