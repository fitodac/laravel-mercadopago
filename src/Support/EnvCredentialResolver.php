<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Support;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Fitodac\LaravelMercadoPago\DTO\MercadoPagoCredentials;
use Fitodac\LaravelMercadoPago\Exceptions\MercadoPagoConfigurationException;
use Illuminate\Contracts\Config\Repository;

final readonly class EnvCredentialResolver implements CredentialResolverInterface
{
    public function __construct(
        private Repository $config,
    ) {}

    public function resolve(): MercadoPagoCredentials
    {
        $accessToken = (string) $this->config->get('mercadopago.access_token', '');

        if ($accessToken === '') {
            throw MercadoPagoConfigurationException::missingAccessToken();
        }

        $publicKey = $this->config->get('mercadopago.public_key');
        $webhookSecret = $this->config->get('mercadopago.webhook_secret');

        return new MercadoPagoCredentials(
            accessToken: $accessToken,
            publicKey: is_string($publicKey) && $publicKey !== '' ? $publicKey : null,
            webhookSecret: is_string($webhookSecret) && $webhookSecret !== '' ? $webhookSecret : null,
        );
    }
}
