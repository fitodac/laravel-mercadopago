<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Tests\Feature;

use Fitodac\LaravelMercadoPago\Services\PaymentMethodService;
use Fitodac\LaravelMercadoPago\Services\PreferenceService;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

final class DemoRoutesTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_health_endpoint_responds_with_configuration_status(): void
    {
        config()->set('mercadopago.access_token', 'token');
        config()->set('mercadopago.public_key', 'public');
        config()->set('mercadopago.webhook_secret', 'secret');
        config()->set('mercadopago.enable_demo_routes', true);

        $this->getJson('/api/mercadopago/health')
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.configured', true)
            ->assertJsonPath('data.has_public_key', true)
            ->assertJsonPath('data.has_webhook_secret', true);
    }

    public function test_demo_routes_return_404_when_disabled(): void
    {
        config()->set('mercadopago.access_token', 'token');
        config()->set('mercadopago.enable_demo_routes', false);

        $this->getJson('/api/mercadopago/health')->assertNotFound();
    }

    public function test_payment_methods_endpoint_delegates_to_the_service(): void
    {
        config()->set('mercadopago.enable_demo_routes', true);

        $mock = Mockery::mock(PaymentMethodService::class);
        $mock->shouldReceive('all')
            ->once()
            ->andReturn([['id' => 'visa', 'name' => 'Visa']]);

        $this->app->instance(PaymentMethodService::class, $mock);

        $this->getJson('/api/mercadopago/payment-methods')
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.0.id', 'visa');
    }

    public function test_preference_endpoint_validates_and_delegates_to_the_service(): void
    {
        config()->set('mercadopago.enable_demo_routes', true);

        $mock = Mockery::mock(PreferenceService::class);
        $mock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(fn (array $payload): bool => $payload['items'][0]['title'] === 'Producto demo'))
            ->andReturn(['id' => 'pref_123']);

        $this->app->instance(PreferenceService::class, $mock);

        $this->postJson('/api/mercadopago/preferences', [
            'items' => [
                [
                    'title' => 'Producto demo',
                    'quantity' => 1,
                    'unit_price' => 100.5,
                ],
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('data.id', 'pref_123');
    }

    public function test_package_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('mercadopago.health'));
        $this->assertTrue(Route::has('mercadopago.webhooks.store'));
    }
}
