<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\MercadoPagoClientFactory;

class RefundService
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function create(string $paymentId, array $payload = []): mixed
    {
        $client = $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Payment\\PaymentRefundClient',
        ]);

        if (array_key_exists('amount', $payload)) {
            return $this->clientFactory->callFirstAvailable(
                $client,
                ['refund'],
                (int) $paymentId,
                (float) $payload['amount'],
            );
        }

        return $this->clientFactory->callFirstAvailable(
            $client,
            ['refundTotal'],
            (int) $paymentId,
        );
    }
}
