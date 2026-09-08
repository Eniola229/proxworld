@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Admin</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.show', $admin->id) }}">{{ $admin->name }}</a></li>
                    <li class="breadcrumb-item">Edit</li>
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
                            <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="alert alert-info mb-4">
                                    <i class="feather-info me-2"></i>
                                    <strong>Admin ID:</strong> {{ $admin->id }}
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $admin->name) }}" 
                                           required>
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
                                           value="{{ old('email', $admin->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label for="password" class="form-label fw-bold">New Password (Optional)</label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Leave blank to keep current password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Only fill this if you want to change the password</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="password_confirmation" class="form-label fw-bold">Confirm New Password</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               placeholder="Re-enter new password">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="role" class="form-label fw-bold">Admin Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="super_admin" {{ old('role', $admin->role) == 'super_admin' ? 'selected' : '' }}>Super Admin (Full Access)</option>
                                        <option value="accountant" {{ old('role', $admin->role) == 'accountant' ? 'selected' : '' }}>Accountant (Edit Orders & Wallet)</option>
                                        <option value="hr" {{ old('role', $admin->role) == 'hr' ? 'selected' : '' }}>HR (Manage Admins Only)</option>
                                        <option value="admin" {{ old('role', $admin->role) == 'admin' ? 'selected' : '' }}>Admin (View & Reply Tickets)</option>
                                        <option value="manager" {{ old('role', $admin->role) == 'manager' ? 'selected' : '' }}>Manager (View & Reply Tickets)</option>
                                        <option value="support" {{ old('role', $admin->role) == 'support' ? 'selected' : '' }}>Support (View & Reply Tickets)</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="active" {{ old('status', $admin->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $admin->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-warning">
                                    <i class="feather-alert-triangle me-2"></i>
                                    <strong>Warning:</strong> All changes will be logged and visible to Super Admins.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>
                                        Update Admin
                                    </button>
                                    <a href="{{ route('admin.admins.show', $admin->id) }}" class="btn btn-light">
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