<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Services\PaymentMethodService;
use Throwable;

final class PaymentMethodController extends AbstractApiController
{
    public function index(PaymentMethodService $paymentMethodService)
    {
        try {
            return $this->success($paymentMethodService->all());
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
