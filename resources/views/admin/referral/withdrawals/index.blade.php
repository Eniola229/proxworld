@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')


<main class="nxl-container">
    <div class="nxl-content">

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Referral Withdrawals</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Referral Withdrawals</li>
        </ul>
    </div>
</div>

@if(session('alert'))
    @php
        $alertType = session('alert.type');
        $bgClass   = match($alertType) {
            'success' => 'success',
            'error'   => 'danger',
            'warning' => 'warning',
            default   => 'info',
        };
        $icon = match($alertType) {
            'success' => 'check-circle',
            'error'   => 'x-circle',
            'warning' => 'alert-triangle',
            default   => 'info',
        };
    @endphp
<div class="main-content">
    <div class="alert alert-{{ $bgClass }} alert-dismissible fade show">
        <i class="feather-{{ $icon }} me-2"></i>
        {{ session('alert.message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif  

<!-- Statistics Cards -->
<div class="row mb-4 mt-3">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $stats['pending'] }}</h3>
                        <p class="text-muted mb-0">Pending</p>
                    </div>
                    <div class="avatar-text avatar-lg bg-warning-subtle">
                        <i class="feather-clock text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $stats['approved'] }}</h3>
                        <p class="text-muted mb-0">Approved</p>
                    </div>
                    <div class="avatar-text avatar-lg bg-info-subtle">
                        <i class="feather-check text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $stats['success'] }}</h3>
                        <p class="text-muted mb-0">Completed</p>
                    </div>
                    <div class="avatar-text avatar-lg bg-success-subtle">
                        <i class="feather-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="fw-bold mb-1">{{ $stats['failed'] }}</h3>
                        <p class="text-muted mb-0">Failed/Rejected</p>
                    </div>
                    <div class="avatar-text avatar-lg bg-danger-subtle">
                        <i class="feather-x-circle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.referral.withdrawals.index') }}" class="row g-3 align-items-end" id="filterForm">
            <!-- Preserve active status tab -->
            <input type="hidden" name="status" value="{{ $status }}">

            <!-- Search Input -->
            <div class="col-md-4">
                <label class="form-label fw-semibold text-muted small mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="feather-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control border-start-0"
                           placeholder="Name, email, reference..."
                           value="{{ $search ?? '' }}">
                </div>
            </div>

            <!-- Withdrawal Method Filter -->
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small mb-1">Method</label>
                <select name="method" class="form-select">
                    <option value="" {{ empty($filterMethod) ? 'selected' : '' }}>All Methods</option>
                    <option value="bank" {{ ($filterMethod ?? '') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="wallet" {{ ($filterMethod ?? '') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                </select>
            </div>

            <!-- Amount Range -->
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small mb-1">Min Amount (₦)</label>
                <input type="number"
                       name="amount_min"
                       class="form-control"
                       placeholder="0"
                       value="{{ $amountMin ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small mb-1">Max Amount (₦)</label>
                <input type="number"
                       name="amount_max"
                       class="form-control"
                       placeholder="e.g. 50000"
                       value="{{ $amountMax ?? '' }}">
            </div>

            <!-- Date Range -->
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small mb-1">From Date</label>
                <input type="date"
                       name="date_from"
                       class="form-control"
                       value="{{ $dateFrom ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold text-muted small mb-1">To Date</label>
                <input type="date"
                       name="date_to"
                       class="form-control"
                       value="{{ $dateTo ?? '' }}">
            </div>

            <!-- Buttons -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="feather-search me-1"></i> Search
                </button>
                <a href="{{ route('admin.referral.withdrawals.index') }}?status={{ $status }}"
                   class="btn btn-outline-secondary" title="Clear Filters">
                    <i class="feather-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Filter Tabs -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}"
                   href="?status=pending&search={{ $search ?? '' }}&method={{ $filterMethod ?? '' }}&amount_min={{ $amountMin ?? '' }}&amount_max={{ $amountMax ?? '' }}&date_from={{ $dateFrom ?? '' }}&date_to={{ $dateTo ?? '' }}">
                    Pending ({{ $stats['pending'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'approved' ? 'active' : '' }}"
                   href="?status=approved&search={{ $search ?? '' }}&method={{ $filterMethod ?? '' }}&amount_min={{ $amountMin ?? '' }}&amount_max={{ $amountMax ?? '' }}&date_from={{ $dateFrom ?? '' }}&date_to={{ $dateTo ?? '' }}">
                    Approved ({{ $stats['approved'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'success' ? 'active' : '' }}"
                   href="?status=success&search={{ $search ?? '' }}&method={{ $filterMethod ?? '' }}&amount_min={{ $amountMin ?? '' }}&amount_max={{ $amountMax ?? '' }}&date_from={{ $dateFrom ?? '' }}&date_to={{ $dateTo ?? '' }}">
                    Completed ({{ $stats['success'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'failed' ? 'active' : '' }}"
                   href="?status=failed&search={{ $search ?? '' }}&method={{ $filterMethod ?? '' }}&amount_min={{ $amountMin ?? '' }}&amount_max={{ $amountMax ?? '' }}&date_from={{ $dateFrom ?? '' }}&date_to={{ $dateTo ?? '' }}">
                    Failed ({{ $stats['failed'] }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}"
                   href="?status=all&search={{ $search ?? '' }}&method={{ $filterMethod ?? '' }}&amount_min={{ $amountMin ?? '' }}&amount_max={{ $amountMax ?? '' }}&date_from={{ $dateFrom ?? '' }}&date_to={{ $dateTo ?? '' }}">
                    All
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr class="border-b">
                        <th>Reference</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Bank Details</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <code>{{ $withdrawal->reference }}</code>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $withdrawal->referral->user->name }}</div>
                                    <small class="text-muted">{{ $withdrawal->referral->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                @if($withdrawal->withdrawal_method == 'bank')
                                    <span class="badge bg-soft-primary text-primary">
                                        <i class="feather-briefcase me-1"></i> Bank Transfer
                                    </span>
                                @else
                                    <span class="badge bg-soft-info text-info">
                                        <i class="feather-credit-card me-1"></i> Wallet
                                    </span>
                                @endif
                            </td>
                            <td class="fw-bold">₦{{ number_format($withdrawal->amount, 2) }}</td>
                            <td>
                                @if($withdrawal->bank_name)
                                    <div class="small">
                                        <div><strong>{{ $withdrawal->bank_name }}</strong></div>
                                        <div>{{ $withdrawal->account_number }}</div>
                                        <div>{{ $withdrawal->account_name }}</div>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($withdrawal->status == 'success')
                                    <span class="badge bg-soft-success text-success">
                                        <i class="feather-check-circle me-1"></i> Success
                                    </span>
                                @elseif($withdrawal->status == 'pending')
                                    <span class="badge bg-soft-warning text-warning">
                                        <i class="feather-clock me-1"></i> Pending
                                    </span>
                                @elseif($withdrawal->status == 'approved')
                                    <span class="badge bg-soft-info text-info">
                                        <i class="feather-check me-1"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-soft-danger text-danger">
                                        <i class="feather-x-circle me-1"></i> {{ ucfirst($withdrawal->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($withdrawal->status == 'pending')
                                    @if(Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->isAccountant())
                                        <div class="d-flex gap-1">
                                            @if($withdrawal->withdrawal_method == 'wallet')
                                                <form action="{{ route('admin.referral.withdrawals.approve-wallet', $withdrawal->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Approve this wallet withdrawal?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve to Wallet">
                                                        <i class="feather-check"></i> Approve
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.referral.withdrawals.approve-bank', $withdrawal->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Initiate bank transfer for this withdrawal?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Process Bank Transfer">
                                                        <i class="feather-send"></i> Process
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#rejectForm{{ $withdrawal->id }}"
                                                    title="Reject Withdrawal">
                                                <i class="feather-x"></i> Reject
                                            </button>
                                        </div>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">
                                            <i class="feather-lock me-1"></i> No Permission
                                        </span>
                                    @endif
                                @else
                                    <a href="{{ route('admin.referral.withdrawals.show', $withdrawal->id) }}" class="btn btn-sm btn-info">
                                        <i class="feather-eye"></i> View
                                    </a>
                                @endif
                            </td>
                        </tr>
                        
                        <!-- Reject Form Dropdown Row -->
                        @if($withdrawal->status == 'pending' && (Auth::guard('admin')->user()->isSuperAdmin() || Auth::guard('admin')->user()->isAccountant()))
                        <tr class="collapse" id="rejectForm{{ $withdrawal->id }}">
                            <td colspan="8" class="bg-light border-top-0 p-3">
                                <div class="card mb-0 shadow-sm">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">
                                            <i class="feather-alert-triangle me-2"></i>Reject Withdrawal - {{ $withdrawal->reference }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.referral.withdrawals.reject', $withdrawal->id) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="alert alert-warning mb-3">
                                                        <strong>User:</strong> {{ $withdrawal->referral->user->name }}<br>
                                                        <strong>Email:</strong> {{ $withdrawal->referral->user->email }}<br>
                                                        <strong>Amount:</strong> ₦{{ number_format($withdrawal->amount, 2) }}<br>
                                                        <small class="d-block mt-2">
                                                            <i class="feather-info me-1"></i>
                                                            The amount will be refunded to the user's referral balance.
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Reason for Rejection *</label>
                                                        <textarea name="reason" 
                                                                  class="form-control" 
                                                                  rows="4" 
                                                                  placeholder="Enter reason for rejection..." 
                                                                  required></textarea>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" 
                                                                class="btn btn-secondary" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#rejectForm{{ $withdrawal->id }}">
                                                            <i class="feather-x me-1"></i> Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="feather-check me-1"></i> Confirm Rejection
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No withdrawal requests found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($withdrawals->hasPages())
        <div class="card-footer">
            {{ $withdrawals->appends([
                'status'      => $status,
                'search'      => $search ?? '',
                'method'      => $filterMethod ?? '',
                'amount_min'  => $amountMin ?? '',
                'amount_max'  => $amountMax ?? '',
                'date_from'   => $dateFrom ?? '',
                'date_to'     => $dateTo ?? '',
            ])->links() }}
        </div>
    @endif
</div>
        </div>
        </div>
        </div>

    </main>
@include('admin.components.footer')