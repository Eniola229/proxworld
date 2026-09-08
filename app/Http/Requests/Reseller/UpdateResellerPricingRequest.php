<?php

namespace App\Http\Requests\Reseller;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateResellerPricingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->reseller && $this->user()->reseller->isActive();
    }

    public function rules(): array
    {
        return [
            'default_markup_percent' => ['required', 'numeric', 'min:0', 'max:1000'],
            // markups[provider_id_externalServiceId] => percent|null, is_hidden[...] => bool
            'markups' => ['sometimes', 'array'],
            'markups.*' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'hidden' => ['sometimes', 'array'],
        ];
    }

    /** A reseller can never set their own default/plan markup below the platform's configured floor. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $config = Setting::get('pricing_config', ['minimum_markup' => 0, 'maximum_markup' => 1000]);
            $floor = (float) $config['minimum_markup'];
            $ceiling = (float) $config['maximum_markup'];

            if ($this->filled('default_markup_percent')) {
                $value = (float) $this->input('default_markup_percent');
                if ($value < $floor || $value > $ceiling) {
                    $validator->errors()->add(
                        'default_markup_percent',
                        "Markup must be between {$floor}% and {$ceiling}%."
                    );
                }
            }

            foreach ((array) $this->input('markups', []) as $key => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                if ((float) $value < $floor || (float) $value > $ceiling) {
                    $validator->errors()->add("markups.{$key}", "Markup must be between {$floor}% and {$ceiling}%.");
                }
            }
        });
    }
}
