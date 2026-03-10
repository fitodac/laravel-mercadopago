<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Http\Requests\StoreWebhookRequest;
use Fitodac\LaravelMercadoPago\Services\WebhookService;
use Throwable;

final class WebhookController extends AbstractApiController
{
    public function store(StoreWebhookRequest $request, WebhookService $webhookService)
    {
        try {
            return $this->success($webhookService->handle($request));
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
