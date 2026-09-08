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
                                <h2 class="display-6 fw-bold text-primary">
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
                                <form method="POST" action="{{ route('wallet.topup') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Amount (₦)</label>
                                        <input type="number" name="amount" class="form-control form-control-lg" placeholder="e.g. 5000" min="500" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">Proceed to Pay</button>
                                    </div>
                                </form>
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
                                        <tbody>
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