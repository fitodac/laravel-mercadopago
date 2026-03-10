<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Exceptions\InvalidWebhookSignatureException;
use Fitodac\LaravelMercadoPago\Exceptions\MercadoPagoConfigurationException;
use Fitodac\LaravelMercadoPago\Support\ResponseNormalizer;
use Illuminate\Http\JsonResponse;
use MercadoPago\Exceptions\MPApiException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

abstract class AbstractApiController
{
    protected function success(mixed $data, int $status = 200, array $meta = []): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'data' => ResponseNormalizer::normalize($data),
            'meta' => $meta,
        ], $status);
    }

    protected function failure(Throwable $throwable): JsonResponse
    {
        $status = match (true) {
            $throwable instanceof MPApiException => $throwable->getStatusCode(),
            $throwable instanceof MercadoPagoConfigurationException => 422,
            $throwable instanceof InvalidWebhookSignatureException => 401,
            $throwable instanceof HttpExceptionInterface => $throwable->getStatusCode(),
            default => 500,
        };

        $payload = [
            'ok' => false,
            'message' => $throwable->getMessage(),
        ];

        if ($throwable instanceof MPApiException) {
            $payload['details'] = $throwable->getApiResponse()->getContent();
        }

        return response()->json($payload, $status);
    }
}
