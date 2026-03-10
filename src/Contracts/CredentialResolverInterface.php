<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Contracts;

use Fitodac\LaravelMercadoPago\DTO\MercadoPagoCredentials;

interface CredentialResolverInterface
{
    public function resolve(): MercadoPagoCredentials;
}
