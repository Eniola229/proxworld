@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
         
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Dashboard</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <!-- Period Filter -->
                    <a href="{{ route('admin.dashboard', ['period' => 'today']) }}" 
                       class="btn btn-sm {{ $period == 'today' ? 'btn-primary' : 'btn-light' }}">
                        Today
                    </a>
                    <a href="{{ route('admin.dashboard', ['period' => 'week']) }}" 
                       class="btn btn-sm {{ $period == 'week' ? 'btn-primary' : 'btn-light' }}">
                        Week
                    </a>
                    <a href="{{ route('admin.dashboard', ['period' => 'month']) }}" 
                       class="btn btn-sm {{ $period == 'month' ? 'btn-primary' : 'btn-light' }}">
                        Month
                    </a>
                    <a href="{{ route('admin.dashboard', ['period' => 'year']) }}" 
                       class="btn btn-sm {{ $period == 'year' ? 'btn-primary' : 'btn-light' }}">
                        Year
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                
                <!-- CUSTOMERS STATISTICS -->
                <div class="col-12 mb-3">
                    <h6 class="fw-bold text-dark">Customer Statistics</h6>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-primary-subtle">
                                        <i class="feather-users text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalCustomers) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Total Customers</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fs-12 fw-medium text-muted">New ({{ ucfirst($period) }})</span>
                                    <span class="fs-12 text-success fw-bold">+{{ $newCustomers }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Customers Today</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($customersToday) }}</div>
                            <div class="text-muted fs-11">New registrations today</div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Customers This Week</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($customersWeek) }}</div>
                            <div class="text-muted fs-11">New registrations this week</div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Customers This Month</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($customersMonth) }}</div>
                            <div class="text-muted fs-11">New registrations this month</div>
                        </div>
                    </div>
                </div>

                <!-- ORDERS STATISTICS -->
                <div class="col-12 mb-3 mt-4">
                    <h6 class="fw-bold text-dark">Order Statistics</h6>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-success-subtle">
                                        <i class="feather-shopping-cart text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalOrders) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Total Orders</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fs-12 fw-medium text-muted">{{ ucfirst($period) }}</span>
                                    <span class="fs-12 text-success fw-bold">{{ $ordersInPeriod }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Orders Today</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($ordersToday) }}</div>
                            <div class="text-muted fs-11">Orders placed today</div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Orders This Week</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($ordersWeek) }}</div>
                            <div class="text-muted fs-11">Orders this week</div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Orders This Month</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($ordersMonth) }}</div>
                            <div class="text-muted fs-11">Orders this month</div>
                        </div>
                    </div>
                </div>
