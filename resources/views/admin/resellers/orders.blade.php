@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')
<main class="nxl-container">
    <div class="nxl-content">
        
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $reseller->panel_name }} — Orders</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.resellers.show', $reseller) }}">{{ $reseller->panel_name }}</a>
            </li>
            <li class="breadcrumb-item">Orders</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-dark">₦{{ number_format($totalCharge, 2) }}</div>
                <div class="fs-13 text-muted">Total Revenue</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-success">₦{{ number_format($totalProfit, 2) }}</div>
                <div class="fs-13 text-muted">Reseller Profit</div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Link</th>
                            <th>Qty</th>
                            <th>Charge</th>
                            <th>Profit</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->user->name }}</td>
                                <td class="text-truncate" style="max-width:140px;" title="{{ $order->service_name }}">
                                    {{ $order->service_name }}
                                </td>
                                <td>
                                    <a href="{{ $order->link }}" target="_blank" class="text-muted" style="font-size:12px;">
                                        {{ Str::limit($order->link, 25) }}
                                    </a>
                                </td>
                                <td>{{ number_format($order->quantity) }}</td>
                                <td>₦{{ number_format($order->charge, 2) }}</td>
                                <td class="text-success">₦{{ number_format($order->profit ?? 0, 2) }}</td>
                                <td>
                                    @php $badges=['completed'=>'success','processing'=>'info','pending'=>'warning','cancelled'=>'danger','partial'=>'primary']; @endphp
                                    <span class="badge bg-soft-{{ $badges[$order->status] ?? 'secondary' }} text-{{ $badges[$order->status] ?? 'secondary' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No orders on this panel yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
</div>
</main>

@include('admin.components.footer')