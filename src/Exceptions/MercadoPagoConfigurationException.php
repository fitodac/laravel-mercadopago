<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Exceptions;

use RuntimeException;
use Throwable;

final class MercadoPagoConfigurationException extends RuntimeException
{
    public static function missingAccessToken(): self
    {
        return new self(self::translate(
            'mercadopago::mercadopago.exceptions.configuration.missing_access_token',
            'Mercado Pago access token is not configured.',
        ));
    }

    public static function sdkNotInstalled(): self
    {
        return new self(self::translate(
            'mercadopago::mercadopago.exceptions.configuration.sdk_not_installed',
            'Mercado Pago PHP SDK is not installed.',
        ));
    }

    public static function clientClassNotFound(array $classNames): self
    {
        return new self(
            self::translate(
                'mercadopago::mercadopago.exceptions.configuration.client_class_not_found',
                'No Mercado Pago SDK client class was found. Attempted: :classes',
                ['classes' => implode(', ', $classNames)],
            ),
        );
    }

    public static function clientMethodNotFound(object $client, array $methodNames): self
    {
        return new self(
            self::translate(
                'mercadopago::mercadopago.exceptions.configuration.client_method_not_found',
                'No supported client method was found on :client. Attempted: :methods',
                [
                    'client' => $client::class,
                    'methods' => implode(', ', $methodNames),
                ],
            ),
        );
    }

    /**
     * @param  array<string, string>  $replace
     */
    private static function translate(string $key, string $fallback, array $replace = []): string
    {
        try {
            return __($key, $replace);
        } catch (Throwable) {
            return strtr($fallback, array_combine(
                array_map(static fn (string $item): string => ':' . $item, array_keys($replace)),
                array_values($replace),
            ) ?: []);
        }
    }
}
