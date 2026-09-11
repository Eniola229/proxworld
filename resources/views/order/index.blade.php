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

                        {{-- Filter Bar --}}
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

                        {{-- Orders Card Grid --}}
                        <div class="card-body p-3">
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

                            <div class="row g-3">
                                @forelse($orders as $order)
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card h-100 border shadow-none mb-0">
                                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                                <div>
                                                    <!-- Top Bar: Order ID & Status Badge -->
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fw-bold text-dark fs-13">#{{ substr($order->id, 0, 8) }}...</span>
                                                        <span class="badge {{ $badgeClass[$order->status] ?? 'bg-soft-secondary text-secondary' }}">
                                                            <i class="{{ $badgeIcon[$order->status] ?? 'feather-help-circle' }} me-1"></i>
                                                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                                        </span>
                                                    </div>

                                                    <!-- Service Name & Product Type -->
                                                    <h6 class="fw-bold mb-1 text-truncate" title="{{ $order->service_name }}">
                                                        {{ $order->service_name }}
                                                    </h6>
                                                    <div class="mb-3">
                                                        <span class="badge bg-soft-secondary text-secondary text-uppercase fs-11">
                                                            {{ $order->product_type ?? '—' }}
                                                        </span>
                                                    </div>

                                                    <!-- Details Grid -->
                                                    <div class="bg-light p-2 rounded mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-1 fs-12">
                                                            <span class="text-muted"><i class="feather-layers me-1"></i>Quantity:</span>
                                                            <span class="fw-semibold text-dark">{{ number_format($order->quantity) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-1 fs-12">
                                                            <span class="text-muted"><i class="feather-credit-card me-1"></i>Charge:</span>
                                                            <span class="fw-bold text-primary">₦{{ number_format($order->charge, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center fs-12">
                                                            <span class="text-muted"><i class="feather-calendar me-1"></i>Date:</span>
                                                            <span class="text-muted">{{ $order->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="d-flex gap-2 pt-2 border-top">
                                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-light flex-grow-1 text-center">
                                                        View Details <i class="feather-eye ms-1"></i>
                                                    </a>
                                                    @if($order->api_order_id && in_array($order->status, [\App\Types\OrderStatus::PENDING, \App\Types\OrderStatus::PROCESSING]))
                                                        <a href="{{ route('orders.check-status', $order->id) }}"
                                                           class="btn btn-sm btn-light"
                                                           title="Check Status">
                                                            <i class="feather-refresh-cw"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
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
                                        </div>
                                    </div>
                                @endforelse
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

<!-- Accpond Partner Modal -->
<div class="modal fade" id="accpondModal" tabindex="-1" aria-labelledby="accpondModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-3">
                    <span class="badge bg-soft-primary text-primary text-uppercase fs-11 fw-bold px-3 py-1">Partner Marketplace</span>
                </div>
                <h4 class="mb-2 fw-bold">Buy & Sell Verified Social Accounts</h4>
                <p class="text-muted mb-4 fs-13">
                    Need an established social media account or asset? Visit our trusted partner <strong>accpond</strong> for fast and secure account trading.
                </p>
                <div class="d-grid gap-2">
                    <a href="https://www.accpond.com.ng" target="_blank" class="btn btn-primary fw-bold">
                        <i class="feather-external-link me-2"></i> Visit accpond.com.ng
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Maybe Later
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.g-footer')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Triggers the Accpond pop-up modal on page load (1-second delay)
        setTimeout(function() {
            const modalElement = document.getElementById('accpondModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        }, 1000);
    });
</script>