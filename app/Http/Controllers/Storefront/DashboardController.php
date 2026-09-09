<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Types\OrderStatus;
use Illuminate\Http\Request;

/**
 * Storefront customer dashboard. Variable names here are dictated by the
 * existing resources/views/reseller/dashboard.blade.php, which expects
 * $balance / $totalOrders / $totalSpent / $pendingOrders / $processingOrders
 * / $completedOrders / $recentOrders — NOT the reseller-business-stats
 * shape Reseller\DashboardController passes (that controller currently
 * mismatches this same view too; separate issue, not fixed here).
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $reseller = $request->attributes->get('storefront_reseller');
        $user = $request->user();

        $customerOrders = $user->orders()->where('reseller_id', $reseller->id);

        return view('reseller.dashboard', [
            'balance' => $user->balance,
            'totalOrders' => (clone $customerOrders)->count(),
            'totalSpent' => (clone $customerOrders)->sum('charge'),
            'pendingOrders' => (clone $customerOrders)->where('status', OrderStatus::PENDING)->count(),
            'processingOrders' => (clone $customerOrders)->where('status', OrderStatus::PROCESSING)->count(),
            'completedOrders' => (clone $customerOrders)->where('status', OrderStatus::COMPLETED)->count(),
            'recentOrders' => (clone $customerOrders)->latest()->limit(10)->get(),
            'orderCreateUrl' => route('storefront.orders.create'), 
        ]);
    }
}