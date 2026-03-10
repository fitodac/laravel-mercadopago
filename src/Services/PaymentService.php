<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;

class PaymentService
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function create(array $payload): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Payment\\PaymentClient',
        ]);

        return $this->clientFactory->callFirstAvailable($client, ['create'], $payload);
    }

    public function get(string $paymentId): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Payment\\PaymentClient',
        ]);

        return $this->clientFactory->callFirstAvailable($client, ['get'], $paymentId);
    }
}
