@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Wallet Transactions</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Wallet</li>
                </ul>
            </div>
        </div>
        <div class="main-content">
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

        @if(!auth('admin')->user()->isSupport())
        <!-- Statistics Cards -->
        <div class="row mb-4 mt-4">
            <div class="col-md-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2 fs-11">Total Transactions</h6>
                        <h3 class="mb-0">{{ number_format($totalTransactions) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2 fs-11">Total Deposits</h6>
                        <h3 class="mb-0 text-success">{{ number_format($totalDeposits) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2 fs-11">Total Debits</h6>
                        <h3 class="mb-0 text-danger">{{ number_format($totalDebits) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2 fs-11">Pending Approval</h6>
                        <h3 class="mb-0 text-warning">{{ number_format($pendingDeposits) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2 fs-11">Pending Amount</h6>
                        <h3 class="mb-0 text-warning">₦{{ number_format($pendingAmount, 0) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-2">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-2 fs-11">Completed Amount</h6>
                        <h3 class="mb-0 text-success">₦{{ number_format($completedAmount, 0) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Deposits Alert -->
        @if($pendingDeposits > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="feather-alert-circle me-3 fs-4"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold">Pending Deposits Alert</h6>
                <p class="mb-0">You have <strong>{{ $pendingDeposits }}</strong> pending deposits totaling 
                    <strong>₦{{ number_format($pendingAmount, 2) }}</strong> waiting for approval.</p>
            </div>
            <a href="{{ route('admin.wallet.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning">
                Review Now
            </a>
        </div>
        @endif
        @endif

        <!-- Main Content -->
        
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">All Transactions</h5>
                            <div class="card-header-action">
                                <button type="button" class="btn btn-sm btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                    <i class="feather-filter me-2"></i> Filters
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search and Filter Form -->
                        <div class="collapse {{ request()->hasAny(['search', 'type', 'status', 'payment_method', 'date_from', 'date_to', 'amount_min', 'amount_max']) ? 'show' : '' }}" id="filterCollapse">
                            <div class="card-body border-bottom bg-light">
                                <form method="GET" action="{{ route('admin.wallet.index') }}" class="row g-3">
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">Search</label>
                                        <input type="text" 
                                               name="search" 
                                               class="form-control" 
                                               placeholder="Reference, Customer..." 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">Type</label>
                                        <select name="type" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                                            <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Completed</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">Payment Method</label>
                                        <select name="payment_method" class="form-select">
                                            <option value="">All Methods</option> 

                                            <option value="korapay" {{ request('payment_method') == 'korapay' ? 'selected' : '' }}>Korapay</option>
                                            <option value="order debit" {{ request('payment_method') == 'order debit' ? 'selected' : '' }}>Order Debit</option>
                                            <option value="panel order (user)" {{ request('payment_method') == 'panel order (user)' ? 'selected' : '' }}>Panel Order (User)</option>
                                            <option value="Reseller Customer Payment" {{ request('payment_method') == 'Reseller Customer Payment' ? 'selected' : '' }}>Reseller Customer Payment</option>
                                            <option value="Panel Cost For Customer Order" {{ request('payment_method') == 'Panel Cost For Customer Order' ? 'selected' : '' }}>Panel Cost For Customer Order</option>
                                            <option value="api order debit" {{ request('payment_method') == 'api order debit' ? 'selected' : '' }}>Api Order Debit</option>
                                             <option value="fincra" {{ request('payment_method') == 'fincra' ? 'selected' : '' }}>Fincra</option>
                                             
                                            <option value="paystack" {{ request('payment_method') == 'paystack' ? 'selected' : '' }}>Paystack</option>
                                            <option value="flutterwave" {{ request('payment_method') == 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                            <option value="admin_adjustment" {{ request('payment_method') == 'admin_adjustment' ? 'selected' : '' }}>Admin Adjustment</option>
                                            <option value="refund" {{ request('payment_method') == 'refund' ? 'selected' : '' }}>Refund</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fs-11">From Date</label>
                                        <input type="date" 
                                               name="date_from" 
                                               class="form-control" 
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fs-11">To Date</label>
                                        <input type="date" 
                                               name="date_to" 
                                               class="form-control" 
                                               value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label fs-11">Min Amount</label>
                                        <input type="number" 
                                               name="amount_min" 
                                               class="form-control" 
                                               placeholder="0" 
                                               value="{{ request('amount_min') }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <div class="d-flex gap-1 w-100">
                                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                <i class="feather-search"></i>
                                            </button>
                                            <a href="{{ route('admin.wallet.index') }}" class="btn btn-light btn-sm">
                                                <i class="feather-x"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Transactions Table -->
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Balance Before</th>
                                            <th>Balance After</th>
                                            <th>Payment Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $transaction)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.wallet.show', $transaction->id) }}" class="fw-bold text-primary">
                                                        {{ $transaction->reference }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.customers.show', $transaction->user_id) }}" class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-sm bg-soft-primary text-primary me-2">
                                                            {{ substr($transaction->user->name, 0, 2) }}
                                                        </div>
                                                        <span>{{ $transaction->user->name }}</span>
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
                                                <td class="fw-bold">₦{{ number_format($transaction->amount, 2) }}</td>
                                                <td>₦{{ number_format($transaction->balance_before, 2) }}</td>
                                                <td class="fw-bold">₦{{ number_format($transaction->balance_after, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info">
                                                        {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($transaction->status == 'success')
                                                        <span class="badge bg-soft-success text-success">
                                                            <i class="feather-check-circle me-1"></i> Completed
                                                        </span>
                                                    @elseif($transaction->status == 'pending')
                                                        <span class="badge bg-soft-warning text-warning">
                                                            <i class="feather-clock me-1"></i> Pending
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">
                                                            <i class="feather-x-circle me-1"></i> Failed
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>{{ $transaction->created_at->format('M d, Y') }}</div>
                                                    <span class="fs-11 text-muted">{{ $transaction->created_at->format('H:i') }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.wallet.show', $transaction->id) }}" 
                                                       class="btn btn-sm btn-light-brand">
                                                        <i class="feather-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i class="feather-credit-card fs-1 text-muted"></i>
                                                    </div>
                                                    <h6 class="text-muted">No transactions found</h6>
                                                    @if(request()->hasAny(['search', 'type', 'status', 'payment_method', 'date_from', 'date_to', 'amount_min', 'amount_max']))
                                                        <p class="text-muted mb-0">Try adjusting your filters</p>
                                                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-sm btn-primary mt-3">
                                                            Clear Filters
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($transactions->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 fs-12">
                                        Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ number_format($transactions->total()) }} transactions
                                    </p>
                                </div>
                                <div>
                                    {{ $transactions->links() }}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

@include('admin.components.footer')