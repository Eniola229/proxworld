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
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            
                                    <th>Order ID</th>
                                    <th>Service</th>
                                    <th>Link</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->service_name }}</td>
                                    <td>
                                        <a href="{{ $order->link }}" target="_blank" class="text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ Str::limit($order->link, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ number_format($order->quantity) }}</td>
                                    <td>₦{{ number_format($order->charge, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : ($order->status == 'processing' ? 'info' : 'danger')) }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        -
                               <!--          <a href="/orders/{{ $order->id }}/status" class="btn btn-sm btn-info" title="Check Status">
                                            <i class="fas fa-sync-alt"></i>
                                        </a> -->
                                    </td>
                                </tr>
                                @empty 
                                <tr>
                                    <td colspan="9" class="text-center">No orders found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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