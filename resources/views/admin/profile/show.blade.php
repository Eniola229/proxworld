@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">My Profile</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Profile</li>
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                
                <!-- Profile Info Card -->
                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="avatar-text avatar-xxl bg-soft-primary text-primary mb-3 mx-auto">
                                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                                </div>
                                <h4 class="mb-1">{{ $admin->name }}</h4>
                                <p class="text-muted mb-2">{{ $admin->email }}</p>
                                <span class="badge bg-soft-success text-success">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
                            </div>

                            <div class="border-top pt-4">
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="text-muted">Role:</span>
                                    <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $admin->role)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="text-muted">Status:</span>
                                    @if($admin->status == 'active')
                                        <span class="badge bg-soft-success text-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-danger text-danger">Inactive</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                    <span class="text-muted">Last Login:</span>
                                    <span class="fw-bold">{{ $admin->last_login_at ? $admin->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Member Since:</span>
                                    <span class="fw-bold">{{ $admin->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Permissions</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                @if($admin->canManageAdmins())
                                    <span class="badge bg-soft-primary text-primary me-2 mb-2">
                                        <i class="feather-users me-1"></i> Manage Admins
                                    </span>
                                @endif
                                @if($admin->canViewAdminLogs())
                                    <span class="badge bg-soft-primary text-primary me-2 mb-2">
                                        <i class="feather-file-text me-1"></i> View Logs
                                    </span>
                                @endif
                                @if($admin->canEditOrders())
                                    <span class="badge bg-soft-info text-info me-2 mb-2">
                                        <i class="feather-shopping-cart me-1"></i> Edit Orders
                                    </span>
                                @endif
                                @if($admin->canDeleteOrders())
                                    <span class="badge bg-soft-danger text-danger me-2 mb-2">
                                        <i class="feather-trash-2 me-1"></i> Delete Orders
                                    </span>
                                @endif
                                @if($admin->canEditTransactions())
                                    <span class="badge bg-soft-success text-success me-2 mb-2">
                                        <i class="feather-dollar-sign me-1"></i> Edit Transactions
                                    </span>
                                @endif
                                @if($admin->canAdjustBalance())
                                    <span class="badge bg-soft-warning text-warning me-2 mb-2">
                                        <i class="feather-trending-up me-1"></i> Adjust Balance
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Forms -->
                <div class="col-xxl-8 col-xl-6">
                    
                    <!-- Update Profile Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="feather-user me-2"></i> Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Update Password -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="feather-lock me-2"></i> Change Password
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.profile.update-password') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                            <i class="feather-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                            <i class="feather-eye"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Must be at least 8 characters</small>
                                    @error('new_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" name="new_password_confirmation" class="form-control" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                            <i class="feather-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="feather-alert-triangle me-2"></i>
                                    <strong>Important:</strong> After changing your password, you will remain logged in on this device, but you'll need to use the new password on other devices.
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="feather-key me-2"></i> Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</main>

@include('admin.components.footer')

<script>
function togglePassword(button) {
    const input = button.parentElement.querySelector('input');
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('feather-eye');
        icon.classList.add('feather-eye-off');
    } else {
        input.type = 'password';
        icon.classList.remove('feather-eye-off');
        icon.classList.add('feather-eye');
    }
}
</script>