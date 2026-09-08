<?php

namespace App\Http\Controllers;

use App\Types\OrderStatus;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
            'balance' => $user->balance,
            'recentOrders' => $user->orders()->latest()->limit(5)->get(),
            'ordersCount' => $user->orders()->count(),
            'completedOrders' => $user->orders()->where('status', OrderStatus::COMPLETED)->count(),
            'openTickets' => $user->tickets()->whereIn('status', ['open', 'in_progress'])->count(),
        ]);
    }
}
