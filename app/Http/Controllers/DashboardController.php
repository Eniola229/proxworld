<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use App\Services\ExchangeRateService;
use App\Types\OrderStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, CurrencyService $currencies, ExchangeRateService $rates)
    {
        $user = $request->user();
        $currency = $currencies->walletCurrency($user);

        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->where('status', OrderStatus::PENDING)->count();
        $processingOrders = $user->orders()->where('status', OrderStatus::PROCESSING)->count();
        $completedOrders = $user->orders()->where('status', OrderStatus::COMPLETED)->count();

        // Orders may have been placed in an earlier currency, so each bucket is converted to the current one.
        $totalSpent = $rates->sumConverted($user->orders(), 'charge', $currency);

        return view('dashboard', [
            'balance' => $user->balance,
            'currency' => $currency,
            'recentOrders' => $user->orders()->latest()->limit(5)->get(),
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'completedOrders' => $completedOrders,
            'totalSpent' => $totalSpent,
            'openTickets' => $user->tickets()->whereIn('status', ['open', 'in_progress'])->count(),
        ]);
    }
}