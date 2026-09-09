<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        return view('admin.exchange-rates.index', [
            // Named $providers to match the existing view, but these are Currency rows.
            'providers' => Currency::orderBy('currency')->get(),
            'rates' => ExchangeRate::where('from_currency', 'NGN')->get()->keyBy('to_currency'),
        ]);
    }

    /** Refresh a single currency's rate from the live provider. */
    public function refresh(Request $request, ExchangeRateService $service)
    {
        $data = $request->validate([
            'from' => ['required', 'string', 'size:3'],
            'to' => ['required', 'string', 'size:3'],
        ]);

        $service->syncAll(); // API returns the full rate table in one call; cheap to just resync all

        return back()->with('success', "Rate for {$data['from']} → {$data['to']} refreshed.");
    }

    public function refreshAll(ExchangeRateService $service)
    {
        $service->syncAll();

        return back()->with('success', 'All exchange rates refreshed.');
    }
}
