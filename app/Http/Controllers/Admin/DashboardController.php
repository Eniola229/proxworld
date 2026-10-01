<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProfitTransaction;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Types\OrderStatus;
use App\Types\TicketStatus;
use App\Types\WalletTransactionDirection;
use Illuminate\Http\Request;
use App\Services\ExchangeRateService;

class DashboardController extends Controller
{
    public function index(Request $request)
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

            // Revenue (based on order `charge`, matching how the reseller
            // dashboard treats "revenue" — total collected from customers)
            'revenueInPeriod' => $rates->sumConverted(Order::where('created_at', '>=', $from), 'charge', 'NGN'),
            'revenueToday'    => $rates->sumConverted(Order::whereDate('created_at', today()), 'charge', 'NGN'),
            'revenueWeek'     => $rates->sumConverted(Order::where('created_at', '>=', now()->startOfWeek()), 'charge', 'NGN'),
            'revenueMonth'    => $rates->sumConverted(Order::where('created_at', '>=', now()->startOfMonth()), 'charge', 'NGN'),


            // Support tickets
            'totalTickets' => Ticket::count(),
            'openTickets' => Ticket::whereIn('status', [TicketStatus::OPEN, TicketStatus::IN_PROGRESS])->count(),
            'closedTickets' => Ticket::where('status', TicketStatus::CLOSED)->count(),

            // Wallet / deposits — "deposit" = a credit-direction wallet transaction.
            // NOTE: 'status' values ('pending'/'success'/'failed') are still a
            // guess — send me the WalletTransaction status Types class (if one
            // exists) and I'll swap these to the real constants.
            'totalDeposits' => WalletTransaction::where('type', WalletTransactionDirection::CREDIT)->count(),
            'depositsInPeriod' => WalletTransaction::where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', $from)->count(),
            'depositAmountToday' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->whereDate('created_at', today()), 'amount', 'NGN'),
            'depositAmountWeek'  => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', now()->startOfWeek()), 'amount', 'NGN'),
            'depositAmountMonth' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('created_at', '>=', now()->startOfMonth()), 'amount', 'NGN'),
            'pendingDepositAmount' => $rates->sumConverted(WalletTransaction::excludingSwitches()->where('type', WalletTransactionDirection::CREDIT)->where('status', 'pending'), 'amount', 'NGN'),


            // Recent activity tables
            'recentCustomers' => User::latest()->take(5)->get(),
            'recentOrders' => Order::with('user')->latest()->take(10)->get()->each->withNgnCharge(),
            'recentTransactions' => WalletTransaction::excludingSwitches()->with('user')->latest()->take(10)->get()->each->withNgnAmounts(),
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