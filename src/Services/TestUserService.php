<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Support\SdkHttpClient;

class TestUserService
{
    public function __construct(
        private SdkHttpClient $sdkHttpClient,
    ) {}

    public function create(array $payload): mixed
    {
        return $this->sdkHttpClient->post('/users/test_user', $payload);
    }
}
