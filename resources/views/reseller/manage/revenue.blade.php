@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Revenue Summary</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/manage/settings">Manage</a></li>
                    <li class="breadcrumb-item">Revenue</li>
                </ul>
            </div>
        </div>

        <div class="main-content">

            {{-- Date Range Filter --}}
            <div class="row mb-4">
                <div class="col-xl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="GET" action="{{ route('reseller.manage.revenue') }}" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">From</label>
                                    <input type="date" name="date_from" class="form-control"
                                           value="{{ $dateFrom }}" max="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">To</label>
                                    <input type="date" name="date_to" class="form-control"
                                           value="{{ $dateTo }}" max="{{ now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-filter me-1"></i> Filter
                                    </button>
                                    @if($dateFrom || $dateTo)
                                        <a href="{{ route('reseller.manage.revenue') }}" class="btn btn-outline-secondary">
                                            Clear
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Revenue / Profit / Orders (respects date filter) --}}
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Total Revenue</h6>
                                    <h3 class="mb-0">₦{{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-success-soft rounded">
                                    <i class="feather-dollar-sign fs-1"></i>
                                </div>
                            </div>
                            @if($dateFrom || $dateTo)
                                <div class="small text-muted mt-2">Filtered range</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Total Profit</h6>
                                    <h3 class="mb-0">₦{{ number_format($totalProfit, 2) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-primary-soft rounded">
                                    <i class="feather-trending-up fs-1"></i>
                                </div>
                            </div>
                            <div class="small text-muted mt-2">From completed orders only</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Total Orders</h6>
                                    <h3 class="mb-0">{{ $totalOrders }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-info-soft rounded">
                                    <i class="feather-shopping-cart fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Withdrawn / Available Balance (always lifetime, not date-filtered) --}}
            <div class="row mt-4">
                <div class="col-xl-6 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Total Withdrawn</h6>
                                    <h3 class="mb-0">₦{{ number_format($totalWithdrawn, 2) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-secondary-soft rounded">
                                    <i class="feather-arrow-up-circle fs-1"></i>
                                </div>
                            </div>
                            <div class="small text-muted mt-2">Pending, processing & successful withdrawals</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Available Balance</h6>
                                    <h3 class="mb-0">₦{{ number_format($availableBalance, 2) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-success-soft rounded">
                                    <i class="feather-wallet fs-1"></i>
                                </div>
                            </div>
                            <div class="small text-muted mt-2">Ready to withdraw</div>
                            <div class="mt-3">
                                <a href="{{ route('reseller.manage.withdraw') }}" class="btn btn-sm btn-primary">
                                    Withdraw
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Statistics Row (respects date filter) --}}
            <div class="row mt-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body text-center">
                            <h6 class="mb-2 text-success">Completed</h6>
                            <h3 class="mb-0 text-success">{{ $completedOrders }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body text-center">
                            <h6 class="mb-2 text-warning">Pending</h6>
                            <h3 class="mb-0 text-warning">{{ $pendingOrders }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body text-center">
                            <h6 class="mb-2 text-info">Processing</h6>
                            <h3 class="mb-0 text-info">{{ $processingOrders }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body text-center">
                            <h6 class="mb-2 text-danger">Cancelled</h6>
                            <h3 class="mb-0 text-danger">{{ $cancelledOrders }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Quantity</th>
                                            <th>Revenue</th>
                                            <th>Profit</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->name ?? 'Deleted User' }}</td>
                                            <td>{{ $order->service_name }}</td>
                                            <td>{{ number_format($order->quantity) }}</td>
                                            <td>₦{{ number_format($order->charge, 2) }}</td>
                                            <td>₦{{ number_format($reseller->realProfitForOrder($order), 2) }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'completed' => 'success',
                                                        'processing' => 'info',
                                                        'pending' => 'warning',
                                                        'cancelled' => 'danger',
                                                        'partial' => 'primary',
                                                    ];
                                                    $color = $statusColors[$order->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} text-{{ $color == 'warning' ? 'dark' : 'white' }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No orders found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $recentOrders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')