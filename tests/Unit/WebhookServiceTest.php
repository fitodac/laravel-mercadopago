<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Tests\Unit;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Fitodac\LaravelMercadoPago\DTO\MercadoPagoCredentials;
use Fitodac\LaravelMercadoPago\Exceptions\InvalidWebhookSignatureException;
use Fitodac\LaravelMercadoPago\Services\WebhookService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

final class WebhookServiceTest extends TestCase
{
    public function test_it_validates_a_signed_webhook_request(): void
    {
        $secret = 'super-secret';
        $request = Request::create(
            '/api/mercadopago/webhooks?data.id=123&topic=payment',
            'POST',
            ['type' => 'payment', 'data' => ['id' => '123']],
            server: [
                'HTTP_X_REQUEST_ID' => 'request-123',
                'HTTP_X_SIGNATURE' => 'ts=1704908010,v1=' . hash_hmac(
                    'sha256',
                    'id:123;request-id:request-123;ts:1704908010;',
                    $secret,
                ),
            ],
        );

        $service = new WebhookService(new class ($secret) implements CredentialResolverInterface {
            public function __construct(
                private readonly string $secret,
            ) {}

            public function resolve(): MercadoPagoCredentials
            {
                return new MercadoPagoCredentials(
                    accessToken: 'token',
                    webhookSecret: $this->secret,
                );
            }
        });

        $result = $service->handle($request);

        $this->assertTrue($result['acknowledged']);
        $this->assertTrue($result['validated']);
        $this->assertSame('payment', $result['topic']);
    }

    public function test_it_rejects_an_invalid_signature(): void
    {
        $this->expectException(InvalidWebhookSignatureException::class);

        $request = Request::create(
            '/api/mercadopago/webhooks?data.id=123',
            'POST',
            ['type' => 'payment', 'data' => ['id' => '123']],
            server: [
                'HTTP_X_REQUEST_ID' => 'request-123',
                'HTTP_X_SIGNATURE' => 'ts=1704908010,v1=invalid',
            ],
        );

        $service = new WebhookService(new class () implements CredentialResolverInterface {
            public function resolve(): MercadoPagoCredentials
            {
                return new MercadoPagoCredentials(
                    accessToken: 'token',
                    webhookSecret: 'super-secret',
                );
            }
        });

        $service->handle($request);
    }
}
