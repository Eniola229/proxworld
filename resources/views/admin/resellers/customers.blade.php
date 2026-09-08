@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $reseller->panel_name }} — Customers</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.resellers.show', $reseller) }}">{{ $reseller->panel_name }}</a>
            </li>
            <li class="breadcrumb-item">Customers</li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Balance</th>
                            <th>Orders</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $membership)
                            @php $u = $membership->user; @endphp
                            <tr>
                                <td class="fw-semibold">{{ $u->name }}</td>
                                <td class="text-muted">{{ $u->email }}</td>
                                <td>₦{{ number_format($u->balance, 2) }}</td>
                                <td>{{ $u->orders()->where('reseller_id', $reseller->id)->count() }}</td>
                                <td>{{ $membership->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No customers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
            <div class="card-footer">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
</div>
</main>

@include('admin.components.footer')