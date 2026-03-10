<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Controllers\Api;

use Fitodac\LaravelMercadoPago\Http\Requests\CreateCustomerRequest;
use Fitodac\LaravelMercadoPago\Http\Requests\StoreCustomerCardRequest;
use Fitodac\LaravelMercadoPago\Services\CardService;
use Fitodac\LaravelMercadoPago\Services\CustomerService;
use Throwable;

final class CustomerController extends AbstractApiController
{
    public function store(
        CreateCustomerRequest $request,
        CustomerService $customerService,
    ) {
        try {
            return $this->success($customerService->create($request->validated()), 201);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }

    public function show(string $customerId, CustomerService $customerService)
    {
        try {
            return $this->success($customerService->get($customerId));
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }

    public function storeCard(
        StoreCustomerCardRequest $request,
        string $customerId,
        CardService $cardService,
    ) {
        try {
            return $this->success($cardService->create($customerId, $request->validated()), 201);
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }

    public function destroyCard(
        string $customerId,
        string $cardId,
        CardService $cardService,
    ) {
        try {
            return $this->success($cardService->delete($customerId, $cardId));
        } catch (Throwable $throwable) {
            return $this->failure($throwable);
        }
    }
}
