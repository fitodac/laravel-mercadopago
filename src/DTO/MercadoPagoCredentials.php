<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\DTO;

final readonly class MercadoPagoCredentials
{
    public function __construct(
        public string $accessToken,
        public ?string $publicKey = null,
        public ?string $webhookSecret = null,
    ) {}

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'public_key' => $this->publicKey,
            'webhook_secret' => $this->webhookSecret,
        ];
    }
}
