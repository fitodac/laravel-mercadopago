<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;

class PreferenceService
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function create(array $payload): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Preference\\PreferenceClient',
        ]);

        return $this->clientFactory->callFirstAvailable($client, ['create'], $payload);
    }
}
