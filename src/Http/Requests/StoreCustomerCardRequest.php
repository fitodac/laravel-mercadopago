<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

final class StoreCustomerCardRequest extends MercadoPagoFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }
}