<!-- ORDER STATUS BREAKDOWN -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Order Status ({{ ucfirst($period) }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-12 fw-medium text-muted">Completed</span>
                                    <span class="fs-12 fw-bold text-success">{{ number_format($completedOrders) }}</span>
                                </div>
                                <div class="progress ht-3">
                                    <div class="progress-bar bg-success" style="width: {{ $ordersInPeriod > 0 ? ($completedOrders/$ordersInPeriod*100) : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-12 fw-medium text-muted">Processing</span>
                                    <span class="fs-12 fw-bold text-info">{{ number_format($processingOrders) }}</span>
                                </div>
                                <div class="progress ht-3">
                                    <div class="progress-bar bg-info" style="width: {{ $ordersInPeriod > 0 ? ($processingOrders/$ordersInPeriod*100) : 0 }}%"></div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-12 fw-medium text-muted">Pending</span>
                                    <span class="fs-12 fw-bold text-warning">{{ number_format($pendingOrders) }}</span>
                                </div>
                                <div class="progress ht-3">
                                    <div class="progress-bar bg-warning" style="width: {{ $ordersInPeriod > 0 ? ($pendingOrders/$ordersInPeriod*100) : 0 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fs-12 fw-medium text-muted">Cancelled</span>
                                    <span class="fs-12 fw-bold text-danger">{{ number_format($cancelledOrders) }}</span>
                                </div>
                                <div class="progress ht-3">
                                    <div class="progress-bar bg-danger" style="width: {{ $ordersInPeriod > 0 ? ($cancelledOrders/$ordersInPeriod*100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!auth('admin')->user()->isSupport())
                @if(auth('admin')->user()->canEditOrders())
                {{-- Provider Balances (one card per provider) --}}
                @php $dashProviders = \App\Models\Provider::orderBy('priority')->get(); @endphp
                 
                @foreach($dashProviders as $dashProvider)
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full {{ $dashProvider->is_active ? '' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text avatar-lg {{ $dashProvider->is_active ? 'bg-info-subtle' : 'bg-secondary-subtle' }}">
                                        <i class="feather-server {{ $dashProvider->is_active ? 'text-info' : 'text-secondary' }}"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">
                                            @if($dashProvider->cached_balance !== null)
                                                ₦{{ number_format($dashProvider->cached_balance, 2) }}
                                            @else
                                                <span class="text-muted fs-5">N/A</span>
                                            @endif
                                        </div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $dashProvider->name }}</h3>
                                    </div>
                                </div>
                                <span class="badge {{ $dashProvider->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                    {{ $dashProvider->is_active ? 'Active' : 'Off' }}
                                </span>
                            </div>
                            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="fs-11 text-muted">
                                    Priority {{ $dashProvider->priority }} ·
                                    {{ $dashProvider->balance_checked_at ? $dashProvider->balance_checked_at->diffForHumans() : 'never refreshed' }}
                                </span>
                                <form method="POST" action="{{ route('admin.providers.refresh-balance', $dashProvider->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-light" title="Refresh">
                                        <i class="feather-refresh-cw" style="font-size:11px;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                 
                @if($dashProviders->isEmpty())
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body text-center text-muted py-4">
                            <i class="feather-server fs-3 d-block mb-2"></i>
                            No providers configured.
                            <a href="{{ route('admin.providers.create') }}" class="d-block mt-1">Add one</a>
                        </div>
                    </div>
                </div>
                @endif
                 
                @endif
                <!-- REVENUE STATISTICS -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Revenue Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="fs-12 text-muted d-block mb-1">{{ ucfirst($period) }}</span>
                                <span class="fs-4 fw-bold text-success">₦{{ number_format($revenueInPeriod, 2) }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="fs-12 text-muted d-block mb-1">Today</span>
                                <span class="fs-4 fw-bold text-dark">₦{{ number_format($revenueToday, 2) }}</span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <span class="fs-12 text-muted d-block mb-1">This Week</span>
                                <span class="fs-4 fw-bold text-dark">₦{{ number_format($revenueWeek, 2) }}</span>
                            </div>
                            <div>
                                <span class="fs-12 text-muted d-block mb-1">This Month</span>
                                <span class="fs-4 fw-bold text-dark">₦{{ number_format($revenueMonth, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- SUPPORT TICKETS -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Support Tickets</h5>
                            <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-light-brand">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 text-center">
                                <div class="fs-1 fw-bold text-dark">{{ number_format($totalTickets) }}</div>
                                <span class="fs-12 text-muted">Total Tickets</span>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div class="text-center">
                                    <div class="fs-4 fw-bold text-warning">{{ number_format($openTickets) }}</div>
                                    <span class="fs-11 text-muted">Open</span>
                                </div>
                                <div class="text-center">
                                    <div class="fs-4 fw-bold text-success">{{ number_format($closedTickets) }}</div>
                                    <span class="fs-11 text-muted">Closed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!auth('admin')->user()->isSupport())
                <!-- WALLET STATISTICS -->
                <div class="col-12 mb-3 mt-4">
                    <h6 class="fw-bold text-dark">Wallet Transaction Statistics</h6>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-warning-subtle">
                                        <i class="feather-credit-card text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalDeposits) }}</div>
                                        <h3 class="fs-13 fw-semibold text-truncate-1-line">Total Deposits</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fs-12 fw-medium text-muted">{{ ucfirst($period) }}</span>
                                    <span class="fs-12 text-success fw-bold">{{ $depositsInPeriod }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Deposits Today</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($depositsToday) }}</div>
                            <div class="text-muted fs-11">₦{{ number_format($depositAmountToday, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Deposits This Week</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($depositsWeek) }}</div>
                            <div class="text-muted fs-11">₦{{ number_format($depositAmountWeek, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="fs-13 fw-bold mb-3">Deposits This Month</h6>
                            <div class="fs-3 fw-bold text-dark mb-2">{{ number_format($depositsMonth) }}</div>
                            <div class="text-muted fs-11">₦{{ number_format($depositAmountMonth, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- PENDING DEPOSITS ALERT -->
                @if($pendingDeposits > 0)
                <div class="col-12">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="feather-alert-circle me-3 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">Pending Deposits Alert</h6>
                            <p class="mb-0">You have <strong>{{ $pendingDeposits }}</strong> pending deposits totaling 
                                <strong>₦{{ number_format($pendingDepositAmount, 2) }}</strong> waiting for approval.</p>
                        </div>
                        <a href="{{ route('admin.wallet.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning">
                            Review Now
                        </a>
                    </div>
                </div>
                @endif
                @endif

                <!-- RECENT ORDERS TABLE -->
                <div class="col-xxl-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Orders</h5>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Order ID</th>
                                            <th>Customer</th>
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
                                                <td>
                                                    <a href="{{ route('admin.customers.show', $order->user_id) }}">
                                                        {{ $order->user->name }}
                                                    </a>
                                                </td>
                                                <td>{{ Str::limit($order->service_name, 25) }}</td>
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
                                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">No orders yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RECENT CUSTOMERS -->
                <div class="col-xxl-4">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Customers</h5>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-light-brand">View All</a>
                        </div>
                        <div class="card-body">
                            @forelse($recentCustomers as $customer)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <div class="avatar-text avatar-md bg-soft-primary text-primary">
                                        <span>{{ substr($customer->name, 0, 2) }}</span>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="fw-bold d-block">
                                            {{ $customer->name }}
                                        </a>
                                        <span class="fs-11 text-muted">{{ $customer->email }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-11 text-muted">{{ $customer->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted py-4">No customers yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if(!auth('admin')->user()->isSupport())
                <!-- RECENT TRANSACTIONS -->
                <div class="col-12 mt-3">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Recent Wallet Transactions</h5>
                            <a href="{{ route('admin.wallet.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Type</th>
                                            <th>Amount</th>
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
                                                    <a href="{{ route('admin.customers.show', $transaction->user_id) }}">
                                                        {{ $transaction->user->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($transaction->type == 'credit')
                                                        <span class="badge bg-soft-success text-success">Credit</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">Debit</span>
                                                    @endif
                                                </td>
                                                <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ ucfirst($transaction->payment_method) }}</td>
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
                                                <td colspan="7" class="text-center py-4">No transactions yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

    </div>
@include('admin.components.footer')