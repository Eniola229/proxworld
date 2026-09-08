<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProfitTransaction;
use App\Models\User;
use App\Types\OrderStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        $from = $this->periodStart($period);
        $admin = $request->user('admin');

        $data = [
            'period' => $period,
            'totalCustomers' => User::count(),
            'newCustomers' => User::where('created_at', '>=', $from)->count(),
            'customersToday' => User::whereDate('created_at', today())->count(),
            'customersWeek' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'customersMonth' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'totalOrders' => Order::count(),
            'ordersInPeriod' => Order::where('created_at', '>=', $from)->count(),
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'ordersWeek' => Order::where('created_at', '>=', now()->startOfWeek())->count(),
            'ordersMonth' => Order::where('created_at', '>=', now()->startOfMonth())->count(),
            'completedOrders' => Order::where('status', OrderStatus::COMPLETED)->count(),
            'processingOrders' => Order::where('status', OrderStatus::PROCESSING)->count(),
            'pendingOrders' => Order::where('status', OrderStatus::PENDING)->count(),
            'cancelledOrders' => Order::where('status', OrderStatus::CANCELLED)->count(),
        ];

        // Profit numbers only computed/passed if the admin actually has
        // permission to see them — never leak the figure into the payload
        // for someone the view would otherwise just hide it from.
        if ($admin->can('profit.view')) {
            $data['totalProfit'] = ProfitTransaction::where('created_at', '>=', $from)->sum('amount');
            $data['allTimeProfit'] = ProfitTransaction::sum('amount');
        }

        return view('admin.dashboard', $data);
    }

    protected function periodStart(string $period)
    {
        return match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfDay(),
        };
    }
}
