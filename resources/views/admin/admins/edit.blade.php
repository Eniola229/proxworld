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

                                <div class="mb-4">
                                    <label for="role" class="form-label fw-bold">Admin Role <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role }}" {{ old('role', $admin->role) == $role ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="is_active" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select name="is_active" id="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                                        <option value="1" {{ old('is_active', $admin->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('is_active', $admin->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
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