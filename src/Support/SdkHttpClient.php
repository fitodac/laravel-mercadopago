<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Support;

use MercadoPago\Client\MercadoPagoClient;
use MercadoPago\Net\HttpMethod;
use MercadoPago\Net\MPResponse;

final readonly class SdkHttpClient
{
    public function __construct(
        private MercadoPagoClientFactory $clientFactory,
    ) {}

    public function post(string $uri, array $payload): array
    {
        $this->clientFactory->makeFirstAvailable([
            'MercadoPago\\Client\\Payment\\PaymentClient',
        ]);

        $client = new class (\MercadoPago\MercadoPagoConfig::getHttpClient()) extends MercadoPagoClient {
            public function postJson(string $uri, array $payload): MPResponse
            {
                return $this->send($uri, HttpMethod::POST, json_encode($payload));
            }
        };

        return $client->postJson($uri, $payload)->getContent();
    }
}
