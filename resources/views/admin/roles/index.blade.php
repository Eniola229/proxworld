@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Roles</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                    <li class="breadcrumb-item">Roles</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i> New Role
                </a>
            </div>
        </div>

        <div class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="feather-alert-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Roles</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="border-b">
                                    <th>Role Name</th>
                                    <th>Permissions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td class="fw-bold text-capitalize">{{ str_replace('_', ' ', $role->name) }}</td>
                                        <td>
                                            <span class="badge bg-soft-info text-info">
                                                {{ $role->permissions_count }} permission{{ $role->permissions_count == 1 ? '' : 's' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-light-brand">
                                                <i class="feather-edit me-1"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete role \'{{ $role->name }}\'? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light-danger">
                                                    <i class="feather-trash-2 me-1"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            <i class="feather-shield fs-1 d-block mb-2"></i>
                                            No roles found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($roles->hasPages())
                <div class="card-footer">{{ $roles->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</main>

@include('admin.components.footer')
