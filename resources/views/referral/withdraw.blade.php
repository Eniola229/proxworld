@include('components.g-header')
@include('components.nav')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Enhanced select with search */
.bank-select-wrapper {
    position: relative;
}

.bank-search-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
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

.selected-bank-display {
    padding: 0.5rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    background: white;
    cursor: pointer;
    min-height: 38px;
    display: flex;
    align-items: center;
}

.selected-bank-display:hover {
    border-color: #86b7fe;
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
            <div class="alert alert-{{ session('alert.type') }} alert-dismissible fade show">
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

                                    <form action="{{ route('referral.withdraw.wallet') }}" method="POST">
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

                                        <div class="alert alert-warning">
                                            <i class="feather-alert-triangle me-2"></i>
                                            This withdrawal requires admin approval and may take 1-2 hours to process.
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="feather-send me-2"></i>
                                            Submit Withdrawal Request
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
                                                       min="100" 
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
                                                <!-- Hidden input that will store the actual bank name -->
                                                <input type="hidden" name="bank_name" id="bankNameInput" required>
                                                
                                                <!-- Search input -->
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="bankSearchInput" 
                                                       placeholder="Type to search for your bank..."
                                                       autocomplete="off">
                                                
                                                <!-- Banks dropdown list -->
                                                <div class="bank-options-list" id="bankOptionsList">
                                                    @if(!empty($banks) && isset($banks['data']))
                                                        @foreach($banks['data'] as $bank)
                                                            <div class="bank-option" 
                                                                 data-bank-name="{{ $bank['name'] }}" 
                                                                 data-bank-code="{{ $bank['code'] }}"
                                                                 data-bank-slug="{{ $bank['slug'] }}">
                                                                {{ $bank['name'] }}
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <!-- Fallback banks if API fails -->
                                                        <div class="bank-option" data-bank-name="Access Bank">Access Bank</div>
                                                        <div class="bank-option" data-bank-name="GTBank">GTBank</div>
                                                        <div class="bank-option" data-bank-name="First Bank">First Bank</div>
                                                        <div class="bank-option" data-bank-name="UBA">UBA</div>
                                                        <div class="bank-option" data-bank-name="Zenith Bank">Zenith Bank</div>
                                                        <div class="bank-option" data-bank-name="Fidelity Bank">Fidelity Bank</div>
                                                        <div class="bank-option" data-bank-name="Union Bank">Union Bank</div>
                                                        <div class="bank-option" data-bank-name="Stanbic IBTC">Stanbic IBTC</div>
                                                        <div class="bank-option" data-bank-name="Sterling Bank">Sterling Bank</div>
                                                        <div class="bank-option" data-bank-name="Polaris Bank">Polaris Bank</div>
                                                        <div class="bank-option" data-bank-name="Wema Bank">Wema Bank</div>
                                                        <div class="bank-option" data-bank-name="Keystone Bank">Keystone Bank</div>
                                                        <div class="bank-option" data-bank-name="FCMB">FCMB</div>
                                                        <div class="bank-option" data-bank-name="Ecobank">Ecobank</div>
                                                        <div class="bank-option" data-bank-name="Heritage Bank">Heritage Bank</div>
                                                        <div class="bank-option" data-bank-name="Jaiz Bank">Jaiz Bank</div>
                                                        <div class="bank-option" data-bank-name="Providus Bank">Providus Bank</div>
                                                        <div class="bank-option" data-bank-name="Kuda Bank">Kuda Bank</div>
                                                        <div class="bank-option" data-bank-name="OPay">OPay</div>
                                                    @endif
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
                                            <input type="text" name="account_name" class="form-control" 
                                                   placeholder="Enter account name as registered with bank" 
                                                   id="accountName"
                                                   required>
                                            @error('account_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                            <small class="text-muted">Enter account name exactly as it appears on your bank account</small>
                                        </div>

                                        <div class="alert alert-warning">
                                            <i class="feather-alert-triangle me-2"></i>
                                            <strong>Important:</strong> This withdrawal requires admin approval. Please double-check your bank details before submitting.
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
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
                                        @php
                                            $recentWithdrawals = $referral->transactions()
                                                ->where('type', 'debit')
                                                ->whereIn('status', ['pending', 'approved', 'success', 'failed'])
                                                ->latest()
                                                ->take(10)
                                                ->get();
                                        @endphp
                                        
                                        @forelse($recentWithdrawals as $withdrawal)
                                            <tr>
                                                <td><code>{{ $withdrawal->reference }}</code></td>
                                                <td>
                                                    @if(str_contains($withdrawal->description, 'bank'))
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Bank search functionality
const bankSearchInput = document.getElementById('bankSearchInput');
const bankNameInput = document.getElementById('bankNameInput');
const bankOptionsList = document.getElementById('bankOptionsList');
const bankOptions = document.querySelectorAll('.bank-option');

// Show dropdown when input is focused
bankSearchInput.addEventListener('focus', function() {
    bankOptionsList.style.display = 'block';
});

// Filter banks as user types
bankSearchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    let hasVisibleOptions = false;
    
    bankOptions.forEach(option => {
        const bankName = option.getAttribute('data-bank-name').toLowerCase();
        if (bankName.includes(searchTerm)) {
            option.style.display = 'block';
            hasVisibleOptions = true;
        } else {
            option.style.display = 'none';
        }
    });
    
    bankOptionsList.style.display = hasVisibleOptions ? 'block' : 'none';
});

// Select bank when clicked
bankOptions.forEach(option => {
    option.addEventListener('click', function() {
        const bankName = this.getAttribute('data-bank-name');
        bankSearchInput.value = bankName;
        bankNameInput.value = bankName;
        bankOptionsList.style.display = 'none';
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.bank-select-wrapper')) {
        bankOptionsList.style.display = 'none';
    }
});

// Form validation
document.getElementById('bankWithdrawalForm')?.addEventListener('submit', function(e) {
    const accountNumber = document.getElementById('accountNumber').value;
    const accountName = document.getElementById('accountName').value;
    const bankName = document.getElementById('bankNameInput').value;
    
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
        alert('Please enter a valid account name');
        return false;
    }
    
    return confirm('Please confirm your bank details are correct:\n\nBank: ' + bankName + '\nAccount Number: ' + accountNumber + '\nAccount Name: ' + accountName);
});

// Only allow numbers in account number field
document.getElementById('accountNumber')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>

@include('components.g-footer')