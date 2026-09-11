<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncExchangeRatesJob;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        return view('admin.exchange-rates.index', [
            'providers' => Currency::orderBy('currency')->get(),
            'rates' => ExchangeRate::where('from_currency', 'NGN')
                ->orWhere('to_currency', 'NGN')
                ->get(),
        ]);
    }

    /** Queues a refresh. $from/$to are kept for the UI's benefit — the job itself resyncs the full table in one call. */
    public function refresh(Request $request)
    {
        $data = $request->validate([
            'from' => ['required', 'string', 'size:3'],
            'to' => ['required', 'string', 'size:3'],
        ]);

        SyncExchangeRatesJob::dispatch();

        return back()->with('success', "Refreshing rate for {$data['from']} → {$data['to']}… this may take a moment.");
    }

    public function refreshAll()
    {
        SyncExchangeRatesJob::dispatch();

        return back()->with('success', 'Exchange rate refresh queued — rates will update shortly.');
    }
}