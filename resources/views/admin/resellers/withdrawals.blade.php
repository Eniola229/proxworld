@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $reseller->panel_name }} — Withdrawals</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.resellers.show', $reseller) }}">{{ $reseller->panel_name }}</a>
            </li>
            <li class="breadcrumb-item">Withdrawals</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-dark">₦{{ number_format($reseller->totalProfitEarned(), 2) }}</div>
                <div class="fs-13 text-muted">Total Profit Earned</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-success">₦{{ number_format($reseller->availableProfitBalance(), 2) }}</div>
                <div class="fs-13 text-muted">Available Profit Balance</div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Bank</th>
                            <th>Account</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $withdrawal)
                            <tr>
                                <td class="fw-semibold">₦{{ number_format($withdrawal->amount, 2) }}</td>
                                <td>{{ $withdrawal->bank_name }}</td>
                                <td>{{ $withdrawal->account_number }} — {{ $withdrawal->account_name }}</td>
                                <td class="fs-12 text-muted">{{ $withdrawal->reference }}</td>
                                <td>
                                    @php $badges=['success'=>'success','processing'=>'info','pending'=>'warning','failed'=>'danger']; @endphp
                                    <span class="badge bg-soft-{{ $badges[$withdrawal->status] ?? 'secondary' }} text-{{ $badges[$withdrawal->status] ?? 'secondary' }}">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                                <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.reseller-withdrawals.show', $withdrawal) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No withdrawal requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($withdrawals->hasPages())
            <div class="card-footer">{{ $withdrawals->links() }}</div>
        @endif
    </div>
</div>
</div>
</main>

@include('admin.components.footer')