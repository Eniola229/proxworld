@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Withdrawal Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.referral.withdrawals.index') }}">Referral Withdrawals</a></li>
                    <li class="breadcrumb-item">{{ $withdrawal->reference }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.customers.show', $withdrawal->user_id) }}" class="btn btn-sm btn-light-brand">
                    <i class="feather-user me-2"></i> View Customer
                </a>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert.type') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                <i class="feather-{{ session('alert.type') == 'success' ? 'check-circle' : 'alert-circle' }} me-2"></i>
                {{ session('alert.message') }}
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
                            <h5 class="card-title">Withdrawal Information</h5>
                            <div>
                                @if($withdrawal->status == 'success')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($withdrawal->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($withdrawal->status == 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($withdrawal->status) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Reference:</span>
                                    <code class="fs-11">{{ $withdrawal->reference }}</code>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Type:</span>
                                    @if($withdrawal->method == 'bank')
                                        <span class="badge bg-soft-primary text-primary">
                                            <i class="feather-briefcase me-1"></i> Bank Transfer
                                        </span>
                                    @else
                                        <span class="badge bg-soft-info text-info">
                                            <i class="feather-credit-card me-1"></i> Wallet
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Amount:</span>
                                    <span class="fs-4 fw-bold text-danger">
                                        -₦{{ number_format($withdrawal->amount, 2) }}
                                    </span>
                                </div>
                            </div>

                            @if($withdrawal->bank_name)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="mb-2">
                                    <span class="fs-12 text-muted d-block mb-2">Bank Details:</span>
                                </div>
                                <div class="ps-2">
                                    <div class="mb-1">
                                        <small class="text-muted">Bank:</small>
                                        <strong class="ms-2">{{ $withdrawal->bank_name }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Account:</small>
                                        <strong class="ms-2">{{ $withdrawal->account_number }}</strong>
                                    </div>
                                    <div>
                                        <small class="text-muted">Name:</small>
                                        <strong class="ms-2">{{ $withdrawal->account_name }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(! is_null($withdrawal->balance_before))
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Balance Before:</span>
                                    <span class="fs-12 fw-bold">₦{{ number_format($withdrawal->balance_before, 2) }}</span>
                                </div>
                            </div>
                            @endif

                            @if(! is_null($withdrawal->balance_after))
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Balance After:</span>
                                    <span class="fs-12 fw-bold text-success">₦{{ number_format($withdrawal->balance_after, 2) }}</span>
                                </div>
                            </div>
                            @endif

                            @if($withdrawal->description)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Description:</span>
                                </div>
                                <p class="fs-12 text-muted mb-0">{{ $withdrawal->description }}</p>
                            </div>
                            @endif

                            @if($withdrawal->failure_reason && $withdrawal->status == 'failed')
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-danger">Failure Reason:</span>
                                </div>
                                <p class="fs-12 text-danger mb-0">{{ $withdrawal->failure_reason }}</p>
                            </div>
                            @endif

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Created:</span>
                                    <span class="fs-12 fw-bold">{{ $withdrawal->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted">Last Updated:</span>
                                    <span class="fs-12 fw-bold">{{ $withdrawal->updated_at->diffForHumans() }}</span>
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
                            @if($withdrawal->user)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary me-3">
                                    {{ substr($withdrawal->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $withdrawal->user->name }}</h6>
                                    <p class="fs-12 text-muted mb-0">{{ $withdrawal->user->email }}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Referral Code:</span>
                                <code class="fs-11">{{ $withdrawal->user->referral_code }}</code>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Current Referral Balance:</span>
                                <span class="fs-12 fw-bold text-success">₦{{ number_format($referralBalance, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Total Referral Earnings:</span>
                                <span class="fs-12 fw-bold">₦{{ number_format($withdrawal->user->referrals()->sum('bonus_amount'), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Total Withdrawals:</span>
                                <span class="fs-12 fw-bold">{{ number_format($totalTransactions) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fs-12 text-muted">Member Since:</span>
                                <span class="fs-12 fw-bold">{{ $withdrawal->user->created_at->format('M d, Y') }}</span>
                            </div>
                            @else
                            <p class="text-muted mb-0">This user account no longer exists.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions & Logs -->
                <div class="col-xxl-8 col-xl-6">
                    
                    <!-- Admin Actions (Super Admin & Accountant Only) -->
                    @if($withdrawal->status == 'pending' && (Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->isAccountant()))
                    <div class="card mb-3">
                        <div class="card-header bg-soft-warning">
                            <h5 class="card-title text-warning">
                                <i class="feather-shield me-2"></i> Admin Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                
                                <!-- Approve Transaction -->
                                @if($withdrawal->method == 'wallet')
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.referral.withdrawals.approve-wallet', $withdrawal->id) }}" onsubmit="return confirm('Are you sure you want to approve this wallet withdrawal? ₦{{ number_format($withdrawal->amount, 2) }} will be credited to customer wallet.')">
                                        @csrf
                                        <label class="form-label fw-bold">Approve to Wallet</label>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="feather-check-circle me-2"></i> Approve Wallet
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.referral.withdrawals.approve-bank', $withdrawal->id) }}" onsubmit="return confirm('This will initiate a live Flutterwave transfer of ₦{{ number_format($withdrawal->amount, 2) }} to {{ $withdrawal->account_name ?? 'customer' }}. Continue?')">
                                        @csrf
                                        <label class="form-label fw-bold">Process Bank Transfer</label>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="feather-send me-2"></i> Approve & Send via Flutterwave
                                        </button>
                                    </form>
                                </div>
                                @endif

                                <!-- Reject Transaction -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Reject Withdrawal</label>
                                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="feather-x-circle me-2"></i> Reject
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    @elseif($withdrawal->status == 'processing')
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Admin Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <i class="feather-clock me-2"></i>
                                Transfer already initiated with Flutterwave. Waiting for the webhook to confirm final status — no further action needed here.
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Activity Logs -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Activity Logs</h5>
                            <small class="text-muted">Admin actions taken on this withdrawal</small>
                        </div>
                        <div class="card-body p-0">
                            @forelse($logs as $log)
                                <div class="border-bottom p-3 hover-bg-light" style="background-color: #fef3c7;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="badge bg-warning text-dark me-2">
                                                <i class="feather-shield me-1"></i> ADMIN ACTION
                                            </span>
                                            <span class="badge bg-soft-primary text-primary">
                                                {{ ucfirst($log->action) }}
                                            </span>
                                            @if($log->admin)
                                                <small class="text-muted ms-2">by <strong>{{ $log->admin->name }}</strong></small>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small>
                                    </div>

                                    @if($log->description)
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Description</small>
                                        <p class="fs-12 mb-0">{{ $log->description }}</p>
                                    </div>
                                    @endif

                                    <div class="row g-2 mb-2">
                                        @if($log->ip_address)
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">IP Address</small>
                                            <code class="fs-11">{{ $log->ip_address }}</code>
                                        </div>
                                        @endif
                                        @if($log->user_agent)
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">User Agent</small>
                                            <small class="fs-11 text-truncate d-block" title="{{ $log->user_agent }}">
                                                {{ Str::limit($log->user_agent, 50) }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Expandable Changes Data -->
                                    @if($log->changes)
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="collapse" data-bs-target="#logData{{ $log->id }}">
                                            <i class="feather-code me-1"></i> View Changes
                                        </button>
                                        
                                        <div class="collapse mt-2" id="logData{{ $log->id }}">
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1"><strong>Changes Made:</strong></small>
                                                <pre class="bg-light p-2 rounded" style="max-height: 200px; overflow-y: auto; font-size: 11px;">{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
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
                    </div>

                </div>

            </div>
        </div>

    </div>
</main>

<!-- Reject Withdrawal Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.referral.withdrawals.reject', $withdrawal->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Withdrawal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="feather-alert-triangle me-2"></i>
                        Are you sure you want to reject this withdrawal? The amount will be refunded to the user's referral balance.
                    </div>
                    
                    <div class="alert alert-info mb-3">
                        <strong>User:</strong> {{ $withdrawal->user->name ?? 'Deleted User' }}<br>
                        <strong>Amount:</strong> ₦{{ number_format($withdrawal->amount, 2) }}<br>
                        <strong>Reference:</strong> {{ $withdrawal->reference }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection *</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                        <small class="text-muted">This reason will be saved and visible to other admins.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather-x-circle me-2"></i> Reject Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.components.footer')