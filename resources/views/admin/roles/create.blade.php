@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">New Role</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item">New</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Role Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.roles.store') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           placeholder="e.g. support">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Lowercase, underscore-separated (e.g. <code>support</code>, <code>super_admin</code>) — this is used internally by the app's permission checks.</small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Permissions</label>
                                    @error('permissions')
                                        <div class="text-danger fs-12 mb-2">{{ $message }}</div>
                                    @enderror
                                    <div class="row g-2">
                                        @forelse($permissions as $permission)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->name }}"
                                                           id="perm-{{ $permission->id }}"
                                                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label fs-12" for="perm-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-muted fs-12 mb-0">
                                                    No permissions exist yet for the "admin" guard. You can create a role now and assign permissions later.
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Create Role
                                    </button>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
                                        <i class="feather-x me-2"></i> Cancel
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