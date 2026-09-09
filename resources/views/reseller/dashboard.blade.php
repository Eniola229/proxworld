@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item">Dashboard</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('reseller.order.create') }}" class="btn btn-primary">
                    <i class="feather-plus-circle me-1"></i> Create Order
                </a>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show" role="alert">
                    {{ session('alert')['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Wallet Balance -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Wallet Balance</h6>
                                    <h3 class="mb-0">₦{{ number_format($balance, 2) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-primary-soft rounded">
                                    <i class="feather-dollar-sign fs-1"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="/wallet" class="btn btn-sm btn-primary">Add Funds</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Orders -->
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

                <!-- Total Spent -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-2">Total Spent</h6>
                                    <h3 class="mb-0">₦{{ number_format($totalSpent, 2) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-success-soft rounded">
                                    <i class="feather-trending-up fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Statistics -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Order Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div class="p-3 border rounded">
                                        <h4 class="text-warning">{{ $pendingOrders }}</h4>
                                        <span>Pending</span>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="p-3 border rounded">
                                        <h4 class="text-info">{{ $processingOrders }}</h4>
                                        <span>Processing</span>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="p-3 border rounded">
                                        <h4 class="text-success">{{ $completedOrders }}</h4>
                                        <span>Completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="row mt-4">
                <div class="col-xl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Orders</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ $orderCreateUrl ?? route('reseller.order.create') }}" class="btn btn-sm btn-primary">Create Order</a>
                                <a href="/orders" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Service</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->service_name }}</td>
                                            <td>{{ number_format($order->quantity) }}</td>
                                            <td>₦{{ number_format($order->charge, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No orders yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')