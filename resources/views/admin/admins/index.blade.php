@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admin Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Admins</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                        <i class="feather-plus me-2"></i> Add New Admin
                    </a>
                    @if(auth('admin')->user()->canViewAdminLogs())
                        <a href="{{ route('admin.admins.logs') }}" class="btn btn-danger">
                            <i class="feather-shield me-2"></i> View Admin Logs
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
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

        <!-- Statistics Cards -->
        <div class="row mb-4 mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-primary-subtle me-3">
                                <i class="feather-shield text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Admins</h6>
                                <h3 class="mb-0">{{ number_format($totalAdmins) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-success-subtle me-3">
                                <i class="feather-check-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Active</h6>
                                <h3 class="mb-0 text-success">{{ number_format($activeAdmins) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-danger-subtle me-3">
                                <i class="feather-x-circle text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Inactive</h6>
                                <h3 class="mb-0 text-danger">{{ number_format($inactiveAdmins) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-warning-subtle me-3">
                                <i class="feather-star text-warning"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Super Admins</h6>
                                <h3 class="mb-0 text-warning">{{ number_format($superAdmins) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">All Admins</h5>
                        </div>
                        
                        <!-- Search and Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('admin.admins.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search by name, email, or role..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="role" class="form-select">
                                        <option value="">All Roles</option>
                                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="accountant" {{ request('role') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="hr" {{ request('role') == 'hr' ? 'selected' : '' }}>HR</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="support" {{ request('role') == 'support' ? 'selected' : '' }}>Support</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="feather-search me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                                            <i class="feather-x"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Admins Table -->
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Admin</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Last Login</th>
                                            <th>Joined</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($admins as $admin)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-md bg-soft-primary text-primary me-3">
                                                            {{ substr($admin->name, 0, 2) }}
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('admin.admins.show', $admin->id) }}" class="fw-bold d-block">
                                                                {{ $admin->name }}
                                                            </a>
                                                            @if($admin->id === auth('admin')->id())
                                                                <span class="badge bg-soft-info text-info fs-10">You</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    @if($admin->role == 'super_admin')
                                                        <span class="badge bg-danger">Super Admin</span>
                                                    @elseif($admin->role == 'accountant')
                                                        <span class="badge bg-success">Accountant</span>
                                                    @elseif($admin->role == 'hr')
                                                        <span class="badge bg-warning">HR</span>
                                                    @elseif($admin->role == 'admin')
                                                        <span class="badge bg-primary">Admin</span>
                                                    @elseif($admin->role == 'manager')
                                                        <span class="badge bg-info">Manager</span>
                                                    @else
                                                        <span class="badge bg-secondary">Support</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($admin->status == 'active')
                                                        <span class="badge bg-soft-success text-success">Active</span>
                                                    @else
                                                        <span class="badge bg-soft-danger text-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($admin->last_login_at)
                                                        <div>{{ $admin->last_login_at->format('M d, Y') }}</div>
                                                        <span class="fs-11 text-muted">{{ $admin->last_login_at->diffForHumans() }}</span>
                                                    @else
                                                        <span class="text-muted">Never</span>
                                                    @endif
                                                </td>
                                                <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.admins.show', $admin->id) }}" 
                                                       class="btn btn-sm btn-light-brand">
                                                        <i class="feather-eye me-1"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i class="feather-shield fs-1 text-muted"></i>
                                                    </div>
                                                    <h6 class="text-muted">No admins found</h6>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($admins->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0 fs-12">
                                        Showing {{ $admins->firstItem() }} to {{ $admins->lastItem() }} of {{ number_format($admins->total()) }} admins
                                    </p>
                                </div>
                                <div>
                                    {{ $admins->links() }}
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