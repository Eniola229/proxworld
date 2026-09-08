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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Service</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $badgeClass = [
                                        \App\Types\OrderStatus::PENDING    => 'bg-warning',
                                        \App\Types\OrderStatus::PROCESSING => 'bg-info',
                                        \App\Types\OrderStatus::COMPLETED  => 'bg-success',
                                        \App\Types\OrderStatus::CANCELLED  => 'bg-danger',
                                        \App\Types\OrderStatus::REFUNDED   => 'bg-secondary',
                                    ];
                                    $statusLabels = \App\Types\OrderStatus::labels();
                                @endphp

                                @forelse($orders as $order)
                                <tr>
                                    <td>#{{ substr($order->id, 0, 8) }}...</td>
                                    <td>{{ $order->service_name }}</td>
                                    <td>
                                        <span class="badge bg-secondary text-uppercase">{{ $order->product_type ?? '—' }}</span>
                                    </td>
                                    <td>{{ number_format($order->quantity) }}</td>
                                    <td>₦{{ number_format($order->charge, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass[$order->status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No orders found</td>
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