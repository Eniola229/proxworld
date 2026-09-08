@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Add New Admin</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item">Add New</li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Admin Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.admins.store') }}">
                                @csrf

                                <div class="alert alert-info mb-4">
                                    <i class="feather-info me-2"></i>
                                    <strong>Note:</strong> You are creating a new admin user. Please ensure you provide strong credentials and assign the appropriate role.
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required
                                           placeholder="Enter admin's full name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required
                                           placeholder="admin@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="password" class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required
                                               placeholder="Minimum 6 characters">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="password_confirmation" class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required
                                               placeholder="Re-enter password">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="role" class="form-label fw-bold">Admin Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin (Full Access)</option>
                                        <option value="accountant" {{ old('role') == 'accountant' ? 'selected' : '' }}>Accountant (Edit Orders & Wallet)</option>
                                        <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR (Manage Admins Only)</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (View & Reply Tickets)</option>
                                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager (View & Reply Tickets)</option>
                                        <option value="support" {{ old('role') == 'support' ? 'selected' : '' }}>Support (View & Reply Tickets)</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Choose the appropriate role based on the admin's responsibilities</small>
                                </div>

                                <div class="mb-4">
                                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role Permissions Info -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Role Permissions:</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled fs-12">
                                                    <li class="mb-2"><i class="feather-shield text-danger me-2"></i> <strong>Super Admin:</strong> Everything + View Admin Logs</li>
                                                    <li class="mb-2"><i class="feather-dollar-sign text-success me-2"></i> <strong>Accountant:</strong> Edit/Delete Orders & Wallet</li>
                                                    <li class="mb-2"><i class="feather-users text-warning me-2"></i> <strong>HR:</strong> Add/Manage Admins Only</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled fs-12">
                                                    <li class="mb-2"><i class="feather-eye text-primary me-2"></i> <strong>Admin:</strong> View Everything + Reply Tickets</li>
                                                    <li class="mb-2"><i class="feather-eye text-info me-2"></i> <strong>Manager:</strong> View Everything + Reply Tickets</li>
                                                    <li class="mb-2"><i class="feather-life-buoy text-secondary me-2"></i> <strong>Support:</strong> View + Reply Tickets</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="feather-alert-triangle me-2"></i>
                                    <strong>Warning:</strong> This action will be logged and visible to Super Admins.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-plus me-2"></i>
                                        Create Admin
                                    </button>
                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                                        <i class="feather-x me-2"></i>
                                        Cancel
                                    </a>
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