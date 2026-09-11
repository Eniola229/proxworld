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
                
                <!-- [ Partner Section: accpond Banner ] Start -->
                <div class="col-12 mb-4">
                    <div class="card border-0 bg-primary text-white overflow-hidden position-relative">
                        <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-white text-primary text-uppercase fs-11 fw-bold">Partner Marketplace</span>
                                    <span class="badge bg-white bg-opacity-20 text-white fs-11">Verified Accounts</span>
                                </div>
                                <h4 class="text-white fw-bold mb-1">Buy & Sell Verified Social Accounts on accpond</h4>
                                <p class="fs-13 text-white-50 mb-0">
                                    Need an established social media account or asset? Visit our trusted partner <strong>accpond</strong> for fast, secure account trading.
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="https://www.accpond.com.ng" target="_blank" class="btn btn-light fw-bold text-primary px-4 d-inline-flex align-items-center gap-2">
                                    Visit accpond.com.ng <i class="feather-external-link fs-12"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Partner Section: accpond Banner ] End -->

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

@include('components.g-footer')