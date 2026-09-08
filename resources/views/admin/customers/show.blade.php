@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<style>
    .log-row:hover {
        background-color: #f8f9fa;
    }
    .log-details td {
        border-top: none !important;
    }
</style> 

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Customer Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item">{{ $customer->name }}</li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                
                <!-- Customer Info Card -->
                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="avatar-text avatar-xl bg-soft-primary text-primary mb-3">
                                    {{ substr($customer->name, 0, 2) }}
                                </div>
                                <h5 class="mb-1">{{ $customer->name }}</h5>
                                <p class="fs-12 text-muted mb-3">{{ $customer->email }}</p>

                                @if($customer->status === 'BLOCK')
                                    <span class="badge bg-soft-danger text-danger">Blocked Customer</span>
                                @else
                                    <span class="badge bg-soft-success text-success">Active Customer</span>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Customer ID:</span>
                                    <span class="fs-12 fw-bold">{{ substr($customer->id, 0, 13) }}...</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Wallet Balance:</span>
                                    <span class="fs-12 fw-bold text-success">₦{{ number_format($customer->balance, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Total Deposits:</span>
                                    <span class="fs-12 fw-bold">₦{{ number_format($totalDeposits, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Total Spent:</span>
                                    <span class="fs-12 fw-bold">₦{{ number_format($totalSpent, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Member Since:</span>
                                    <span class="fs-12 fw-bold">{{ $customer->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="d-grid gap-3">
                                @if(auth('admin')->user()->canEditCustomer())
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-primary">
                                        <i class="feather-edit me-2"></i> Edit Customer
                                    </a>
                                @endif

                                @if(auth('admin')->user()->canAdjustBalance())
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#adjustBalanceModal">
                                        <i class="feather-dollar-sign me-2"></i> Adjust Balance
                                    </button>
                                @endif
                                
                                @if(auth('admin')->user()->canEditCustomer())
                                    <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name"   value="{{ $customer->name }}">
                                        <input type="hidden" name="email"  value="{{ $customer->email }}">
                                        <input type="hidden" name="status" value="{{ $customer->status === 'BLOCK' ? 'ACTIVE' : 'BLOCK' }}">
                                        <button type="submit" class="btn w-100 {{ $customer->status === 'BLOCK' ? 'btn-success' : 'btn-danger' }}">
                                            <i class="feather-{{ $customer->status === 'BLOCK' ? 'unlock' : 'lock' }} me-2"></i>
                                            {{ $customer->status === 'BLOCK' ? 'Unblock Customer' : 'Block Customer' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="col-xxl-8 col-xl-6">
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-text avatar-md bg-primary-subtle me-3">
                                            <i class="feather-shopping-cart text-primary"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0">{{ $totalOrders }}</h4>
                                            <p class="fs-12 text-muted mb-0">Total Orders</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-text avatar-md bg-success-subtle me-3">
                                            <i class="feather-check-circle text-success"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0">{{ $completedOrders }}</h4>
                                            <p class="fs-12 text-muted mb-0">Completed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-text avatar-md bg-warning-subtle me-3">
                                            <i class="feather-clock text-warning"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0">{{ $pendingOrders }}</h4>
                                            <p class="fs-12 text-muted mb-0">Pending</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-text avatar-md bg-info-subtle me-3">
                                            <i class="feather-refresh-cw text-info"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0">{{ $processingOrders }}</h4>
                                            <p class="fs-12 text-muted mb-0">Processing</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Recent Orders</h5>
                            <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" class="btn btn-sm btn-light-brand">
                                View All Orders
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Order ID</th>
                                            <th>Service</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-primary">
                                                        #{{ substr($order->id, 0, 8) }}
                                                    </a>
                                                </td>
                                                <td>{{ Str::limit($order->service_name, 30) }}</td>
                                                <td>₦{{ number_format($order->charge, 2) }}</td>
                                                <td>
                                                    @if($order->status == 'completed')
                                                        <span class="badge bg-soft-success text-success">Completed</span>
                                                    @elseif($order->status == 'processing')
                                                        <span class="badge bg-soft-info text-info">Processing</span>
                                                    @elseif($order->status == 'pending')
                                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">{{ ucfirst($order->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">No orders yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($recentOrders->hasPages())
                            <div class="card-footer">
                                {{ $recentOrders->appends(['logs_page' => request('logs_page')])->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Wallet Transactions -->
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Recent Wallet Transactions</h5>
                            <a href="{{ route('admin.wallet.index', ['search' => $customer->email]) }}" class="btn btn-sm btn-light-brand">
                                View All Transactions
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Balance Before</th>
                                            <th>Balance After</th>
                                            <th>Payment Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.wallet.show', $transaction->id) }}" class="fw-bold text-primary">
                                                        {{ $transaction->reference }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($transaction->type == 'credit')
                                                        <span class="badge bg-soft-success text-success">
                                                            <i class="feather-arrow-down me-1"></i> Credit
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">
                                                            <i class="feather-arrow-up me-1"></i> Debit
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                                <td>₦{{ number_format($transaction->balance_before, 2) }}</td>
                                                <td>₦{{ number_format($transaction->balance_after, 2) }}</td>
                                                <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                                                <td>
                                                    @if($transaction->status == 'success')
                                                        <span class="badge bg-soft-success text-success">Completed</span>
                                                    @elseif($transaction->status == 'pending')
                                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-3 text-muted">No transactions yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referred Users Table with Statistics -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-users me-2"></i>Referred Users
                        </h5>
                    </div>
                    
                    <!-- Statistics in Card Body -->
                    <div class="card-body border-bottom">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text avatar-md bg-primary-subtle me-3">
                                        <i class="feather-users text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $totalReferred }}</h4>
                                        <p class="fs-12 text-muted mb-0">Total Referred</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text avatar-md bg-success-subtle me-3">
                                        <i class="feather-dollar-sign text-success"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $depositedCount }}</h4>
                                        <p class="fs-12 text-muted mb-0">Deposited</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text avatar-md bg-info-subtle me-3">
                                        <i class="feather-shopping-cart text-info"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $orderedCount }}</h4>
                                        <p class="fs-12 text-muted mb-0">Placed Orders</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text avatar-md bg-warning-subtle me-3">
                                        <i class="feather-gift text-warning"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $bonusPaidCount }}</h4>
                                        <p class="fs-12 text-muted mb-0">Bonus Paid</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="border-b">
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Deposited</th>
                                        <th>Ordered</th>
                                        <th>Bonus Paid</th>
                                        <th>Referred On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($referredUsers as $referred)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm bg-soft-primary text-primary me-2">
                                                        {{ substr($referred->referredUser->name ?? 'U', 0, 2) }}
                                                    </div>
                                                    <a href="{{ route('admin.customers.show', $referred->referred_user_id) }}" class="fw-bold text-primary">
                                                        {{ $referred->referredUser->name ?? 'Unknown User' }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $referred->referredUser->email ?? 'N/A' }}</td>
                                            <td>
                                                @if($referred->referredUser)
                                                    <span class="badge bg-soft-success text-success">Active</span>
                                                @else
                                                    <span class="badge bg-soft-danger text-danger">Deleted</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($referred->has_deposited)
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check me-1"></i>Yes
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">
                                                        <i class="feather-x me-1"></i>No
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($referred->has_ordered)
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check me-1"></i>Yes
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary">
                                                        <i class="feather-x me-1"></i>No
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($referred->bonus_paid)
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check me-1"></i>Paid
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="feather-clock me-1"></i>Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $referred->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">
                                                <i class="feather-users fs-3 d-block mb-2"></i>
                                                This customer hasn't referred anyone yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($referredUsers->hasPages())
                        <div class="card-footer">
                            {{ $referredUsers->appends([
                                'orders_page' => request('orders_page'),
                                'logs_page' => request('logs_page')
                            ])->links() }}
                        </div>
                    @endif
                </div>
            </div>

                <!-- Customer Activity Logs (Super Admin Only) -->
                @if(auth('admin')->user()->canViewCustomerLogs() && $logs)
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-header bg-soft-danger">
                            <h5 class="card-title text-danger">
                                <i class="feather-shield me-2"></i> Customer Activity Logs (Super Admin Only)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th width="5%"></th>
                                            <th>Type</th>
                                            <th>Method</th>
                                            <th>Reference</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Description</th>
                                            <th>IP Address</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                            <tr class="log-row" style="cursor: pointer;" onclick="toggleLogDetails('log-{{ $log->id }}')">
                                                <td>
                                                    <i class="feather-chevron-right" id="icon-log-{{ $log->id }}"></i>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-primary text-primary">
                                                        {{ ucfirst($log->type) }}
                                                    </span>
                                                </td>
                                                <td><code class="fs-11">{{ $log->method }}</code></td>
                                                <td><code class="fs-11">{{ $log->reference }}</code></td>
                                                <td>₦{{ number_format($log->amount, 2) }}</td>
                                                <td>
                                                    @if($log->status == 'success')
                                                        <span class="badge bg-soft-success text-success">Success</span>
                                                    @elseif($log->status == 'failed')
                                                        <span class="badge bg-soft-danger text-danger">Failed</span>
                                                    @elseif($log->status == 'pending')
                                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                                    @else
                                                        <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($log->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ Str::limit($log->description, 40) }}</td>
                                                <td><code class="fs-11">{{ $log->ip_address }}</code></td>
                                                <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr id="details-log-{{ $log->id }}" class="log-details" style="display: none;">
                                                <td colspan="9" class="bg-light">
                                                    <div class="p-4">
                                                        <h6 class="mb-3 text-primary">
                                                            <i class="feather-info me-2"></i>Full Log Details
                                                        </h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Type</label>
                                                                    <strong>{{ ucfirst($log->type) }}</strong>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Status</label>
                                                                    <strong>
                                                                        @if($log->status == 'success')
                                                                            <span class="badge bg-soft-success text-success">Success</span>
                                                                        @elseif($log->status == 'failed')
                                                                            <span class="badge bg-soft-danger text-danger">Failed</span>
                                                                        @elseif($log->status == 'pending')
                                                                            <span class="badge bg-soft-warning text-warning">Pending</span>
                                                                        @else
                                                                            <span class="badge bg-soft-secondary">{{ ucfirst($log->status) }}</span>
                                                                        @endif
                                                                    </strong>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Amount</label>
                                                                    <strong class="text-success">₦{{ number_format($log->amount, 2) }}</strong>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">IP Address</label>
                                                                    <code>{{ $log->ip_address }}</code>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Reference</label>
                                                                    <code>{{ $log->reference }}</code>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Method</label>
                                                                    <code>{{ $log->method }}</code>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Date & Time</label>
                                                                    <strong>{{ $log->created_at->format('M d, Y - h:i:s A') }}</strong>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="p-3 border rounded bg-white">
                                                                    <label class="fs-11 text-muted mb-1 d-block">Description</label>
                                                                    <p class="mb-0">{{ $log->description ?? 'No description available' }}</p>
                                                                </div>
                                                            </div>
                                                            @if($log->error_message)
                                                            <div class="col-12">
                                                                <div class="p-3 border rounded bg-danger bg-opacity-10">
                                                                    <label class="fs-11 text-danger mb-1 d-block">
                                                                        <i class="feather-alert-circle me-1"></i>Error Message
                                                                    </label>
                                                                    <p class="mb-0 text-danger">{{ $log->error_message }}</p>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if($log->request_data)
                                                            <div class="col-12">
                                                                <div class="p-3 border rounded bg-info bg-opacity-10">
                                                                    <label class="fs-11 text-info mb-2 d-block">
                                                                        <i class="feather-upload me-1"></i>Request Data
                                                                    </label>
                                                                    <pre class="bg-dark text-white p-3 rounded mb-0" style="max-height: 300px; overflow-y: auto; font-size: 0.75rem;"><code>{{ is_string($log->request_data) ? json_encode(json_decode($log->request_data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : json_encode($log->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            @if($log->response_data)
                                                            <div class="col-12">
                                                                <div class="p-3 border rounded bg-success bg-opacity-10">
                                                                    <label class="fs-11 text-success mb-2 d-block">
                                                                        <i class="feather-download me-1"></i>Response Data
                                                                    </label>
                                                                    <pre class="bg-dark text-white p-3 rounded mb-0" style="max-height: 300px; overflow-y: auto; font-size: 0.75rem;"><code>{{ is_string($log->response_data) ? json_encode(json_decode($log->response_data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : json_encode($log->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                                                </div>
                                                            </div>
                                                            @endif
                                                                 @if($log->description)      
                                                                <div class="p-3 border rounded bg-success bg-opacity-10">
                                                                    <label class="fs-11 text-success mb-2 d-block">
                                                                        <i class="feather-download me-1"></i>Response Data
                                                                    </label>
                                                                    <pre class="bg-dark text-white p-3 rounded mb-0" style="max-height: 300px; overflow-y: auto; font-size: 0.75rem;"><code>{{ $log->description }}</code></pre>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-3 text-muted">No activity logs</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($logs->hasPages())
                            <div class="card-footer">
                                {{ $logs->appends(['orders_page' => request('orders_page')])->links() }}
                            </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
</main>

<!-- Adjust Balance Modal (Super Admin & Accountant Only) -->
@if(auth('admin')->user()->canAdjustBalance())
<div class="modal fade" id="adjustBalanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.customers.adjust-balance', $customer->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Customer Balance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Current Balance:</strong> ₦{{ number_format($walletBalance, 2) }}
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transaction Type</label>
                        <select name="type" class="form-select" required>
                            <option value="credit">Credit (Add Money)</option>
                            <option value="debit">Debit (Subtract Money)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (₦)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description/Reason</label>
                        <textarea name="description" class="form-control" rows="3" required placeholder="e.g., Bonus credit, Refund, Correction, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Adjust Balance</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    // Toggle log details
    function toggleLogDetails(logId) {
        const detailsRow = document.getElementById('details-' + logId);
        const icon = document.getElementById('icon-' + logId);
        
        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
            icon.className = 'feather-chevron-down';
        } else {
            detailsRow.style.display = 'none';
            icon.className = 'feather-chevron-right';
        }
    }
</script>

@include('admin.components.footer')