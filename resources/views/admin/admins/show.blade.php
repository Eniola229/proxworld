@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admin Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item">{{ $admin->name }}</li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                
                <!-- Admin Info Card -->
                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="avatar-text avatar-xl bg-soft-primary text-primary mb-3">
                                    {{ substr($admin->name, 0, 2) }}
                                </div>
                                <h5 class="mb-1">{{ $admin->name }}</h5>
                                <p class="fs-12 text-muted mb-3">{{ $admin->email }}</p>
                                
                                <div class="mb-3">
                                    @if($admin->role == 'super_admin')
                                        <span class="badge bg-danger fs-12">Super Admin</span>
                                    @elseif($admin->role == 'accountant')
                                        <span class="badge bg-success fs-12">Accountant</span>
                                    @elseif($admin->role == 'hr')
                                        <span class="badge bg-warning fs-12">HR</span>
                                    @elseif($admin->role == 'admin')
                                        <span class="badge bg-primary fs-12">Admin</span>
                                    @elseif($admin->role == 'manager')
                                        <span class="badge bg-info fs-12">Manager</span>
                                    @else
                                        <span class="badge bg-secondary fs-12">Support</span>
                                    @endif

                                    @if($admin->status == 'active')
                                        <span class="badge bg-soft-success text-success fs-12">Active</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger fs-12">Inactive</span>
                                    @endif

                                    @if($admin->id === auth('admin')->id())
                                        <span class="badge bg-soft-info text-info fs-12">You</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Admin ID:</span>
                                    <code class="fs-11">{{ substr($admin->id, 0, 13) }}...</code>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Last Login:</span>
                                    <span class="fs-12 fw-bold">
                                        @if($lastLogin)
                                            {{ $lastLogin->diffForHumans() }}
                                        @else
                                            Never
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Last Login IP:</span>
                                    <code class="fs-11">{{ $admin->last_login_ip ?? 'N/A' }}</code>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Total Logins:</span>
                                    <span class="fs-12 fw-bold">{{ number_format($totalLogins) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="fs-12 text-muted">Total Actions:</span>
                                    <span class="fs-12 fw-bold">{{ number_format($totalActions) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Member Since:</span>
                                    <span class="fs-12 fw-bold">{{ $admin->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            @if($admin->id !== auth('admin')->id())
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-primary">
                                    <i class="feather-edit me-2"></i> Edit Admin
                                </a>

                                @if(auth('admin')->user()->isSuperAdmin())
                                    <form method="POST" action="{{ route('admin.admins.destroy', $admin->id) }}" onsubmit="return confirm('Are you sure you want to DELETE this admin? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="feather-trash-2 me-2"></i> Delete Admin
                                        </button>
                                    </form>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Permissions Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Permissions</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12">Manage Admins</span>
                                    @if($admin->canManageAdmins())
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-x-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12">View Admin Logs</span>
                                    @if($admin->canViewAdminLogs())
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-x-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12">Edit Orders</span>
                                    @if($admin->canEditOrders())
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-x-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12">Edit Transactions</span>
                                    @if($admin->canEditTransactions())
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-x-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12">Adjust Balances</span>
                                    @if($admin->canAdjustBalance())
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-x-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12">View Customer Logs</span>
                                    @if($admin->canViewCustomerLogs())
                                        <i class="feather-check-circle text-success"></i>
                                    @else
                                        <i class="feather-x-circle text-danger"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Activity Logs (Super Admin Only) -->
                <div class="col-xxl-8 col-xl-6">
                    @if(auth('admin')->user()->canViewAdminLogs() && $logs)
                    <div class="card">
                        <div class="card-header bg-soft-danger">
                            <h5 class="card-title text-danger">
                                <i class="feather-shield me-2"></i> Admin Activity Logs (Super Admin Only)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Action</th>
                                            <th>Description</th>
                                            <th>Target</th>
                                            <th>IP Address</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-soft-primary text-primary">
                                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                    </span>
                                                </td>
                                                <td>{{ Str::limit($log->description, 50) }}</td>
                                                <td>
                                                    @if($log->target_type)
                                                        <code class="fs-11">{{ $log->target_type }}</code>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td><code class="fs-11">{{ $log->ip_address }}</code></td>
                                                <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted">No activity logs</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($logs->hasPages())
                        <div class="card-footer">
                            {{ $logs->links() }}
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="feather-info me-2"></i>
                        <strong>Activity logs are only visible to Super Admins.</strong>
                    </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</main>

@include('admin.components.footer')