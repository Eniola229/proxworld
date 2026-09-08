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
                                        $statuses = [
                                            ''           => ['label' => 'All Orders',  'icon' => 'feather-list',        'active' => 'btn-secondary',  'text' => 'text-white'],
                                            'pending'    => ['label' => 'Pending',     'icon' => 'feather-clock',       'active' => 'btn-primary',    'text' => 'text-white'],
                                            'processing' => ['label' => 'Processing',  'icon' => 'feather-loader',      'active' => 'btn-warning',    'text' => 'text-white'],
                                            'completed'  => ['label' => 'Completed',   'icon' => 'feather-check-circle','active' => 'btn-success',    'text' => 'text-white'],
                                            'partial'    => ['label' => 'Partial',     'icon' => 'feather-pie-chart',   'active' => 'btn-info',       'text' => 'text-white'],
                                            'cancelled'  => ['label' => 'Cancelled',   'icon' => 'feather-x-circle',    'active' => 'btn-danger',     'text' => 'text-white'],
                                        ];
                                        $current = request('status', '');
                                    @endphp

                                    @foreach($statuses as $value => $meta)
                                        <button type="submit" name="status" value="{{ $value }}"
                                            class="btn btn-sm {{ $current === $value ? $meta['active'] : 'btn-light' }}">
                                            <i class="{{ $meta['icon'] }} me-1"></i>
                                            {{ $meta['label'] }}
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
                                                <th>Link</th>
                                                <th>Quantity</th>
                                                <th>Charge</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                                <tr>
                                                    <td>#{{ substr($order->id, 0, 8) }}...</td>
                                                    <td>{{ $order->service_name }}</td>
                                                    <td>
                                                        <a href="{{ $order->link }}" target="_blank" class="text-truncate d-block" style="max-width: 200px;">
                                                            {{ Str::limit($order->link, 30) }}
                                                        </a>
                                                    </td>
                                                    <td>{{ number_format($order->quantity) }}</td>
                                                    <td>₦{{ number_format($order->charge, 2) }}</td>
                                                    <td>
                                                        @if($order->status == 'completed')
                                                            <span class="badge bg-soft-success text-success">
                                                                <i class="feather-check-circle me-1"></i> Completed
                                                            </span>
                                                        @elseif($order->status == 'processing')
                                                            <span class="badge bg-soft-warning text-warning">
                                                                <i class="feather-loader me-1"></i> Processing
                                                            </span>
                                                        @elseif($order->status == 'pending')
                                                            <span class="badge bg-soft-primary text-primary">
                                                                <i class="feather-clock me-1"></i> Pending
                                                            </span>
                                                        @elseif($order->status == 'partial')
                                                            <span class="badge bg-soft-info text-info">
                                                                <i class="feather-pie-chart me-1"></i> Partial
                                                            </span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-soft-danger text-danger">
                                                                <i class="feather-x-circle me-1"></i> Cancelled (Refunded)
                                                            </span>
                                                        @else
                                                            <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($order->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $order->created_at->diffForHumans() }}</td>
                                                    <td>
                                                        @if($order->api_order_id && in_array($order->status, ['pending', 'processing']))
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
                                                                No {{ ucfirst($current) }} orders found.
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

                            <!-- Pagination -->
                            <div class="card-footer">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Accpond Promotion Modal -->
    <div class="modal fade" id="accpondPromoModal" tabindex="-1" aria-labelledby="accpondPromoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-4 pb-4">
                    <div class="mb-4">
                        <i class="feather-shopping-cart" style="font-size: 3rem; color: #3b82f6;"></i>
                    </div>
                    <h4 class="mb-3">Need Social Media Accounts?</h4>
                    <p class="text-muted mb-4">
                        Get verified and premium social media accounts at unbeatable prices! 
                        Instagram, Facebook, Twitter, TikTok and more available now.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="https://www.accpond.com.ng" target="_blank" class="btn btn-primary">
                            <i class="feather-external-link me-2"></i> Visit Accpond
                        </a>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Maybe Later
                        </button>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">www.accpond.com.ng</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.g-footer')

    <script>
        // Show the Accpond promotion modal on every page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const modal = new bootstrap.Modal(document.getElementById('accpondPromoModal'));
                modal.show();
            }, 1000);
        });
    </script>

</body>
</html>