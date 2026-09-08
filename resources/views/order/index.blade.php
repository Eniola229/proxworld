@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Order History</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Orders</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('order.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> New Order
                </a>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h5 class="card-title mb-0">Your Orders</h5>
                                <button onclick="window.location.reload()" class="btn btn-sm btn-light">
                                    <i class="feather-refresh-cw me-1"></i> Refresh Status
                                </button>
                            </div>
                        </div>

                        {{-- Filter Bar — built strictly from App\Types\OrderStatus, nothing invented --}}
                        <div class="card-body border-bottom p-3">
                            <form method="GET" action="{{ route('orders.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                                @php
                                    $statusMeta = [
                                        \App\Types\OrderStatus::PENDING    => ['icon' => 'feather-clock',        'active' => 'btn-primary'],
                                        \App\Types\OrderStatus::PROCESSING => ['icon' => 'feather-loader',       'active' => 'btn-warning'],
                                        \App\Types\OrderStatus::COMPLETED  => ['icon' => 'feather-check-circle', 'active' => 'btn-success'],
                                        \App\Types\OrderStatus::CANCELLED  => ['icon' => 'feather-x-circle',     'active' => 'btn-danger'],
                                        \App\Types\OrderStatus::REFUNDED   => ['icon' => 'feather-rotate-ccw',   'active' => 'btn-info'],
                                    ];
                                    $current = request('status', '');
                                @endphp

                                <button type="submit" name="status" value=""
                                    class="btn btn-sm {{ $current === '' ? 'btn-secondary' : 'btn-light' }}">
                                    <i class="feather-list me-1"></i> All Orders
                                </button>

                                @foreach(\App\Types\OrderStatus::labels() as $value => $label)
                                    <button type="submit" name="status" value="{{ $value }}"
                                        class="btn btn-sm {{ $current === $value ? $statusMeta[$value]['active'] : 'btn-light' }}">
                                        <i class="{{ $statusMeta[$value]['icon'] }} me-1"></i>
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </form>
                        </div>

                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Service</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Charge</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $badgeClass = [
                                                \App\Types\OrderStatus::PENDING    => 'bg-soft-primary text-primary',
                                                \App\Types\OrderStatus::PROCESSING => 'bg-soft-warning text-warning',
                                                \App\Types\OrderStatus::COMPLETED  => 'bg-soft-success text-success',
                                                \App\Types\OrderStatus::CANCELLED  => 'bg-soft-danger text-danger',
                                                \App\Types\OrderStatus::REFUNDED   => 'bg-soft-info text-info',
                                            ];
                                            $badgeIcon = [
                                                \App\Types\OrderStatus::PENDING    => 'feather-clock',
                                                \App\Types\OrderStatus::PROCESSING => 'feather-loader',
                                                \App\Types\OrderStatus::COMPLETED  => 'feather-check-circle',
                                                \App\Types\OrderStatus::CANCELLED  => 'feather-x-circle',
                                                \App\Types\OrderStatus::REFUNDED   => 'feather-rotate-ccw',
                                            ];
                                            $statusLabels = \App\Types\OrderStatus::labels();
                                        @endphp

                                        @forelse($orders as $order)
                                            <tr>
                                                <td>#{{ substr($order->id, 0, 8) }}...</td>
                                                <td>{{ $order->service_name }}</td>
                                                <td>
                                                    <span class="badge bg-soft-secondary text-secondary text-uppercase">{{ $order->product_type ?? '—' }}</span>
                                                </td>
                                                <td>{{ number_format($order->quantity) }}</td>
                                                <td>₦{{ number_format($order->charge, 2) }}</td>
                                                <td>
                                                    <span class="badge {{ $badgeClass[$order->status] ?? 'bg-soft-secondary text-secondary' }}">
                                                        <i class="{{ $badgeIcon[$order->status] ?? 'feather-help-circle' }} me-1"></i>
                                                        {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $order->created_at->diffForHumans() }}</td>
                                                <td class="d-flex gap-2">
                                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light" title="View Details">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    @if($order->api_order_id && in_array($order->status, [\App\Types\OrderStatus::PENDING, \App\Types\OrderStatus::PROCESSING]))
                                                        <a href="{{ route('orders.check-status', $order->id) }}"
                                                           class="btn btn-sm btn-light"
                                                           title="Check Status">
                                                            <i class="feather-refresh-cw"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="text-muted mb-3">
                                                        @if($current)
                                                            No {{ $statusLabels[$current] ?? ucfirst($current) }} orders found.
                                                        @else
                                                            No orders found.
                                                        @endif
                                                    </div>
                                                    @if($current)
                                                        <a href="{{ route('orders.index') }}" class="btn btn-light btn-sm me-2">View All Orders</a>
                                                    @endif
                                                    <a href="{{ route('order.create') }}" class="btn btn-primary btn-sm">Place Your First Order</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')