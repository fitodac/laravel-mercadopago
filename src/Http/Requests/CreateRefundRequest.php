<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

final class CreateRefundRequest extends MercadoPagoFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
        ];
    }
}
