<?php

declare(strict_types=1);

use Fitodac\LaravelMercadoPago\Support\EnvCredentialResolver;

return [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
    'route_prefix' => env('MERCADOPAGO_ROUTE_PREFIX', 'api/mercadopago'),
    'enable_demo_routes' => (bool) env('MERCADOPAGO_ENABLE_DEMO_ROUTES', true),
    'runtime_environment' => env('MERCADOPAGO_RUNTIME_ENVIRONMENT'),
    'credential_resolver' => EnvCredentialResolver::class,
];
