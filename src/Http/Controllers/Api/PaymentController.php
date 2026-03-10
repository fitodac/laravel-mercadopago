<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Http\Requests\CreatePaymentRequest;
use Fitodac\LaravelMercadoPago\Http\Requests\CreateRefundRequest;
use Fitodac\LaravelMercadoPago\Services\PaymentService;
use Fitodac\LaravelMercadoPago\Services\RefundService;
use Throwable;

final class PaymentController extends AbstractApiController
{
    public function store(
        CreatePaymentRequest $request,
        PaymentService $paymentService,
    ) {
        try {
            return $this->success($paymentService->create($request->validated()), 201);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }

    public function show(string $paymentId, PaymentService $paymentService)
    {
        try {
            return $this->success($paymentService->get($paymentId));
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }

    public function refund(
        CreateRefundRequest $request,
        string $paymentId,
        RefundService $refundService,
    ) {
        try {
            return $this->success($refundService->create($paymentId, $request->validated()), 201);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
