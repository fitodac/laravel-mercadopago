<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

final class CreatePaymentRequest extends MercadoPagoFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_amount' => ['required', 'numeric', 'min:0.01'],
            'token' => ['required', 'string'],
            'description' => ['required', 'string'],
            'installments' => ['required', 'integer', 'min:1'],
            'payment_method_id' => ['required', 'string'],
            'issuer_id' => ['sometimes', 'integer'],
            'payer' => ['required', 'array'],
            'payer.email' => ['required', 'email'],
            'payer.identification' => ['sometimes', 'array'],
            'metadata' => ['sometimes', 'array'],
            'external_reference' => ['sometimes', 'string'],
            'notification_url' => ['sometimes', 'url'],
        ];
    }
}
