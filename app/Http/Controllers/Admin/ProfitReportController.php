<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfitTransaction;
use App\Models\Provider;
use Illuminate\Http\Request;

/** Reachable only via permission:profit.view (Super Admin + Finance by default). */
class ProfitReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ProfitTransaction::query();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('provider_id')) {
            $query->where('provider_id', $request->provider_id);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        return view('admin.profit.index', [
            'totalProfit' => (clone $query)->sum('amount'),
            'transactions' => $query->latest()->paginate(25)->withQueryString(),
            'providers' => Provider::orderBy('name')->get(),
            'byChannel' => (clone $query)->selectRaw('channel, sum(amount) as total')->groupBy('channel')->get(),
            'byProvider' => (clone $query)->selectRaw('provider_id, sum(amount) as total')->groupBy('provider_id')->with('provider:id,name')->get(),
        ]);
    }
}
