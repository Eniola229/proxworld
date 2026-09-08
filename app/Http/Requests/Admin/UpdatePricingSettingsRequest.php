<?php

namespace App\Http\Requests\Admin;

use App\Types\ProductType;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePricingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('pricing.manage');
    }

    public function rules(): array
    {
        return [
            'default_markup' => ['required', 'numeric', 'min:0', 'max:1000'],
            'minimum_markup' => ['required', 'numeric', 'min:0', 'max:1000', 'lte:maximum_markup'],
            'maximum_markup' => ['required', 'numeric', 'min:0', 'max:1000', 'gte:minimum_markup'],
            'currency_buffer' => ['required', 'numeric', 'min:0', 'max:100'],
            'round_prices' => ['sometimes', 'boolean'],
            'service_type_markup' => ['sometimes', 'array'],
            'service_type_markup.*' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'platform_markup' => ['sometimes', 'array'],
            'platform_markup.*' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'minimum_markup.lte' => 'Minimum markup cannot be greater than maximum markup.',
            'maximum_markup.gte' => 'Maximum markup cannot be less than minimum markup.',
        ];
    }
}
