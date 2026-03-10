<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Tests\Feature;

use Tests\TestCase;

final class WebhookControllerTest extends TestCase
{
    public function test_webhook_endpoint_accepts_unsigned_payload_when_secret_is_not_configured(): void
    {
        config()->set('mercadopago.access_token', 'token');
        config()->set('mercadopago.webhook_secret', null);

        $this->postJson('/api/mercadopago/webhooks?data.id=123&topic=payment', [
            'type' => 'payment',
            'data' => ['id' => '123'],
        ])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('data.acknowledged', true)
            ->assertJsonPath('data.validated', false);
    }
}
