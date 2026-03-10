<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Http\Requests\CreatePreferenceRequest;
use Fitodac\LaravelMercadoPago\Services\PreferenceService;
use Throwable;

final class PreferenceController extends AbstractApiController
{
    public function store(
        CreatePreferenceRequest $request,
        PreferenceService $preferenceService,
    ) {
        try {
            return $this->success($preferenceService->create($request->validated()), 201);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
