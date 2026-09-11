<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePricingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'default_markup' => ['required', 'numeric', 'min:0', 'max:1000'],
            'minimum_markup' => ['required', 'numeric', 'min:0', 'max:1000'],
            'maximum_markup' => ['required', 'numeric', 'min:0', 'max:1000'],
            'currency_buffer' => ['required', 'numeric', 'min:0', 'max:100'],
            'round_prices' => ['sometimes', 'boolean'],
        ];
    }
}