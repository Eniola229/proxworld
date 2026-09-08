@include('components.g-header')
@include('components.nav')
<style>
.bank-select-wrapper {
    position: relative;
}

.bank-options-list {
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    display: none;
    position: absolute;
    background: white;
    width: 100%;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.bank-option {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.bank-option:hover {
    background-color: #f8f9fa;
}

.bank-option:last-child {
    border-bottom: none;
}
</style>
<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Withdraw Referral Earnings</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('referral.index') }}">Referral</a></li>
                    <li class="breadcrumb-item">Withdraw</li>
                </ul>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert.type') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                <i class="feather-{{ session('alert.type') == 'success' ? 'check-circle' : 'alert-circle' }} me-2"></i>
                {{ session('alert.message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            <div class="row justify-content-center">

                <!-- Balance Overview -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="avatar-text avatar-lg bg-success-subtle mx-auto mb-3">
                                <i class="feather-dollar-sign text-success" style="font-size: 2rem;"></i>
                            </div>
                            <h3 class="fw-bold mb-2">₦{{ number_format($referral->referral_balance, 2) }}</h3>
                            <p class="text-muted mb-0">Available Referral Balance</p>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal Options -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Select Withdrawal Method</h5>
                        </div>
                        <div class="card-body">

                            <ul class="nav nav-tabs mb-4" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#wallet-tab" type="button">
                                        <i class="feather-credit-card me-2"></i>
                                        To Wallet
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank-tab" type="button">
                                        <i class="feather-briefcase me-2"></i>
                                        To Bank Account
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">

                                <!-- Withdraw to Wallet Tab -->
                                <div class="tab-pane fade show active" id="wallet-tab">
                                    <div class="alert alert-info mb-4">
                                        <i class="feather-info me-2"></i>
                                        <strong>Wallet Withdrawal:</strong> Minimum withdrawal is ₦1000. Funds will be added to your main wallet after admin approval.
                                    </div>

                                    <form action="{{ route('referral.withdraw.wallet') }}" method="POST" id="walletWithdrawalForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Amount to Withdraw</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₦</span>
                                                <input type="number" name="amount" class="form-control"
                                                       placeholder="Enter amount" min="1000" max="{{ $referral->referral_balance }}"
                                                       step="0.01" required>
                                            </div>
                                            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                                            <small class="text-muted">Minimum: ₦1000 | Available: ₦{{ number_format($referral->referral_balance, 2) }}</small>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="feather-alert-triangle me-2"></i>
                                            <strong>Important:</strong> This withdrawal requires admin approval before your wallet is credited.
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="feather-send me-2"></i> Submit Wallet Withdrawal Request
                                        </button>
                                    </form>

                                </div>

                                <!-- Withdraw to Bank Tab -->
                                <div class="tab-pane fade" id="bank-tab">
                                    <div class="alert alert-info mb-4">
                                        <i class="feather-info me-2"></i>
                                        <strong>Bank Withdrawal:</strong> Minimum withdrawal is ₦1000. Please ensure your account details are correct.
                                    </div>

                                    <form action="{{ route('referral.withdraw.bank') }}" method="POST" id="bankWithdrawalForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Amount to Withdraw</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₦</span>
                                                <input type="number" name="amount" class="form-control"
                                                       placeholder="Enter amount"
                                                       min="1000"
                                                       max="{{ $referral->referral_balance }}"
                                                       step="0.01"
                                                       required>
                                            </div>
                                            @error('amount')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">Minimum: ₦1000 | Available: ₦{{ number_format($referral->referral_balance, 2) }}</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Select Bank</label>
                                            <div class="bank-select-wrapper">
                                                <input type="hidden" name="bank_name" id="bankNameInput" required>
                                                <input type="hidden" name="bank_code" id="bankCodeInput" required>

                                                <input type="text"
                                                       class="form-control"
                                                       id="bankSearchInput"
                                                       placeholder="Type to search for your bank..."
                                                       autocomplete="off">

                                                <div class="bank-options-list" id="bankOptionsList">
                                                    @foreach($banks as $bank)
                                                        <div class="bank-option"
                                                             data-bank-name="{{ $bank['name'] }}"
                                                             data-bank-code="{{ $bank['code'] }}">
                                                            {{ $bank['name'] }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @error('bank_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">Start typing to search for your bank</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Account Number</label>
                                            <input type="text" name="account_number" class="form-control"
                                                   placeholder="Enter 10-digit account number"
                                                   minlength="10"
                                                   maxlength="10"
                                                   pattern="[0-9]{10}"
                                                   id="accountNumber"
                                                   required>
                                            @error('account_number')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">Enter your 10-digit account number</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Account Name</label>
                                            <input type="text" name="account_name" class="form-control" id="accountName" readonly required
                                                   placeholder="Auto-filled once account resolves">
                                            <div id="resolveStatus" class="small mt-1"></div>
                                            @error('account_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="feather-alert-triangle me-2"></i>
                                            <strong>Important:</strong> This withdrawal requires admin approval. Please double-check your bank details before submitting.
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100" id="bankSubmitBtn" disabled>
                                            <i class="feather-send me-2"></i>
                                            Submit Bank Withdrawal Request
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Withdrawal Requests -->
                <div class="col-lg-8 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Withdrawal Requests</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentWithdrawals as $withdrawal)
                                            <tr>
                                                <td><code>{{ $withdrawal->reference }}</code></td>
                                                <td>
                                                    @if($withdrawal->method === 'bank')
                                                        <span class="badge bg-soft-primary text-primary">Bank</span>
                                                    @else
                                                        <span class="badge bg-soft-info text-info">Wallet</span>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">₦{{ number_format($withdrawal->amount, 2) }}</td>
                                                <td>
                                                    @if($withdrawal->status == 'success')
                                                        <span class="badge bg-soft-success text-success">Success</span>
                                                    @elseif($withdrawal->status == 'pending')
                                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                                    @elseif($withdrawal->status == 'approved')
                                                        <span class="badge bg-soft-info text-info">Approved</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">{{ ucfirst($withdrawal->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    No withdrawal requests yet
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('bankSearchInput');
    const nameInput = document.getElementById('bankNameInput');
    const codeInput = document.getElementById('bankCodeInput');
    const dropdown = document.getElementById('bankOptionsList');
    const options = dropdown.querySelectorAll('.bank-option');
    const accountNumberInput = document.getElementById('accountNumber');
    const accountNameInput = document.getElementById('accountName');
    const resolveStatus = document.getElementById('resolveStatus');
    const submitBtn = document.getElementById('bankSubmitBtn');

    searchInput.addEventListener('focus', () => dropdown.style.display = 'block');

    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        nameInput.value = codeInput.value = accountNameInput.value = '';
        submitBtn.disabled = true;
        let any = false;
        options.forEach(o => {
            const match = o.dataset.bankName.toLowerCase().includes(q);
            o.style.display = match ? 'block' : 'none';
            if (match) any = true;
        });
        dropdown.style.display = any ? 'block' : 'none';
    });

    options.forEach(o => {
        o.addEventListener('click', () => {
            searchInput.value = o.dataset.bankName;
            nameInput.value = o.dataset.bankName;
            codeInput.value = o.dataset.bankCode;
            dropdown.style.display = 'none';
            tryResolve();
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.bank-select-wrapper')) dropdown.style.display = 'none';
    });

    accountNumberInput.addEventListener('input', () => {
        accountNumberInput.value = accountNumberInput.value.replace(/[^0-9]/g, '');
        accountNameInput.value = '';
        submitBtn.disabled = true;
        tryResolve();
    });

    function tryResolve() {
        if (!nameInput.value || accountNumberInput.value.length !== 10) {
            resolveStatus.textContent = '';
            return;
        }

        resolveStatus.textContent = 'Resolving account name...';
        resolveStatus.className = 'small mt-1 text-muted';
        submitBtn.disabled = true;

        fetch('{{ route("referral.withdraw.resolve-account") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ bank_name: nameInput.value, account_number: accountNumberInput.value }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.account_name) {
                accountNameInput.value = data.account_name;
                resolveStatus.textContent = 'Verified: ' + data.account_name;
                resolveStatus.className = 'small mt-1 text-success';
                submitBtn.disabled = false;
            } else {
                resolveStatus.textContent = data.message || 'Could not resolve account name.';
                resolveStatus.className = 'small mt-1 text-danger';
            }
        })
        .catch(() => {
            resolveStatus.textContent = 'Error resolving account. Try again.';
            resolveStatus.className = 'small mt-1 text-danger';
        });
    }

    // Bank form confirmation before submit
    document.getElementById('bankWithdrawalForm')?.addEventListener('submit', function(e) {
        const accountNumber = accountNumberInput.value;
        const accountName = accountNameInput.value;
        const bankName = nameInput.value;

        if (accountNumber.length !== 10) {
            e.preventDefault();
            alert('Account number must be exactly 10 digits');
            return false;
        }

        if (!bankName) {
            e.preventDefault();
            alert('Please select a bank');
            return false;
        }

        if (!accountName || accountName.trim().length < 3) {
            e.preventDefault();
            alert('Account name has not been verified yet');
            return false;
        }

        return confirm('Please confirm your bank details are correct:\n\nBank: ' + bankName + '\nAccount Number: ' + accountNumber + '\nAccount Name: ' + accountName);
    });

    // Wallet form confirmation before submit
    document.getElementById('walletWithdrawalForm')?.addEventListener('submit', function(e) {
        return confirm('Submit this amount for withdrawal to your wallet? This requires admin approval.');
    });
});
</script>
@include('components.g-footer')