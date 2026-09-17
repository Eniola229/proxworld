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

        $resellerOrders = $reseller->orders();

        return view('reseller.dashboard', [
            'balance' => $reseller->balance,
            'totalOrders' => (clone $resellerOrders)->count(),
            'totalSpent' => (clone $resellerOrders)->sum('charge'),
            'pendingOrders' => (clone $resellerOrders)->where('status', OrderStatus::PENDING)->count(),
            'processingOrders' => (clone $resellerOrders)->where('status', OrderStatus::PROCESSING)->count(),
            'completedOrders' => (clone $resellerOrders)->where('status', OrderStatus::COMPLETED)->count(),
            'recentOrders' => (clone $resellerOrders)->latest()->limit(10)->get(),
            'orderCreateUrl' => route('reseller.order.create'),

            // Extra reseller-specific data the shared view doesn't currently
            // render, kept available in case a future revenue widget wants it
            // without needing another controller change.
            'reseller' => $reseller,
            'availableBalance' => $reseller->profit_balance,
            'totalProfit' => $reseller->total_profit_earned,
        ]);
    }
}