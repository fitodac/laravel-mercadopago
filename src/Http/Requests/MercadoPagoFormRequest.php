<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class MercadoPagoFormRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'required' => __('mercadopago::mercadopago.validation.required'),
            'email' => __('mercadopago::mercadopago.validation.email'),
            'string' => __('mercadopago::mercadopago.validation.string'),
            'array' => __('mercadopago::mercadopago.validation.array'),
            'boolean' => __('mercadopago::mercadopago.validation.boolean'),
            'numeric' => __('mercadopago::mercadopago.validation.numeric'),
            'integer' => __('mercadopago::mercadopago.validation.integer'),
            'url' => __('mercadopago::mercadopago.validation.url'),
            'date' => __('mercadopago::mercadopago.validation.date'),
            'min' => __('mercadopago::mercadopago.validation.min'),
        ];
    }

    public function attributes(): array
    {
        /** @var array<string, string> $attributes */
        $attributes = trans('mercadopago::mercadopago.validation.attributes');

        return $attributes;
    }
}
