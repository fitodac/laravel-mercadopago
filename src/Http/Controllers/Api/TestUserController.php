<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Http\Requests\CreateTestUserRequest;
use Fitodac\LaravelMercadoPago\Services\TestUserService;
use Throwable;

final class TestUserController extends AbstractApiController
{
    public function store(
        CreateTestUserRequest $request,
        TestUserService $testUserService,
    ) {
        try {
            return $this->success($testUserService->create($request->validated()), 201);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
