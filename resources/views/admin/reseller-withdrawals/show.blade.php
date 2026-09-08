@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Withdrawal — {{ $withdrawal->reference }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.reseller-withdrawals.index') }}">Reseller Withdrawals</a></li>
            <li class="breadcrumb-item">{{ $withdrawal->reference }}</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h6 class="m-0">Withdrawal Details</h6></div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><th class="text-muted">Reseller</th><td>{{ $withdrawal->reseller->panel_name }}</td></tr>
                        <tr><th class="text-muted">Owner</th><td>{{ $withdrawal->reseller->owner->name }} ({{ $withdrawal->reseller->owner->email }})</td></tr>
                        <tr><th class="text-muted">Amount</th><td class="fw-bold">₦{{ number_format($withdrawal->amount, 2) }}</td></tr>
                        <tr><th class="text-muted">Bank Name</th><td>{{ $withdrawal->bank_name }}</td></tr>
                        <tr><th class="text-muted">Account Number</th><td>{{ $withdrawal->account_number }}</td></tr>
                        <tr><th class="text-muted">Account Name</th><td>{{ $withdrawal->account_name }}</td></tr>
                        <tr><th class="text-muted">Reference</th><td>{{ $withdrawal->reference }}</td></tr>
                        <tr><th class="text-muted">Status</th><td>{{ ucfirst($withdrawal->status) }}</td></tr>
                        @if($withdrawal->failure_reason)
                            <tr><th class="text-muted">Failure Reason</th><td class="text-danger">{{ $withdrawal->failure_reason }}</td></tr>
                        @endif
                        <tr><th class="text-muted">Requested</th><td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

    @if(in_array($withdrawal->status, ['pending', 'processing']) && auth('admin')->user()->canManageResellerWithdrawals())
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h6 class="m-0">Take Action</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.reseller-withdrawals.approve', $withdrawal) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Confirm you have sent ₦{{ number_format($withdrawal->amount, 2) }} to {{ $withdrawal->account_name }}?')">
                            <i class="feather-check me-1"></i> Mark as Paid
                        </button>
                    </form>
                     <form action="{{ route('admin.reseller-withdrawals.reject', $withdrawal) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Reason for rejection</label>
                            <textarea name="failure_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="feather-x me-1"></i> Reject & Refund
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
</main>

@include('admin.components.footer')