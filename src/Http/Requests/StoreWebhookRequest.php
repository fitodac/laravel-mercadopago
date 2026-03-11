<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

final class StoreWebhookRequest extends MercadoPagoFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['sometimes', 'string'],
            'api_version' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string'],
            'data' => ['sometimes', 'array'],
            'data.id' => ['sometimes'],
            'live_mode' => ['sometimes', 'boolean'],
            'user_id' => ['sometimes'],
        ];
    }
}
