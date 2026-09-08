<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        $period = $request->get('period', 'month');
        $from = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        return view('reseller.manage.revenue', [
            'reseller' => $reseller,
            'period' => $period,
            'periodOrders' => $reseller->orders()->where('created_at', '>=', $from)->count(),
            'periodProfit' => $reseller->profitTransactions()->where('created_at', '>=', $from)->sum('amount'),
            'totalProfit' => $reseller->total_profit_earned,
            'availableBalance' => $reseller->profit_balance,
            'transactions' => $reseller->profitTransactions()->latest()->paginate(15),
        ]);
    }
}
