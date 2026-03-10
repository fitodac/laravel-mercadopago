<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Exceptions;

use RuntimeException;

final class InvalidWebhookSignatureException extends RuntimeException
{
    public static function malformedHeader(): self
    {
        return new self('Mercado Pago webhook signature header is malformed.');
    }

    public static function signatureMismatch(): self
    {
        return new self('Mercado Pago webhook signature is invalid.');
    }
}
