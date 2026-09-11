<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PricingRuleController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'scope_type' => ['required', Rule::in(['product_type', 'protocol', 'combined', 'provider', 'provider_product_type'])],
            'product_type' => ['nullable', 'string', 'max:50', 'required_if:scope_type,product_type,combined,provider_product_type'],
            'protocol' => ['nullable', 'string', 'max:50', 'required_if:scope_type,protocol,combined'],
            'provider_id' => ['nullable', 'uuid', 'exists:providers,id', 'required_if:scope_type,provider,provider_product_type'],
            'markup_percentage' => ['required', 'numeric', 'min:0', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        // Only keep the fields relevant to this scope so the unique index behaves predictably.
        $data['product_type'] = in_array($data['scope_type'], ['product_type', 'combined', 'provider_product_type']) ? $data['product_type'] : null;
        $data['protocol'] = in_array($data['scope_type'], ['protocol', 'combined']) ? $data['protocol'] : null;
        $data['provider_id'] = in_array($data['scope_type'], ['provider', 'provider_product_type']) ? $data['provider_id'] : null;

        PricingRule::create(array_merge($data, ['is_active' => true]));

        return back()->with('success', 'Markup rule added.');
    }

    public function toggle(PricingRule $rule)
    {
        $rule->update(['is_active' => ! $rule->is_active]);

        return back()->with('success', $rule->is_active ? 'Rule enabled.' : 'Rule disabled.');
    }

    public function destroy(PricingRule $rule)
    {
        $rule->delete();

        return back()->with('success', 'Markup rule removed.');
    }
}