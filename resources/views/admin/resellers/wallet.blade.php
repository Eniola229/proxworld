@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')
<main class="nxl-container">
    <div class="nxl-content">

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $reseller->panel_name }} — Owner Wallet</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.resellers.show', $reseller) }}">{{ $reseller->panel_name }}</a>
            </li>
            <li class="breadcrumb-item">Wallet</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-success">₦{{ number_format($owner->balance, 2) }}</div>
                <div class="fs-13 text-muted">Current Balance</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-dark">
                    ₦{{ number_format($transactions->where('type','credit')->sum('amount'), 2) }}
                </div>
                <div class="fs-13 text-muted">Total Credited (this page)</div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance Before</th>
                            <th>Balance After</th>
                            <th>Description</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td><code class="fs-11">{{ $tx->reference }}</code></td>
                                <td>
                                    @if($tx->type === 'credit')
                                        <span class="badge bg-soft-success text-success">Credit</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">Debit</span>
                                    @endif
                                </td>
                                <td class="{{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }} fw-semibold">
                                    {{ $tx->type === 'credit' ? '+' : '-' }}₦{{ number_format($tx->amount, 2) }}
                                </td>
                                <td class="text-muted">₦{{ number_format($tx->balance_before, 2) }}</td>
                                <td class="fw-semibold">₦{{ number_format($tx->balance_after, 2) }}</td>
                                <td class="text-muted fs-12" style="max-width:180px;">{{ $tx->description }}</td>
                                <td><span class="badge bg-soft-secondary text-secondary">{{ $tx->payment_method }}</span></td>
                                <td>
                                    @php $sc = ['success'=>'success','pending'=>'warning','failed'=>'danger']; @endphp
                                    <span class="badge bg-soft-{{ $sc[$tx->status] ?? 'secondary' }} text-{{ $sc[$tx->status] ?? 'secondary' }}">
                                        {{ ucfirst($tx->status) }}
                                    </span>
                                </td>
                                <td class="fs-12">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
</div>
</main>
@include('admin.components.footer')