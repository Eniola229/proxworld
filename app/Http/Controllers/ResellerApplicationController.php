<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Models\Setting;
use App\Types\ResellerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResellerApplicationController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        return view('reseller-panel.index', [
            'reseller' => $reseller,
            'ordersCount' => $reseller?->orders()->count() ?? 0,
            'availableBalance' => $reseller?->profit_balance ?? 0,
            'totalProfit' => $reseller?->total_profit_earned ?? 0,
        ]);
    }

    public function create(Request $request)
    {
        abort_if($request->user()->reseller, 302, 'You already have a reseller panel.');

        return view('reseller-panel.create');
    }

    public function store(Request $request)
    {
        abort_if($request->user()->reseller, 422, 'You already have a reseller panel.');

        $data = $request->validate([
            'panel_name' => ['required', 'string', 'max:100'],
            'subdomain' => ['required', 'string', 'max:63', 'alpha_dash', 'unique:resellers,subdomain'],
        ]);

        Reseller::create([
            'owner_id' => $request->user()->id,
            'panel_name' => $data['panel_name'],
            'subdomain' => strtolower($data['subdomain']),
            'default_markup_percent' => Setting::get('pricing_config')['default_markup'] ?? 30,
            'status' => ResellerStatus::PENDING,
        ]);

        $request->user()->forceFill([
            'is_reseller' => true,
            'reseller_status' => ResellerStatus::PENDING,
        ])->save();

        return redirect()->route('reseller-panel.index')->with('success', 'Your reseller application has been submitted for review.');
    }

    public function update(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller, 404);

        $data = $request->validate([
            'panel_name' => ['required', 'string', 'max:100'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'support_email' => ['nullable', 'email'],
            'support_telegram' => ['nullable', 'string', 'max:255'],
            'support_whatsapp' => ['nullable', 'string', 'max:255'],
        ]);

        $reseller->update($data);

        return back()->with('success', 'Panel settings updated.');
    }

    public function updateDomain(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller, 404);

        $data = $request->validate(['custom_domain' => ['nullable', 'string', 'max:255', 'unique:resellers,custom_domain,'.$reseller->id]]);

        $reseller->update(['custom_domain' => $data['custom_domain'] ?? null]);

        return back()->with('success', 'Custom domain saved. Point its DNS at our server IP, then verify below.');
    }

    /** Basic DNS verification — confirms the domain's A/CNAME record actually points at us before activating it. */
    public function verifyDomain(Request $request)
    {
        $reseller = $request->user()->reseller;
        abort_unless($reseller && $reseller->custom_domain, 404);

        $records = @dns_get_record($reseller->custom_domain, DNS_A + DNS_CNAME) ?: [];
        $expectedIp = Setting::get('server_ip');
        $verified = collect($records)->contains(fn ($r) => ($r['ip'] ?? null) === $expectedIp || str_contains($r['target'] ?? '', parse_url(config('app.url'), PHP_URL_HOST)));

        if (! $verified) {
            return back()->with('error', 'DNS not verified yet. Changes can take a few hours to propagate — try again shortly.');
        }

        $reseller->update(['custom_domain_status' => 'verified']);

        return back()->with('success', 'Domain verified! Your storefront is now live on your custom domain.');
    }
}
