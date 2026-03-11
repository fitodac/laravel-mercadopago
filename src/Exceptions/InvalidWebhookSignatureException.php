<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Exceptions;

use RuntimeException;
use Throwable;

final class InvalidWebhookSignatureException extends RuntimeException
{
    public static function malformedHeader(): self
    {
        return new self(self::translate(
            'mercadopago::mercadopago.exceptions.webhook.malformed_header',
            'Mercado Pago webhook signature header is malformed.',
        ));
    }

    public static function signatureMismatch(): self
    {
        return new self(self::translate(
            'mercadopago::mercadopago.exceptions.webhook.invalid_signature',
            'Mercado Pago webhook signature is invalid.',
        ));
    }

    private static function translate(string $key, string $fallback): string
    {
        try {
            return __($key);
        } catch (Throwable) {
            return $fallback;
        }
    }
}
