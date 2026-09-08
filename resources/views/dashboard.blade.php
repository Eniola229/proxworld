@include('components.g-header')
@include('components.nav')
    <main class="nxl-container">
        <div class="nxl-content">
            
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item">Home</li>
                        <li class="breadcrumb-item">Dashboard</li>
                    </ul>
                </div>
            </div>
            <!-- [ page-header ] end -->

            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    
                    <!-- [Wallet Balance Card] -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-dollar-sign"></i>
                                        </div>
                                        <div>
                                            <!-- Real Data: Wallet Balance -->
                                            <div class="fs-4 fw-bold text-dark">₦{{ number_format($balance, 2) }}</div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Current Balance</h3>
                                        </div>
                                    </div>
                                    <a href="{{ route('wallet.index') }}">
                                        <i class="feather-plus-circle fs-4 text-primary"></i>
                                    </a>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a href="javascript:void(0);" class="fs-12 fw-medium text-muted">Status</a>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-success">Active</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- [Pending Orders] -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-clock"></i>
                                        </div>
                                        <div>
                                            <!-- Real Data: Pending -->
                                            <div class="fs-4 fw-bold text-dark">{{ $pendingOrders }}</div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Pending Orders</h3>
                                        </div>
                                    </div>
                                    <a href="{{ route('orders.index') }}">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a href="javascript:void(0);" class="fs-12 fw-medium text-muted">Total Orders</a>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-dark">{{ $totalOrders }}</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalOrders > 0 ? ($pendingOrders / $totalOrders * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- [Completed Orders] -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-check-circle"></i>
                                        </div>
                                        <div>
                                            <!-- Real Data: Completed -->
                                            <div class="fs-4 fw-bold text-dark">{{ $completedOrders }}</div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Completed Orders</h3>
                                        </div>
                                    </div>
                                    <a href="{{ route('orders.index') }}">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a href="javascript:void(0);" class="fs-12 fw-medium text-muted">Success Rate</a>
                                        <div class="w-100 text-end">
                                            @php
                                                $successRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0;
                                            @endphp
                                            <span class="fs-12 text-success">{{ $successRate }}%</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $successRate }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- [Processing Orders - UPDATED] -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-refresh-cw"></i>
                                        </div>
                                        <div>
                                            <!-- Real Data: Processing (In progress) -->
                                            <div class="fs-4 fw-bold text-dark">{{ $processingOrders }}</div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">In Progress</h3>
                                        </div>
                                    </div>
                                    <a href="{{ route('orders.index') }}">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                </div>
                                <div class="pt-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <a href="javascript:void(0);" class="fs-12 fw-medium text-muted">Active Jobs</a>
                                        <div class="w-100 text-end">
                                            <span class="fs-12 text-dark">{{ $processingOrders }}</span>
                                        </div>
                                    </div>
                                    <div class="progress mt-2 ht-3">
                                        @php
                                            $processingPercentage = $totalOrders > 0 ? round(($processingOrders / $totalOrders) * 100) : 0;
                                        @endphp
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $processingPercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- [Latest Orders] start -->
                    <div class="col-xxl-8">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Latest Orders</h5>
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary float-end">View All</a>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>Service</th>
                                                <th>Link</th>
                                                <th>Quantity</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th class="text-end">Charge</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentOrders as $order)
                                                <tr>
                                                    <td>{{ $order->service_name }}</td>
                                                    <td>
                                                        <span class="badge bg-soft-secondary text-secondary text-uppercase">{{ $order->product_type ?? '—' }}</span>
                                                    </td>
                                                    <td>{{ number_format($order->quantity) }}</td>
                                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        @if($order->status == 'completed')
                                                            <span class="badge bg-soft-success text-success">
                                                                <i class="feather-check-circle me-1"></i> Completed
                                                            </span>
                                                        @elseif($order->status == 'processing')
                                                            <span class="badge bg-soft-info text-info">
                                                                <i class="feather-loader me-1"></i> In Progress
                                                            </span>
                                                        @elseif($order->status == 'pending')
                                                            <span class="badge bg-soft-warning text-warning">
                                                                <i class="feather-clock me-1"></i> Pending
                                                            </span>
                                                        @elseif($order->status == 'refunded')
                                                            <span class="badge bg-soft-primary text-primary">
                                                                <i class="feather-rotate-ccw me-1"></i> Refunded
                                                            </span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-soft-danger text-danger">
                                                                <i class="feather-x-circle me-1"></i> Cancelled
                                                            </span>
                                                        @else
                                                            <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($order->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">₦{{ number_format($order->charge, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">
                                                        No orders found. 
                                                        <a href="{{ route('order.create') }}" class="fw-bold text-primary">Place an Order</a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Latest Orders] end -->

                    <!-- [Quick Actions / Total Spent] Start -->
                    <div class="col-xxl-4">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Total Spent</h5>
                                <h2 class="display-6 fw-bold text-dark mb-3">₦{{ number_format($totalSpent, 2) }}</h2>
                                <p class="text-muted mb-4">Lifetime spending on ProxWorld.com</p>
                                
                                <hr class="border-dashed my-4">
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('order.create') }}" class="btn btn-primary btn-lg">
                                        <i class="feather-plus me-2"></i> New Order
                                    </a>
                                    <a href="{{ route('wallet.index') }}" class="btn btn-light btn-lg">
                                        <i class="feather-wallet me-2"></i> Add Funds
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [Quick Actions] End -->

                </div>
            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>

    <!-- Community Modal -->
    <div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-4 pb-4">
                    <div class="mb-4">
                        <i class="feather-message-circle" style="font-size: 3rem; color: blue;"></i>
                    </div>
                    <h4 class="mb-3">Join Our Community!</h4>
                    <p class="text-muted mb-4">
                        Stay updated with the latest offers, tips, and announcements.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="https://whatsapp.com/channel/0029Vb7I1e3JuyAL31rIia0r" target="_blank" class="btn btn-success">
                            <i class="feather-users me-2"></i> Join WhatsApp Channel
                        </a>
                        <a href="https://t.me/+ZA-kt075CUpkNTg0" target="_blank" class="btn" style="background-color: #229ED9; color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="white" class="me-2" style="margin-bottom: 2px;">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg> Join Telegram Channel
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
        // Show WhatsApp modal on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const modal = new bootstrap.Modal(document.getElementById('whatsappModal'));
                modal.show();
            }, 1000);
        });
    </script>

</body>
</html>