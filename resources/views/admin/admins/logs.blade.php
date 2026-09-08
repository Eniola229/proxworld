@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admin Activity Logs</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item">Activity Logs</li>
                </ul>
            </div>
        </div>

        <!-- Super Admin Only Alert -->
        <div class="alert alert-danger d-flex align-items-center mb-4">
            <i class="feather-shield fs-3 me-3"></i>
            <div>
                <h6 class="mb-1 fw-bold">Super Admin Only Section</h6>
                <p class="mb-0">You are viewing sensitive admin activity logs. This section is only accessible to Super Admins.</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">All Admin Activities</h5>
                            <div class="card-header-action">
                                <button type="button" class="btn btn-sm btn-light-brand" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                                    <i class="feather-filter me-2"></i> Filters
                                </button>
                            </div>
                        </div>
                        
                        <!-- Search and Filter Form -->
                        <div class="collapse {{ request()->hasAny(['search', 'action', 'admin_id', 'date_from', 'date_to']) ? 'show' : '' }}" id="filterCollapse">
                            <div class="card-body border-bottom bg-light">
                                <form method="GET" action="{{ route('admin.admins.logs') }}" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fs-11">Search</label>
                                        <input type="text" 
                                               name="search" 
                                               class="form-control" 
                                               placeholder="Action, Description..." 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">Admin</label>
                                        <select name="admin_id" class="form-select">
                                            <option value="">All Admins</option>
                                            @foreach($allAdmins as $adminItem)
                                                <option value="{{ $adminItem->id }}" {{ request('admin_id') == $adminItem->id ? 'selected' : '' }}>
                                                    {{ $adminItem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">Action</label>
                                        <select name="action" class="form-select">
                                            <option value="">All Actions</option>
                                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                            <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                            <option value="viewed" {{ request('action') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                                            <option value="approved_transaction" {{ request('action') == 'approved_transaction' ? 'selected' : '' }}>Approved Transaction</option>
                                            <option value="rejected_transaction" {{ request('action') == 'rejected_transaction' ? 'selected' : '' }}>Rejected Transaction</option>
                                            <option value="refunded" {{ request('action') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                            <option value="adjusted_balance" {{ request('action') == 'adjusted_balance' ? 'selected' : '' }}>Adjusted Balance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">From Date</label>
                                        <input type="date" 
                                               name="date_from" 
                                               class="form-control" 
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fs-11">To Date</label>
                                        <input type="date" 
                                               name="date_to" 
                                               class="form-control" 
                                               value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <div class="d-flex gap-1 w-100">
                                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                <i class="feather-search"></i>
                                            </button>
                                            <a href="{{ route('admin.admins.logs') }}" class="btn btn-light btn-sm">
                                                <i class="feather-x"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Logs Table -->
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Admin</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>Target Type</th>
                                            <th>Target ID</th>
                                            <th>IP Address</th>
                                            <th>Date/Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                            <tr>
                                                <td>
                                                    @if($log->admin)
                                                        <a href="{{ route('admin.admins.show', $log->admin_id) }}" class="d-flex align-items-center">
                                                            <div class="avatar-text avatar-sm bg-soft-primary text-primary me-2">
                                                                {{ substr($log->admin->name, 0, 2) }}
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold d-block">{{ $log->admin->name }}</span>
                                                                <span class="fs-11 text-muted">{{ ucfirst(str_replace('_', ' ', $log->admin->role)) }}</span>
                                                            </div>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Deleted Admin</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $actionColors = [
                                                            'login' => 'success',
                                                            'logout' => 'secondary',
                                                            'created' => 'info',
                                                            'updated' => 'warning',
                                                            'deleted' => 'danger',
                                                            'viewed' => 'primary',
                                                            'approved_transaction' => 'success',
                                                            'rejected_transaction' => 'danger',
                                                            'refunded' => 'warning',
                                                            'adjusted_balance' => 'info',
                                                        ];
                                                        $color = $actionColors[$log->action] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-soft-{{ $color }} text-{{ $color }}">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $log->description }}">
                                                        {{ $log->description }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($log->target_type)
                                                        <code class="fs-11">{{ $log->target_type }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($log->target_id)
                                                        <code class="fs-11">{{ Str::limit($log->target_id, 10) }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td><code class="fs-11">{{ $log->ip_address }}</code></td>
                                                <td>
                                                    <div>{{ $log->created_at->format('M d, Y') }}</div>
                                                    <span class="fs-11 text-muted">{{ $log->created_at->format('H:i:s') }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i class="feather-activity fs-1 text-muted"></i>
                                                    </div>
                                                    <h6 class="text-muted">No activity logs found</h6>
                                                    @if(request()->hasAny(['search', 'action', 'admin_id', 'date_from', 'date_to']))
                                                        <p class="text-muted mb-0">Try adjusting your filters</p>
                                                        <a href="{{ route('admin.admins.logs') }}" class="btn btn-sm btn-primary mt-3">
                                                            Clear Filters
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($logs->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 fs-12">
                                        Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ number_format($logs->total()) }} logs
                                    </p>
                                </div>
                                <div>
                                    {{ $logs->links() }}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

@include('admin.components.footer')