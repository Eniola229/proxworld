<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\ProxyProviders\ProxyProviderFactory;
use App\Services\ResellerWalletService;
use App\Services\WalletService;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->channel))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->filled('search'), fn ($q) => $q->where('id', 'like', "%{$request->search}%")
                ->orWhere('service_name', 'like', "%{$request->search}%"))
            ->with(['user:id,name,email', 'provider:id,name'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load(['user', 'reseller', 'provider'])]);
    }

    public function checkStatus(Order $order)
    {
        if (! $order->provider || ! $order->api_order_id) {
            return back()->with('error', 'This order has no provider reference to check.');
        }

        $driver = ProxyProviderFactory::make($order->provider);
        $status = $driver->getOrderStatus($order->api_order_id);

        $order->update(['provider_synced_at' => now()]);

        return back()->with('success', "Provider reports status: {$status}");
    }

    public function refund(Request $request, Order $order, WalletService $wallet, ResellerWalletService $resellerWallet)
    {
        abort_if($order->status === OrderStatus::REFUNDED, 422, 'Already refunded.');

        if ($order->reseller_id) {
            $resellerWallet->credit($order->reseller, (float) $order->platform_price_snapshot, [
                'description' => "Admin refund for order #{$order->id}",
            ]);
        } else {
            $wallet->credit($order->user, (float) $order->charge, TransactionType::ORDER_REFUND, [
                'description' => "Admin refund for order #{$order->id}",
                'currency' => $order->currency,
            ]);
        }

        $order->update(['status' => OrderStatus::REFUNDED, 'admin_note' => $request->input('note')]);

        return back()->with('success', 'Order refunded.');
    }

    /** Manual status override — for edge cases the automated pipeline can't resolve on its own. Refunds if moved to refunded/cancelled and not already. */
    public function updateStatus(Request $request, Order $order, WalletService $wallet, ResellerWalletService $resellerWallet)
    {
        $data = $request->validate(['status' => ['required', 'in:pending,processing,completed,cancelled,refunded']]);

        $triggersRefund = in_array($data['status'], [OrderStatus::CANCELLED, OrderStatus::REFUNDED])
            && ! in_array($order->status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED]);

        if ($triggersRefund) {
            if ($order->reseller_id) {
                $resellerWallet->credit($order->reseller, (float) $order->platform_price_snapshot, ['description' => "Order #{$order->id} status changed to {$data['status']}"]);
            } else {
                $wallet->credit($order->user, (float) $order->charge, TransactionType::ORDER_REFUND, [
                    'description' => "Order #{$order->id} status changed to {$data['status']}",
                    'currency' => $order->currency,
                ]);
            }
        }

        $order->update(['status' => $data['status']]);

        return back()->with('success', 'Order status updated.');
    }

    public function destroy(Order $order)
    {
        // Financial records are never hard-deleted for real orders in production —
        // this exists for cleaning up genuine test/duplicate rows only.
        abort_unless(in_array($order->status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED]), 422, 'Only cancelled/refunded orders can be deleted.');
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }
}
