<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;

class PaymentMethodService
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function all(): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\PaymentMethod\\PaymentMethodClient',
        ]);

        return $this->clientFactory->callFirstAvailable($client, ['list', 'getAll']);
    }
}
