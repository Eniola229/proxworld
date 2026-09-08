<?php

namespace App\Http\Controllers;

use App\Types\OrderStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->where('status', OrderStatus::PENDING)->count();
        $processingOrders = $user->orders()->where('status', OrderStatus::PROCESSING)->count();
        $completedOrders = $user->orders()->where('status', OrderStatus::COMPLETED)->count();

        // Assumption: "Total Spent" = sum of charge across every order the
        // user has placed, regardless of status. Adjust the where() below
        // if you only want completed (paid/delivered) orders counted.
        $totalSpent = $user->orders()->sum('charge');

        return view('dashboard', [
            'balance' => $user->balance,
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