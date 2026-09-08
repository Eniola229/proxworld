@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Transaction Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                    <li class="breadcrumb-item">{{ $transaction->reference }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.customers.show', $transaction->user_id) }}" class="btn btn-sm btn-light-brand">
                    <i class="feather-user me-2"></i> View Customer
                </a>
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
                
                <!-- Transaction Info Card -->
                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Transaction Information</h5>
                            <div>
                                @if($transaction->status == 'success')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($transaction->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Reference:</span>
                                    <code class="fs-11">{{ $transaction->reference }}</code>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Type:</span>
                                    @if($transaction->type == 'credit')
                                        <span class="badge bg-soft-success text-success">
                                            <i class="feather-arrow-down me-1"></i> Credit
                                        </span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">
                                            <i class="feather-arrow-up me-1"></i> Debit
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Amount:</span>
                                    <span class="fs-4 fw-bold {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type == 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Payment Method:</span>
                                    <span class="badge bg-soft-info text-info">
                                        {{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'N/A')) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Balance Before:</span>
                                    <span class="fs-12 fw-bold">₦{{ number_format($transaction->balance_before, 2) }}</span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Balance After:</span>
                                    <span class="fs-12 fw-bold text-success">₦{{ number_format($transaction->balance_after, 2) }}</span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Description:</span>
                                </div>
                                <p class="fs-12 text-muted mb-0">{{ $transaction->description }}</p>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Created:</span>
                                    <span class="fs-12 fw-bold">{{ $transaction->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Last Updated:</span>
                                    <span class="fs-12 fw-bold">{{ $transaction->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary me-3">
                                    {{ substr($transaction->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $transaction->user->name }}</h6>
                                    <p class="fs-12 text-muted mb-0">{{ $transaction->user->email }}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Current Balance:</span>
                                <span class="fs-12 fw-bold text-success">₦{{ number_format($customerBalance, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Total Transactions:</span>
                                <span class="fs-12 fw-bold">{{ number_format($totalTransactions) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fs-12 text-muted">Member Since:</span>
                                <span class="fs-12 fw-bold">{{ $transaction->user->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions & Logs -->
                <div class="col-xxl-8 col-xl-6">
                    
                    <!-- Admin Actions (Super Admin & Accountant Only) -->
                    @if(auth('admin')->user()->canEditTransactions())
                    <div class="card mb-3">
                        <div class="card-header bg-soft-warning">
                            <h5 class="card-title text-warning">
                                <i class="feather-shield me-2"></i> Admin Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                
                                <!-- Approve Transaction -->
                                @if($transaction->status == 'pending' && $transaction->type == 'credit')
                                <div class="col-md-4">
                                    <form method="POST" action="{{ route('admin.wallet.approve', $transaction->id) }}" onsubmit="return confirm('Are you sure you want to approve this transaction? ₦{{ number_format($transaction->amount, 2) }} will be credited to customer.')">
                                        @csrf
                                        <label class="form-label fw-bold">Approve Transaction</label>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="feather-check-circle me-2"></i> Approve
                                        </button>
                                    </form>
                                </div>
                                @endif

                                <!-- Reject Transaction -->
                                @if($transaction->status == 'pending')
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Reject Transaction</label>
                                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="feather-x-circle me-2"></i> Reject
                                    </button>
                                </div>
                                @endif

                                <!-- Delete Transaction -->
                                @if(auth('admin')->user()->canDeleteTransactions() && $transaction->status != 'completed')
                                <div class="col-md-4">
                                    <form method="POST" action="{{ route('admin.wallet.destroy', $transaction->id) }}" onsubmit="return confirm('Are you sure you want to DELETE this transaction? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <label class="form-label fw-bold">Delete Transaction</label>
                                        <button type="submit" class="btn btn-dark w-100">
                                            <i class="feather-trash-2 me-2"></i> Delete
                                        </button>
                                    </form>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Transaction Logs -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Transaction Activity Logs</h5>
                        </div>
                        <div class="card-body p-0">
                            @forelse($logs as $log)
                                <div class="border-bottom p-3 hover-bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-soft-primary text-primary me-2">
                                                {{ ucfirst($log->type) }}
                                            </span>
                                            @if($log->status == 'success')
                                                <span class="badge bg-soft-success text-success">Success</span>
                                            @elseif($log->status == 'failed')
                                                <span class="badge bg-soft-danger text-danger">Failed</span>
                                            @elseif($log->status == 'cancelled')
                                                <span class="badge bg-soft-dark text-dark">Cancelled</span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning">{{ ucfirst($log->status) }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small>
                                    </div>

                                    <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Reference</small>
                                            <code class="fs-11">{{ $log->reference ?? 'N/A' }}</code>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Method</small>
                                            <span class="badge bg-soft-info text-info">{{ $log->method ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Amount</small>
                                            <strong>₦{{ number_format($log->amount ?? 0, 2) }}</strong>
                                        </div>
                                    </div>

                                    @if($log->description)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Description</small>
                                        <p class="fs-12 mb-0">{{ $log->description }}</p>
                                    </div>
                                    @endif

                                    @if($log->error_message)
                                    <div class="mb-2">
                                        <small class="text-danger d-block">Error Message</small>
                                        <p class="fs-12 text-danger mb-0">{{ $log->error_message }}</p>
                                    </div>
                                    @endif

                                    @if($log->ip_address)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">IP Address</small>
                                        <code class="fs-11">{{ $log->ip_address }}</code>
                                    </div>
                                    @endif

                                    <!-- Expandable JSON Data -->
                                    @if($log->request_data || $log->response_data)
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#logData{{ $log->id }}">
                                            <i class="feather-code me-1"></i> View Technical Data
                                        </button>
                                        
                                        <div class="collapse mt-2" id="logData{{ $log->id }}">
                                            @if($log->request_data)
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1"><strong>Request Data:</strong></small>
                                                <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto; font-size: 11px;">{{ json_encode($log->request_data, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            @endif

                                            @if($log->response_data)
                                            <div>
                                                <small class="text-muted d-block mb-1"><strong>Response Data:</strong></small>
                                                <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto; font-size: 11px;">{{ json_encode($log->response_data, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="feather-inbox fs-3 d-block mb-2"></i>
                                    <p class="mb-0">No activity logs found</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Logs Pagination -->
                        @if($logs->hasPages())
                        <div class="card-footer">
                            {{ $logs->links() }}
                        </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>

    </div>
</main>

<!-- Reject Transaction Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.wallet.reject', $transaction->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="feather-alert-triangle me-2"></i>
                        Are you sure you want to reject this transaction? The customer's balance will be reversed if this is a credit transaction.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-muted">(Optional)</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-x-circle me-2"></i> Reject Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.components.footer')