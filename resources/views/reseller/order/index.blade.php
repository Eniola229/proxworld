@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Orders</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item">Orders</li>
                </ul>
            </div>
            <div class="page-header-right">
                <a href="/orders/new" class="btn btn-primary">New Order</a>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show" role="alert">
                    {{ session('alert')['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Order History</h5>
                    <div class="card-header-right">
                        <form method="GET" class="d-flex">
                            <select name="status" class="form-select me-2" style="width: auto;">
                                <option value="">All Status</option>
                                @foreach(\App\Types\OrderStatus::labels() as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-3">
                    @php
                        $badgeClass = [
                            \App\Types\OrderStatus::PENDING    => 'bg-warning text-dark',
                            \App\Types\OrderStatus::PROCESSING => 'bg-info text-white',
                            \App\Types\OrderStatus::COMPLETED  => 'bg-success text-white',
                            \App\Types\OrderStatus::CANCELLED  => 'bg-danger text-white',
                            \App\Types\OrderStatus::REFUNDED   => 'bg-secondary text-white',
                        ];
                        $statusLabels = \App\Types\OrderStatus::labels();
                    @endphp

                    <div class="row g-3">
                        @forelse($orders as $order)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 border shadow-none mb-0">
                                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <!-- Top Row: Order ID and Status -->
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold text-dark fs-13">#{{ substr($order->id, 0, 8) }}...</span>
                                                <span class="badge {{ $badgeClass[$order->status] ?? 'bg-secondary' }}">
                                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                                </span>
                                            </div>

                                            <!-- Service Name -->
                                            <h6 class="fw-bold mb-1 text-truncate" title="{{ $order->service_name }}">
                                                {{ $order->service_name }}
                                            </h6>

                                            <!-- Product Type Tag -->
                                            <div class="mb-3">
                                                <span class="badge bg-secondary text-uppercase fs-11">
                                                    {{ $order->product_type ?? '—' }}
                                                </span>
                                            </div>

                                            <!-- Key Details Container -->
                                            <div class="bg-light p-2 rounded mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1 fs-12">
                                                    <span class="text-muted"><i class="feather-layers me-1"></i>Quantity:</span>
                                                    <span class="fw-semibold text-dark">{{ number_format($order->quantity) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-1 fs-12">
                                                    <span class="text-muted"><i class="feather-credit-card me-1"></i>Amount:</span>
                                                    <span class="fw-bold text-primary">₦{{ number_format($order->charge, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center fs-12">
                                                    <span class="text-muted"><i class="feather-calendar me-1"></i>Date:</span>
                                                    <span class="text-muted">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="pt-2 border-top">
                                            <a href="{{ url('/orders/' . $order->id) }}" class="btn btn-sm btn-light w-100 text-center">
                                                View Details <i class="feather-eye ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 text-muted">
                                    No orders found.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')