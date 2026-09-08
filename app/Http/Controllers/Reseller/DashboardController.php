<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Types\OrderStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        return view('reseller.dashboard', [
            'reseller' => $reseller,
            'ordersCount' => $reseller->orders()->count(),
            'completedOrders' => $reseller->orders()->where('status', OrderStatus::COMPLETED)->count(),
            'recentOrders' => $reseller->orders()->latest()->limit(5)->get(),
            'availableBalance' => $reseller->profit_balance,
            'totalProfit' => $reseller->total_profit_earned,
        ]);
    }
}
