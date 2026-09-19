@include('components.g-header')
@include('components.nav')
 <main class="nxl-container">
        <div class="nxl-content">

            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">My Wallet</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Wallet</li>
                    </ul>
                </div>
            </div>

            <div class="main-content">

                <!-- TOAST ALERT -->
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
                    <!-- Balance -->
                    <div class="col-md-6 mb-4">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <h5 class="card-title text-muted mb-4">Current Balance</h5>
                                <h2 class="display-6 fw-bold text-primary" id="wallet-balance">
                                    ₦{{ number_format(auth()->user()->balance, 2) }}
                                </h2>
                                <p class="text-muted mt-3">Available funds for orders</p>
                            </div>
                        </div>
                    </div>

                    <!-- Add Funds Form -->
                    <div class="col-md-6 mb-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Add Funds</h5>
                            </div>
                            <div class="card-body">

                                @if(session('virtualAccount'))
                                    @php $va = session('virtualAccount'); @endphp

                                    <style>
                                        #virtual-account-details .va-label {
                                            font-size: .9rem;
                                            font-weight: 600;
                                            color: #495057;
                                            margin-bottom: .15rem;
                                        }
                                        #virtual-account-details .va-value {
                                            font-size: 1.5rem;
                                            font-weight: 800;
                                            line-height: 1.2;
                                            color: #111;
                                            word-break: break-word;
                                        }
                                        #virtual-account-details .va-number {
                                            font-size: 2rem;
                                            letter-spacing: .08em;
                                        }
                                        #virtual-account-details .va-amount {
                                            font-size: 1.75rem;
                                        }
                                        #virtual-account-details .va-copy-btn {
                                            min-width: 84px;
                                            font-weight: 700;
                                            flex-shrink: 0;
                                        }
                                        #va-timer-wrap {
                                            font-size: 1rem;
                                            font-weight: 700;
                                            font-variant-numeric: tabular-nums;
                                        }
                                        #va-timer-wrap.is-low {
                                            background-color: #dc3545 !important;
                                        }

                                        /* "Copied" popup */
                                        #copy-toast {
                                            position: fixed;
                                            left: 50%;
                                            bottom: 28px;
                                            transform: translate(-50%, 20px);
                                            background: #212529;
                                            color: #fff;
                                            padding: .65rem 1.2rem;
                                            border-radius: 50rem;
                                            font-weight: 600;
                                            font-size: .95rem;
                                            box-shadow: 0 6px 20px rgba(0, 0, 0, .25);
                                            opacity: 0;
                                            pointer-events: none;
                                            z-index: 2000;
                                            transition: opacity .2s ease, transform .2s ease;
                                        }
                                        #copy-toast.show {
                                            opacity: 1;
                                            transform: translate(-50%, 0);
                                        }
                                        @@media (prefers-reduced-motion: reduce) {
                                            #copy-toast { transition: none; }
                                        }
                                    </style>

                                    <div class="alert alert-info p-3 p-md-4" id="virtual-account-details" data-reference="{{ $va['reference'] }}">

                                        <!-- Active state -->
                                        <div id="va-active">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                                                <h6 class="mb-0 fw-bold fs-5">Complete your transfer</h6>
                                                <span id="va-timer-wrap" class="badge bg-dark">
                                                    Expires in <span id="va-timer">15:00</span>
                                                </span>
                                            </div>

                                            <div class="mb-4">
                                                <div class="va-label">Bank</div>
                                                <div class="va-value">{{ $va['account_bank_name'] }}</div>
                                            </div>

                                            <div class="mb-4">
                                                <div class="va-label">Account number</div>
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <span class="va-value va-number" id="va-account-number">{{ $va['account_number'] }}</span>
                                                    <button type="button"
                                                            class="btn btn-dark va-copy-btn"
                                                            data-copy="{{ $va['account_number'] }}"
                                                            data-copy-label="Account number copied">
                                                        Copy
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <div class="va-label">Amount to send</div>
                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                    <span class="va-value va-amount" id="va-amount">₦{{ number_format($va['amount'], 2) }}</span>
                                                    <button type="button"
                                                            class="btn btn-dark va-copy-btn"
                                                            data-copy="{{ number_format($va['amount'], 2, '.', '') }}"
                                                            data-copy-label="Amount copied">
                                                        Copy
                                                    </button>
                                                </div>
                                            </div>

                                            <p class="text-muted small mb-3">
                                                Send the <strong>exact</strong> amount above from your bank app or USSD.
                                                This account is one-time use and expires in 15 minutes — don't save it.
                                            </p>
                                            <p class="mb-0">
                                                <span id="va-status" class="badge bg-warning fs-6">Waiting for payment…</span>
                                            </p>
                                        </div>

                                        <!-- Expired state -->
                                        <div id="va-expired" class="d-none text-center py-4">
                                            <h4 class="fw-bold text-danger mb-2">Account expired</h4>
                                            <p class="mb-0" id="va-expired-msg">
                                                Don't send money to this account.
                                                Reloading in <strong id="va-reload-count">3</strong> sec…
                                            </p>
                                            <button type="button" id="va-reload-btn" class="btn btn-primary mt-3 d-none" onclick="window.location.reload()">
                                                Start again
                                            </button>
                                        </div>
                                    </div>

                                    <div id="copy-toast" role="status" aria-live="polite"></div>
                                @else
                                    <form method="POST" action="{{ route('wallet.topup') }}">
                                        @csrf
                                        <input type="hidden" name="currency" value="NGN">
                                        <div class="mb-3">
                                            <label class="form-label">Amount (₦)</label>
                                            <input type="number" name="amount" class="form-control form-control-lg" placeholder="e.g. 5000" min="500" required>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg">Proceed to Pay</button>
                                        </div>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Transaction History</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Reference</th>
                                                <th>Method</th>
                                                <th>Bal Before</th>
                                                <th>Amount</th>
                                                <th>Bal After</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="transactions-body">
                                            @forelse(auth()->user()->wallet()->latest()->paginate(10) as $log)
                                                <tr>
                                                    <td>{{ $log->created_at->format('M d, Y') }}</td>
                                                    <td><code>{{ $log->reference }}</code></td>
                                                    <td>{{ $log->payment_method }}</td>
                                                    <td class="text-muted">₦{{ number_format($log->balance_before, 2) }}</td>

                                                    <td class="fw-bold {{ $log->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                        @if($log->type == 'credit') + @else - @endif
                                                        ₦{{ number_format($log->amount, 2) }}
                                                    </td>

                                                    <td class="text-muted">₦{{ number_format($log->balance_after, 2) }}</td>
                                                    <td>
                                                  <span class="badge bg-{{ $log->status == 'success' ? 'success' : ($log->status == 'pending' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($log->status) }}
                                                    </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="text-center text-muted py-4">No transactions yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                {{ auth()->user()->wallet()->latest()->paginate(10)->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    @include('components.g-footer')

    @if(session('virtualAccount'))
    <script>
        (function () {
            const box         = document.getElementById('virtual-account-details');
            const reference   = box.dataset.reference;
            const statusBadge = document.getElementById('va-status');
            const timerEl     = document.getElementById('va-timer');
            const timerWrap   = document.getElementById('va-timer-wrap');
            const activeBox   = document.getElementById('va-active');
            const expiredBox  = document.getElementById('va-expired');
            const reloadCount = document.getElementById('va-reload-count');
            const expiredMsg  = document.getElementById('va-expired-msg');
            const reloadBtn   = document.getElementById('va-reload-btn');
            const toastEl     = document.getElementById('copy-toast');

            const DURATION   = 15 * 60 * 1000; // 15 minutes
            const expiryKey  = 'va_expires_' + reference;
            const reloadKey  = 'va_reloaded_' + reference;

            let finished = false;
            let pollTimer = null;
            let clockTimer = null;

            /* ---------------------------------------------------------
             * COPY (works on iPhone Safari + Android + desktop)
             * ------------------------------------------------------- */
            function legacyCopy(text) {
                // Must run synchronously inside the tap for iOS to allow it
                const el = document.createElement('textarea');
                el.value = text;
                el.contentEditable = true;
                el.readOnly = false;
                el.style.position = 'fixed';
                el.style.top = '0';
                el.style.left = '0';
                el.style.opacity = '0';
                el.style.fontSize = '16px'; // stops iOS from zooming
                document.body.appendChild(el);

                const range = document.createRange();
                range.selectNodeContents(el);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                el.setSelectionRange(0, text.length);

                let ok = false;
                try { ok = document.execCommand('copy'); } catch (e) { ok = false; }

                sel.removeAllRanges();
                document.body.removeChild(el);
                return ok;
            }

            async function copyText(text) {
                if (legacyCopy(text)) return true;

                if (navigator.clipboard && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(text);
                        return true;
                    } catch (e) { /* fall through */ }
                }
                return false;
            }

            let toastTimeout = null;
            function showToast(message) {
                toastEl.textContent = message;
                toastEl.classList.add('show');
                clearTimeout(toastTimeout);
                toastTimeout = setTimeout(() => toastEl.classList.remove('show'), 1600);
            }

            document.querySelectorAll('[data-copy]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const ok = await copyText(btn.dataset.copy);
                    showToast(ok ? (btn.dataset.copyLabel || 'Copied') : 'Could not copy — press and hold to copy');
                });
            });

            /* ---------------------------------------------------------
             * 15 MINUTE TIMER
             * ------------------------------------------------------- */
            let expiresAt;
            try {
                // Keep the same deadline if the page is refreshed
                const saved = parseInt(localStorage.getItem(expiryKey), 10);
                expiresAt = saved > 0 ? saved : Date.now() + DURATION;
                localStorage.setItem(expiryKey, String(expiresAt));
            } catch (e) {
                expiresAt = Date.now() + DURATION;
            }

            function formatTime(ms) {
                const total = Math.max(0, Math.ceil(ms / 1000));
                const m = String(Math.floor(total / 60)).padStart(2, '0');
                const s = String(total % 60).padStart(2, '0');
                return m + ':' + s;
            }

            function stopAll() {
                finished = true;
                clearInterval(pollTimer);
                clearInterval(clockTimer);
            }

            function expire() {
                if (finished) return;
                stopAll();

                activeBox.classList.add('d-none');
                expiredBox.classList.remove('d-none');

                let alreadyReloaded = false;
                try { alreadyReloaded = sessionStorage.getItem(reloadKey) === '1'; } catch (e) {}

                // Safety: if we already reloaded once and this account is still showing,
                // don't loop forever — let the user restart manually.
                if (alreadyReloaded) {
                    expiredMsg.textContent = "Don't send money to this account. Please start again.";
                    reloadBtn.classList.remove('d-none');
                    return;
                }

                try { sessionStorage.setItem(reloadKey, '1'); } catch (e) {}

                let left = 3;
                reloadCount.textContent = left;
                const t = setInterval(() => {
                    left--;
                    reloadCount.textContent = Math.max(left, 0);
                    if (left <= 0) {
                        clearInterval(t);
                        window.location.reload();
                    }
                }, 1000);
            }

            function tick() {
                if (finished) return;
                const remaining = expiresAt - Date.now();
                if (remaining <= 0) {
                    timerEl.textContent = '00:00';
                    expire();
                    return;
                }
                timerEl.textContent = formatTime(remaining);
                timerWrap.classList.toggle('is-low', remaining <= 2 * 60 * 1000);
            }

            tick();
            clockTimer = setInterval(tick, 1000);

            // iPhones pause timers in the background — re-check when the user returns
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) tick();
            });

            /* ---------------------------------------------------------
             * PAYMENT STATUS POLLING
             * ------------------------------------------------------- */
            pollTimer = setInterval(async () => {
                if (finished) return;
                try {
                    const res = await fetch(`{{ route('wallet.topup-status') }}?reference=${encodeURIComponent(reference)}`);
                    const json = await res.json();

                    if (finished) return;

                    if (json.status === 'success') {
                        stopAll();
                        timerWrap.classList.add('d-none');
                        statusBadge.textContent = 'Payment received!';
                        statusBadge.className = 'badge bg-success fs-6';
                        try { localStorage.removeItem(expiryKey); } catch (e) {}
                        setTimeout(() => window.location.reload(), 1200);
                    } else if (json.status === 'failed') {
                        stopAll();
                        timerWrap.classList.add('d-none');
                        statusBadge.textContent = 'Payment failed';
                        statusBadge.className = 'badge bg-danger fs-6';
                    }
                    // if 'pending', keep polling silently
                } catch (e) {
                    // network hiccup — just try again next tick
                }
            }, 4000);
        })();
    </script>
    @endif
