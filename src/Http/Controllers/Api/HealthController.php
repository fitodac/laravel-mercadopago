<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Contracts\CredentialResolverInterface;
use Throwable;

final class HealthController extends AbstractApiController
{
    public function __invoke(CredentialResolverInterface $credentialResolver)
    {
        try {
            $credentials = $credentialResolver->resolve();

            return $this->success([
                'configured' => true,
                'has_public_key' => $credentials->publicKey !== null,
                'has_webhook_secret' => $credentials->webhookSecret !== null,
                'environment' => app()->environment(),
            ]);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
