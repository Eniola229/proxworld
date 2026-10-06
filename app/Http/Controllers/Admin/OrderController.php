<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\ProxyProviders\ProxyProviderFactory;
use App\Services\ExchangeRateService;
use App\Services\ResellerWalletService;
use App\Services\WalletService;
use App\Types\OrderStatus;
use App\Types\TransactionType;
use Illuminate\Http\Request;
use App\Models\OrderExtension;
use App\Models\Provider;


class OrderController extends Controller
{

    public function index(Request $request)
    {
        $search     = $request->query('search');
        $status     = $request->query('status');
        $providerId = $request->query('provider_id');
        $from       = $request->query('date_from');
        $to         = $request->query('date_to');

        // Orders table (all filters)
        $orders = Order::with(['user', 'provider'])
            ->withCount(['extensions as extensions_count' => fn ($q) => $q->where('status', 'completed')])
            ->withSum(['extensions as extensions_profit' => fn ($q) => $q->where('status', 'completed')], 'profit')
            ->withSum(['extensions as extensions_revenue' => fn ($q) => $q->where('status', 'completed')], 'platform_price_snapshot')
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('service_name', 'like', "%{$search}%")
                  ->orWhere('api_order_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            }))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($providerId, fn ($q) => $q->where('provider_id', $providerId))
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Top stat cards (global counts)
        $totalOrders      = Order::count();
        $pendingOrders    = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders  = Order::where('status', 'completed')->count();
        $cancelledOrders  = Order::whereIn('status', ['cancelled', 'refunded'])->count();

        // Profit / revenue period: explicit range if given, otherwise the current month
        $applyPeriod = function ($q) use ($from, $to) {
            if ($from) {
                $q->whereDate('created_at', '>=', $from);
            } elseif (! $to) {
                $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }
            if ($to) {
                $q->whereDate('created_at', '<=', $to);
            }
        };

        $profitOrders = Order::where('status', 'completed');
        $applyPeriod($profitOrders);
        if ($providerId) $profitOrders->where('provider_id', $providerId);
        if ($search) {
            $profitOrders->where(fn ($q) => $q->where('id', 'like', "%{$search}%")->orWhere('service_name', 'like', "%{$search}%"));
        }
        $periodOrderCount   = (clone $profitOrders)->count();
        $periodOrderProfit  = (float) (clone $profitOrders)->sum('profit');
        $periodOrderRevenue = (float) (clone $profitOrders)->sum('platform_price_snapshot');

        // Extensions count in the period they were bought
        $ext = OrderExtension::completed();
        $applyPeriod($ext);
        if ($providerId) $ext->where('provider_id', $providerId);
        if ($search) {
            $ext->whereHas('order', fn ($q) => $q->where('id', 'like', "%{$search}%")->orWhere('service_name', 'like', "%{$search}%"));
        }
        $periodExtCount   = (clone $ext)->count();
        $periodExtProfit  = (float) (clone $ext)->sum('profit');
        $periodExtRevenue = (float) (clone $ext)->sum('platform_price_snapshot');

        return view('admin.orders.index', [
            'orders'           => $orders,
            'providers'        => Provider::orderBy('name')->get(['id', 'name']),
            'totalOrders'      => $totalOrders,
            'pendingOrders'    => $pendingOrders,
            'processingOrders' => $processingOrders,
            'completedOrders'  => $completedOrders,
            'cancelledOrders'  => $cancelledOrders,
            'periodOrderCount' => $periodOrderCount,
            'periodExtCount'   => $periodExtCount,
            'periodExtProfit'  => $periodExtProfit,
            'totalProfit'      => $periodOrderProfit + $periodExtProfit,
            'totalRevenue'     => $periodOrderRevenue + $periodExtRevenue,
        ]);
    }
    
    public function show(Order $order)
    {
        $order->load([
            'user',
            'reseller',
            'provider',
            'extensions' => fn ($q) => $q->latest(),
        ]);

        $customerBalance = $order->user->balanceInNgn(); // NGN equivalent, shown beside the customer's real balance

        return view('admin.orders.show', [
            'order' => $order,
            'customerBalance' => $customerBalance,
            'logs' => \App\Models\ActivityLog::where('subject_type', Order::class)
                ->where('subject_id', $order->id)
                ->latest()
                ->paginate(15),
        ]);
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
        abort_unless(in_array($order->status, [OrderStatus::CANCELLED, OrderStatus::REFUNDED]), 422, 'Only cancelled/refunded orders can be deleted.');
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }
}