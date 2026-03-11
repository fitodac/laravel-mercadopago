<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

final class CreateCustomerRequest extends MercadoPagoFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'phone' => ['sometimes', 'array'],
            'identification' => ['sometimes', 'array'],
            'default_address' => ['sometimes', 'array'],
            'metadata' => ['sometimes', 'array'],
        ];
    }
}
