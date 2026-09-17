<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->user()->reseller;

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $ordersQuery = $reseller->orders()
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo));

        $totalRevenue = (clone $ordersQuery)->sum('charge');
        $totalOrders = (clone $ordersQuery)->count();

        $totalProfit = (clone $ordersQuery)
            ->where('status', 'completed')
            ->get()
            ->sum(fn ($order) => $reseller->realProfitForOrder($order));

        $completedOrders = (clone $ordersQuery)->where('status', 'completed')->count();
        $pendingOrders = (clone $ordersQuery)->where('status', 'pending')->count();
        $processingOrders = (clone $ordersQuery)->where('status', 'processing')->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();

        // Lifetime figures — never date-filtered.
        $totalWithdrawn = $reseller->withdrawals()
            ->whereIn('status', ['pending', 'processing', 'successful'])
            ->sum('amount');

        $availableBalance = $reseller->profit_balance;

        $recentOrders = (clone $ordersQuery)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('reseller.manage.revenue', [
            'reseller' => $reseller,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalRevenue' => $totalRevenue,
            'totalProfit' => $totalProfit,
            'totalOrders' => $totalOrders,
            'totalWithdrawn' => $totalWithdrawn,
            'availableBalance' => $availableBalance,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'cancelledOrders' => $cancelledOrders,
            'recentOrders' => $recentOrders,
        ]);
    }
}