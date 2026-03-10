<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Exceptions;

use RuntimeException;

final class MercadoPagoConfigurationException extends RuntimeException
{
    public static function missingAccessToken(): self
    {
        return new self('Mercado Pago access token is not configured.');
    }

    public static function sdkNotInstalled(): self
    {
        return new self('Mercado Pago PHP SDK is not installed.');
    }

    public static function clientClassNotFound(array $classNames): self
    {
        return new self(
            sprintf(
                'No Mercado Pago SDK client class was found. Attempted: %s',
                implode(', ', $classNames),
            ),
        );
    }

    public static function clientMethodNotFound(object $client, array $methodNames): self
    {
        return new self(
            sprintf(
                'No supported client method was found on %s. Attempted: %s',
                $client::class,
                implode(', ', $methodNames),
            ),
        );
    }
}
