@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Withdraw Profit</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/manage/settings">Manage</a></li>
                    <li class="breadcrumb-item">Withdraw</li>
                </ul> 
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
                    <div class="toast show bg-white shadow-lg border-0" role="alert">
                        <div class="toast-header">
                            <strong class="me-auto text-uppercase">{{ session('alert')['type'] }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">{{ session('alert')['message'] }}</div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="mb-2">Available Balance</h6>
                            <h3 class="mb-0 text-success">₦{{ number_format($availableBalance, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="mb-2">Total Profit Earned</h6>
                            <h3 class="mb-0">₦{{ number_format($totalProfit, 2) }}</h3>
                        </div>
                    </div> 
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <h6 class="mb-2">Total Withdrawn</h6>
                            <h3 class="mb-0">₦{{ number_format($totalWithdrawn, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-xl-5">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Request Withdrawal</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('reseller.manage.withdraw.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Amount (₦)</label>
                                    <input type="number" name="amount" class="form-control"
                                           min="1000" step="0.01" required
                                           placeholder="Minimum ₦1,000" />
                                </div>

                                <div class="mb-3 position-relative">
                                    <label class="form-label">Bank</label>
                                    <input type="text"
                                           id="bank_search"
                                           class="form-control"
                                           placeholder="Type to search your bank..."
                                           autocomplete="off"
                                           required />
                                    <input type="hidden" name="bank_name" id="bank_name_input" required />
                                    <input type="hidden" name="bank_code" id="bank_code_input" required />

                                    <div id="bank_dropdown"
                                         class="list-group position-absolute w-100 shadow-sm"
                                         style="max-height: 220px; overflow-y: auto; z-index: 1000; display: none;">
                                        @foreach($banks as $bank)
                                            <button type="button"
                                                    class="list-group-item list-group-item-action bank-option"
                                                    data-name="{{ $bank['name'] }}"
                                                    data-code="{{ $bank['code'] }}">
                                                {{ $bank['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Account Number</label>
                                    <input type="text" name="account_number" class="form-control"
                                           maxlength="10" minlength="10" required />
                                </div>

                              <div class="mb-3">
                                    <label class="form-label">Account Name</label>
                                    <input type="text" name="account_name" id="account_name" class="form-control" readonly required
                                           placeholder="Auto-filled after account resolves" />
                                    <div id="resolve_status" class="small mt-1"></div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100"
                                        {{ $availableBalance < 1000 ? 'disabled' : '' }}>
                                    Withdraw
                                </button>

                                @if($availableBalance < 1000)
                                    <div class="small text-muted mt-2">
                                        You need at least ₦1,000 in available profit to withdraw.
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Withdrawal History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Amount</th>
                                            <th>Bank</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($withdrawals as $w)
                                        <tr>
                                            <td>{{ $w->reference }}</td>
                                            <td>₦{{ number_format($w->amount, 2) }}</td>
                                            <td>{{ $w->bank_name }}</td>
                                            <td>
                                                @php
                                                    $colors = ['success' => 'success', 'processing' => 'info', 'pending' => 'warning', 'failed' => 'danger'];
                                                    $color = $colors[$w->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ ucfirst($w->status) }}</span>
                                            </td>
                                            <td>{{ $w->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center">No withdrawals yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">{{ $withdrawals->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('bank_search');
    const hiddenInput    = document.getElementById('bank_name_input');
    const bankCodeInput  = document.getElementById('bank_code_input');
    const dropdown        = document.getElementById('bank_dropdown');
    const options          = dropdown.querySelectorAll('.bank-option');
    const accountNumberInput = document.querySelector('input[name="account_number"]');
    const accountNameInput   = document.getElementById('account_name');
    const resolveStatus       = document.getElementById('resolve_status');
    const submitBtn            = document.querySelector('button[type="submit"]');

    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
    });

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();
        hiddenInput.value = '';
        bankCodeInput.value = '';
        accountNameInput.value = '';
        let anyVisible = false;

        options.forEach(opt => {
            const match = opt.dataset.name.toLowerCase().includes(query);
            opt.style.display = match ? 'block' : 'none';
            if (match) anyVisible = true;
        });

        dropdown.style.display = anyVisible ? 'block' : 'none';
    });

    options.forEach(opt => {
        opt.addEventListener('click', () => {
            searchInput.value = opt.dataset.name;
            hiddenInput.value = opt.dataset.name;
            bankCodeInput.value = opt.dataset.code;
            dropdown.style.display = 'none';
            tryResolveAccount();
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#bank_search') && !e.target.closest('#bank_dropdown')) {
            dropdown.style.display = 'none';
        }
    });

    accountNumberInput.addEventListener('input', () => {
        accountNameInput.value = '';
        tryResolveAccount();
    });

    function tryResolveAccount() {
        const bankName = hiddenInput.value;
        const accountNumber = accountNumberInput.value.trim();

        if (!bankName || accountNumber.length !== 10) {
            resolveStatus.textContent = '';
            return;
        }

        resolveStatus.textContent = 'Resolving account name...';
        resolveStatus.className = 'small mt-1 text-muted';
        submitBtn.disabled = true;

        fetch('{{ route("reseller.manage.withdraw.resolve-account") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ bank_name: bankName, account_number: accountNumber }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.account_name) {
                accountNameInput.value = data.account_name;
                bankCodeInput.value = data.bank_code || bankCodeInput.value;
                resolveStatus.textContent = 'Verified: ' + data.account_name;
                resolveStatus.className = 'small mt-1 text-success';
            } else {
                accountNameInput.value = '';
                resolveStatus.textContent = data.message || 'Could not resolve account name.';
                resolveStatus.className = 'small mt-1 text-danger';
            }
        })
        .catch(() => {
            accountNameInput.value = '';
            resolveStatus.textContent = 'Error resolving account. Try again.';
            resolveStatus.className = 'small mt-1 text-danger';
        })
        .finally(() => {
            submitBtn.disabled = ({{ $availableBalance < 1000 ? 'true' : 'false' }}) || !accountNameInput.value;
        });
    }
});
</script>

@include('reseller.components.g-footer')