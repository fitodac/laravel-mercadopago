<?php

declare(strict_types=1);

namespace Fitodac\LaravelMercadoPago\Http\Requests;

use Illuminate\Validation\Rule;

final class CreateTestUserRequest extends MercadoPagoFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['required', 'string', Rule::in(['MLA', 'MLB', 'MLC', 'MLM', 'MLU', 'MCO', 'MPE'])],
            'description' => ['sometimes', 'string'],
        ];
    }
}
