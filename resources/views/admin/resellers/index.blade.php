@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">Reseller Panels</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Resellers</li>
        </ul> 
    </div>
<!--     <div class="page-header-right ms-auto">
        <a href="{{ route('admin.resellers.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i> New Panel
        </a>
    </div> -->
</div>

<div class="main-content">
    <div class="card stretch stretch-full">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Panel Name</th>
                            <th>Subdomain</th>
                            <th>Owner</th>
                            <th>Orders</th>
                            <th>Default Markup</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resellers as $reseller)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-text avatar-sm rounded"
                                             style="background:{{ $reseller->primary_color }}; color:#fff; font-weight:600;">
                                            {{ strtoupper(substr($reseller->panel_name, 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold">{{ $reseller->panel_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <a href="http://{{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}"
                                       target="_blank" class="text-muted fs-12">
                                        {{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}
                                        <i class="feather-external-link ms-1" style="font-size:10px;"></i>
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $reseller->owner->name }}</div>
                                    <small class="text-muted">{{ $reseller->owner->email }}</small>
                                </td>
                                <td>{{ number_format($reseller->orders_count) }}</td>
                                <td>{{ $reseller->default_markup_percent }}%</td>
                                <td>
                                    @if($reseller->status === 'active')
                                        <span class="badge bg-soft-success text-success">Active</span>
                                    @elseif($reseller->status === 'suspended')
                                        <span class="badge bg-soft-danger text-danger">Suspended</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $reseller->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.resellers.show', $reseller) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No reseller panels yet.
                                    <!-- <a href="{{ route('admin.resellers.create') }}" class="text-primary fw-bold">Create one</a> -->
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($resellers->hasPages())
            <div class="card-footer">{{ $resellers->links() }}</div>
        @endif
    </div>
</div>
</div>
</main>


@include('admin.components.footer')