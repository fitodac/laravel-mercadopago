<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Services;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Fitodac\LaravelMercadoPago\Exceptions\InvalidWebhookSignatureException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WebhookService
{
    public function __construct(
        private CredentialResolverInterface $credentialResolver,
    ) {}

    public function handle(Request $request): array
    {
        $payload = $request->all();
        $secret = $this->credentialResolver->resolve()->webhookSecret;
        $signatureHeader = $request->header('x-signature');

        if ($secret !== null && $signatureHeader !== null && $signatureHeader !== '') {
            $this->assertValidSignature($request, $payload, $secret, $signatureHeader);
        }

        return [
            'acknowledged' => true,
            'validated' => $secret !== null && $signatureHeader !== null && $signatureHeader !== '',
            'topic' => $request->query('topic', Arr::get($payload, 'type')),
            'resource' => Arr::get($payload, 'data.id', $request->query('data.id')),
            'payload' => $payload,
        ];
    }

    private function assertValidSignature(
        Request $request,
        array $payload,
        string $secret,
        string $signatureHeader,
    ): void {
        $signatureParts = $this->parseSignatureHeader($signatureHeader);
        $resourceId = (string) ($request->query('data.id') ?? Arr::get($payload, 'data.id', ''));
        $requestId = (string) $request->header('x-request-id', '');

        $manifest = sprintf(
            'id:%s;request-id:%s;ts:%s;',
            $resourceId,
            $requestId,
            $signatureParts['ts'],
        );

        $computedHash = hash_hmac('sha256', $manifest, $secret);

        if (! hash_equals($computedHash, $signatureParts['v1'])) {
            throw InvalidWebhookSignatureException::signatureMismatch();
        }
    }

    private function parseSignatureHeader(string $signatureHeader): array
    {
        $parts = [];

        foreach (explode(',', $signatureHeader) as $piece) {
            [$key, $value] = array_pad(explode('=', trim($piece), 2), 2, null);

            if ($key !== null && $value !== null) {
                $parts[Str::lower($key)] = $value;
            }
        }

        if (! isset($parts['ts'], $parts['v1'])) {
            throw InvalidWebhookSignatureException::malformedHeader();
        }

        return $parts;
    }
}
