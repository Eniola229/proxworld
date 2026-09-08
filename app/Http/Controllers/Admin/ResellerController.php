<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use App\Models\User;
use App\Types\ResellerStatus;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    public function index(Request $request)
    {
        $resellers = Reseller::query()
            ->with('owner:id,name,email')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), fn ($q) => $q->where('panel_name', 'like', "%{$request->search}%")
                ->orWhere('subdomain', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.resellers.index', ['resellers' => $resellers]);
    }

    public function show(Reseller $reseller)
    {
        return view('admin.resellers.show', ['reseller' => $reseller->load('owner')]);
    }

    public function wallet(Reseller $reseller)
    {
        return view('admin.resellers.wallet', [
            'reseller' => $reseller,
            'transactions' => $reseller->wallet()->latest()->paginate(20),
        ]);
    }

    public function customers(Reseller $reseller)
    {
        $customers = User::whereHas('orders', fn ($q) => $q->where('reseller_id', $reseller->id))
            ->withCount(['orders' => fn ($q) => $q->where('reseller_id', $reseller->id)])
            ->paginate(20);

        return view('admin.resellers.customers', ['reseller' => $reseller, 'customers' => $customers]);
    }

    public function orders(Reseller $reseller)
    {
        return view('admin.resellers.orders', [
            'reseller' => $reseller,
            'orders' => $reseller->orders()->latest()->paginate(20),
        ]);
    }

    public function withdrawals(Reseller $reseller)
    {
        return view('admin.resellers.withdrawals', [
            'reseller' => $reseller,
            'withdrawals' => $reseller->withdrawals()->latest()->paginate(20),
        ]);
    }

    public function create()
    {
        // Admin-initiated reseller creation (rare — most come through self-service applications).
        return view('admin.resellers.create', ['users' => User::whereDoesntHave('reseller')->orderBy('name')->limit(200)->get()]);
    }

    public function approve(Reseller $reseller)
    {
        $reseller->update(['status' => ResellerStatus::APPROVED, 'approved_at' => now()]);
        $reseller->owner->update(['reseller_status' => ResellerStatus::APPROVED]);

        return back()->with('success', "{$reseller->panel_name} has been approved.");
    }

    public function reject(Request $request, Reseller $reseller)
    {
        $reseller->update(['status' => ResellerStatus::REJECTED, 'rejection_reason' => $request->input('reason')]);
        $reseller->owner->update(['reseller_status' => ResellerStatus::REJECTED]);

        return back()->with('success', 'Reseller application rejected.');
    }

    public function toggleStatus(Request $request, Reseller $reseller)
    {
        $reseller->update([
            'is_suspended' => ! $reseller->is_suspended,
            'rejection_reason' => $request->input('reason'),
        ]);

        return back()->with('success', $reseller->is_suspended ? 'Reseller suspended.' : 'Reseller reactivated.');
    }
}
