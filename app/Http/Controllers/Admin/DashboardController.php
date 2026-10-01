<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProfitTransaction;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\ExchangeRateService;
use App\Types\OrderStatus;
use App\Types\TicketStatus;
use App\Types\WalletTransactionDirection;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, ExchangeRateService $rates)
    {
        $period = $request->get('period', 'today');
        $from = $this->periodStart($period);
        $admin = $request->user('admin');

        $data = [
            'period' => $period,

            // Customers
            'totalCustomers' => User::count(),
            'newCustomers' => User::where('created_at', '>=', $from)->count(),
            'customersToday' => User::whereDate('created_at', today())->count(),
            'customersWeek' => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'customersMonth' => User::where('created_at', '>=', now()->startOfMonth())->count(),

            // Orders
            'totalOrders' => Order::count(),
            'ordersInPeriod' => Order::where('created_at', '>=', $from)->count(),
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'ordersWeek' => Order::where('created_at', '>=', now()->startOfWeek())->count(),
            'ordersMonth' => Order::where('created_at', '>=', now()->startOfMonth())->count(),
            'completedOrders' => Order::where('status', OrderStatus::COMPLETED)->count(),
            'processingOrders' => Order::where('status', OrderStatus::PROCESSING)->count(),
            'pendingOrders' => Order::where('status', OrderStatus::PENDING)->count(),
            'cancelledOrders' => Order::where('status', OrderStatus::CANCELLED)->count(),

            // Revenue (order `charge`, every currency converted to NGN)
            'revenueInPeriod' => $rates->sumConverted(Order::where('created_at', '>=', $from), 'charge', 'NGN'),
            'revenueToday' => $rates->sumConverted(Order::whereDate('created_at', today()), 'charge', 'NGN'),
            'revenueWeek' => $rates->sumConverted(Order::where('created_at', '>=', now()->startOfWeek()), 'charge', 'NGN'),
            'revenueMonth' => $rates->sumConverted(Order::where('created_at', '>=', now()->startOfMonth()), 'charge', 'NGN'),

            // Support tickets
            'totalTickets' => Ticket::count(),
            'openTickets' => Ticket::whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])->count(),
            'closedTickets' => Ticket::where('status', TicketStatus::CLOSED)->count(),

            // Wallet / deposits — "deposit" = a credit-direction wallet transaction.
            // excludingSwitches() keeps "currency switched" rows out of the totals.
            'totalDeposits' => WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->count(),
            'depositsInPeriod' => WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', $from)->count(),
            'depositsToday' => WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->whereDate('created_at', today())->count(),
            'depositAmountToday' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->whereDate('created_at', today()), 'amount', 'NGN'),
            'depositsWeek' => WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', now()->startOfWeek())->count(),
            'depositAmountWeek' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', now()->startOfWeek()), 'amount', 'NGN'),
            'depositsMonth' => WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', now()->startOfMonth())->count(),
            'depositAmountMonth' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', now()->startOfMonth()), 'amount', 'NGN'),
            'pendingDeposits' => WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('status', 'pending')->count(),
            'pendingDepositAmount' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('status', 'pending'), 'amount', 'NGN'),

            // Recent activity tables (shown in NGN)
            'recentCustomers' => User::latest()->take(5)->get(),
            'recentOrders' => Order::with('user')->latest()->take(10)->get(),
            'recentTransactions' => WalletTransaction::excludingSwitches()->with('user')->latest()->take(10)->get(),
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