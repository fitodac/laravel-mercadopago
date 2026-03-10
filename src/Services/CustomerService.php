<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;

class CustomerService
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function create(array $payload): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Customer\\CustomerClient',
        ]);

        return $this->clientFactory->callFirstAvailable($client, ['create'], $payload);
    }

    public function get(string $customerId): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Customer\\CustomerClient',
        ]);

        return $this->clientFactory->callFirstAvailable($client, ['get'], $customerId);
    }
}
