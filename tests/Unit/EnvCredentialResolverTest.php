<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Tests\Unit;

use Fitodac\LaravelMercadoPago\Exceptions\MercadoPagoConfigurationException;
use Fitodac\LaravelMercadoPago\Support\EnvCredentialResolver;
use Illuminate\Config\Repository;
use PHPUnit\Framework\TestCase;

final class EnvCredentialResolverTest extends TestCase
{
    public function test_it_resolves_credentials_from_config(): void
    {
        $resolver = new EnvCredentialResolver(new Repository([
            'mercadopago.access_token' => 'token',
            'mercadopago.public_key' => 'public',
            'mercadopago.webhook_secret' => 'secret',
        ]));

        $credentials = $resolver->resolve();

        $this->assertSame('token', $credentials->accessToken);
        $this->assertSame('public', $credentials->publicKey);
        $this->assertSame('secret', $credentials->webhookSecret);
    }

    public function test_it_throws_when_access_token_is_missing(): void
    {
        $this->expectException(MercadoPagoConfigurationException::class);

        $resolver = new EnvCredentialResolver(new Repository([
            'mercadopago.access_token' => '',
        ]));

        $resolver->resolve();
    }
}
