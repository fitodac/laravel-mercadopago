<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateCustomerRequest extends FormRequest
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
