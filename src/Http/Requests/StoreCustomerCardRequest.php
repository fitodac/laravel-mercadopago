<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreCustomerCardRequest extends FormRequest
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
