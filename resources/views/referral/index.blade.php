@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Referral Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Referral</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('referral.withdraw') }}" class="btn btn-primary">
                    <i class="feather-credit-card me-2"></i>
                    Withdraw Earnings
                </a>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert.type') }} alert-dismissible fade show">
                <i class="feather-{{ session('alert.type') == 'success' ? 'check-circle' : 'alert-circle' }} me-2"></i>
                {{ session('alert.message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            <div class="row">
                
                <!-- Referral Balance Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="avatar-text avatar-lg bg-success-subtle">
                                    <i class="feather-dollar-sign text-success"></i>
                                </div>
                            </div>
                            <div>
                                <h2 class="fw-bold mb-2">₦{{ number_format($referral->referral_balance, 2) }}</h2>
                                <p class="fs-12 text-muted mb-0">Referral Balance</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Referred -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="avatar-text avatar-lg bg-primary-subtle">
                                    <i class="feather-users text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h2 class="fw-bold mb-2">{{ $totalReferred }}</h2>
                                <p class="fs-12 text-muted mb-0">Total Referred Users</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Who Deposited -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="avatar-text avatar-lg bg-warning-subtle">
                                    <i class="feather-credit-card text-warning"></i>
                                </div>
                            </div>
                            <div>
                                <h2 class="fw-bold mb-2">{{ $depositedCount }}</h2>
                                <p class="fs-12 text-muted mb-0">Users Who Deposited</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bonuses Earned -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="avatar-text avatar-lg bg-info-subtle">
                                    <i class="feather-gift text-info"></i>
                                </div>
                            </div>
                            <div>
                                <h2 class="fw-bold mb-2">{{ $bonusPaidCount }}</h2>
                                <p class="fs-12 text-muted mb-0">Bonuses Earned (₦100 each)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral Link Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Your Referral Link</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-4">
                                <h6 class="fw-bold mb-2">
                                    <i class="feather-info me-2"></i>
                                    How Referral Works:
                                </h6>
                                <ul class="mb-0">
                                    <li>Share your unique referral link with friends</li>
                                    <li>When they register using your link and make their first deposit</li>
                                    <li>Then place their first order</li>
                                    <li><strong>You earn ₦100 bonus!</strong> (One-time per user)</li>
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Your Referral Code:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $referral->referral_code }}" id="referralCode" readonly>
                                        <button class="btn btn-primary" onclick="copyCode()">
                                            <i class="feather-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Your Referral Link:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ url('/register?ref=' . $referral->referral_code) }}" id="referralLink" readonly>
                                        <button class="btn btn-primary" onclick="copyLink()">
                                            <i class="feather-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-success" onclick="shareWhatsApp()">
                                    <i class="feather-message-circle me-2"></i> Share on WhatsApp
                                </button>
                                <button class="btn btn-info" onclick="shareTwitter()">
                                    <i class="feather-twitter me-2"></i> Share on Twitter
                                </button>
                                <button class="btn btn-primary" onclick="shareFacebook()">
                                    <i class="feather-facebook me-2"></i> Share on Facebook
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referred Users Table -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Referred Users</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>User</th>
                                            <th>Email</th>
                                            <th>Deposited</th>
                                            <th>Placed Order</th>
                                            <th>Bonus Status</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($referredUsers as $referred)
                                            <tr>
                                                <td>{{ $referred->referredUser->name }}</td>
                                                <td>{{ $referred->referredUser->email }}</td>
                                                <td>
                                                    @if($referred->has_deposited)
                                                        <span class="badge bg-soft-success text-success">
                                                            <i class="feather-check me-1"></i> Yes
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">
                                                            <i class="feather-x me-1"></i> No
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($referred->has_ordered)
                                                        <span class="badge bg-soft-success text-success">
                                                            <i class="feather-check me-1"></i> Yes
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">
                                                            <i class="feather-x me-1"></i> No
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($referred->bonus_paid)
                                                        <span class="badge bg-soft-success text-success">
                                                            <i class="feather-gift me-1"></i> ₦100 Paid
                                                        </span>
                                                    @else
                                                        <span class="badge bg-soft-warning text-warning">
                                                            <i class="feather-clock me-1"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $referred->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    No referrals yet. Start sharing your link!
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($referredUsers->hasPages())
                        <div class="card-footer">
                            {{ $referredUsers->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Transaction History</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $transaction)
                                            <tr>
                                                <td><code>{{ $transaction->reference }}</code></td>
                                                <td>
                                                    @if($transaction->type == 'credit')
                                                        <span class="badge bg-soft-success text-success">Credit</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">Debit</span>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">₦{{ number_format($transaction->amount, 2) }}</td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>
                                                    @if($transaction->status == 'success')
                                                        <span class="badge bg-soft-success text-success">Success</span>
                                                    @elseif($transaction->status == 'pending')
                                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                                    @elseif($transaction->status == 'approved')
                                                        <span class="badge bg-soft-info text-info">Approved</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">{{ ucfirst($transaction->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">
                                                    No transactions yet
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($transactions->hasPages())
                        <div class="card-footer">
                            {{ $transactions->links() }}
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
function copyCode() {
    const input = document.getElementById('referralCode');
    input.select();
    document.execCommand('copy');
    alert('Referral code copied to clipboard!');
}

function copyLink() {
    const input = document.getElementById('referralLink');
    input.select();
    document.execCommand('copy');
    alert('Referral link copied to clipboard!');
}

function shareWhatsApp() {
    const text = `Join {{ config('app.name', 'ProxWorld') }} for fast, reliable proxies! Use my referral code: {{ $referral->referral_code }}`;
    const url = `{{ url('/register?ref=' . $referral->referral_code) }}`;
    window.open(`https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`, '_blank');
}

function shareTwitter() {
    const text = `Join {{ config('app.name', 'ProxWorld') }} for fast, reliable proxies! Use my referral code: {{ $referral->referral_code }}`;
    const url = `{{ url('/register?ref=' . $referral->referral_code) }}`;
    window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`, '_blank');
}

function shareFacebook() {
    const url = `{{ url('/register?ref=' . $referral->referral_code) }}`;
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
}
</script>

@include('components.g-footer')