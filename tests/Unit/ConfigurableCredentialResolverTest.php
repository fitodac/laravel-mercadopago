<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Tests\Unit;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Fitodac\LaravelMercadoPago\DTO\MercadoPagoCredentials;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ConfigurableCredentialResolverTest extends TestCase
{
    public function test_package_binds_the_configured_credential_resolver(): void
    {
        config()->set('mercadopago.credential_resolver', ConfigurableCredentialResolverFake::class);

        Route::get('/_resolver-check', function (CredentialResolverInterface $resolver) {
            return response()->json([
                'access_token' => $resolver->resolve()->accessToken,
            ]);
        });

        $this->getJson('/_resolver-check')
            ->assertOk()
            ->assertJsonPath('access_token', 'configured-token');
    }
}

final class ConfigurableCredentialResolverFake implements CredentialResolverInterface
{
    public function resolve(): MercadoPagoCredentials
    {
        return new MercadoPagoCredentials(
            accessToken: 'configured-token',
            publicKey: 'configured-public',
            webhookSecret: 'configured-secret',
        );
    }
}
