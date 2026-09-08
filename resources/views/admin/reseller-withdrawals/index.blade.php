@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Reseller Withdrawals</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Reseller Withdrawals</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card text-center py-3">
                <div class="fs-4 fw-bold text-warning">₦{{ number_format($totalPending, 2) }}</div>
                <div class="fs-13 text-muted">Total Pending</div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="btn-group">
                <a href="{{ route('admin.reseller-withdrawals.index') }}"
                   class="btn btn-sm btn-outline-secondary {{ !request('status') ? 'active' : '' }}">All</a>
                @foreach(['pending', 'processing', 'success', 'failed'] as $s)
                    <a href="{{ route('admin.reseller-withdrawals.index', ['status' => $s]) }}"
                       class="btn btn-sm btn-outline-secondary {{ request('status') === $s ? 'active' : '' }}">
                        {{ ucfirst($s) }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Reseller</th>
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
                                <td>
                                    <div>{{ $withdrawal->reseller->panel_name }}</div>
                                    <small class="text-muted">{{ $withdrawal->reseller->owner->name }}</small>
                                </td>
                                <td class="fw-semibold">₦{{ number_format($withdrawal->amount, 2) }}</td>
                                <td>{{ $withdrawal->bank_name }}</td>
                                <td>
                                    {{ $withdrawal->account_number }}<br>
                                    <small class="text-muted">{{ $withdrawal->account_name }}</small>
                                </td>
                                <td><span class="fs-12 text-muted">{{ $withdrawal->reference }}</span></td>
                                <td>
                                    @php
                                        $badges = [
                                            'success'    => 'success',
                                            'processing' => 'info',
                                            'pending'    => 'warning',
                                            'failed'     => 'danger',
                                        ];
                                    @endphp
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
                                <td colspan="8" class="text-center py-4 text-muted">No withdrawal requests yet.</td>
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