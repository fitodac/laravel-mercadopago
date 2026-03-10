<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreatePreferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'payer' => ['sometimes', 'array'],
            'back_urls' => ['sometimes', 'array'],
            'metadata' => ['sometimes', 'array'],
            'notification_url' => ['sometimes', 'url'],
            'external_reference' => ['sometimes', 'string'],
            'expires' => ['sometimes', 'boolean'],
            'expiration_date_from' => ['sometimes', 'date'],
            'expiration_date_to' => ['sometimes', 'date'],
        ];
    }
}
