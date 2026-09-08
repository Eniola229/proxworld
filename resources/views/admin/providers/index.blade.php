@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">API Providers</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Providers</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto d-flex gap-2">
                <form method="POST" action="{{ route('admin.providers.refresh-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-light">
                        <i class="feather-refresh-cw me-1"></i> Refresh All Balances
                    </button>
                </form>
                <a href="{{ route('admin.providers.create') }}" class="btn btn-sm btn-primary">
                    <i class="feather-plus me-1"></i> Add Provider
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

            {{-- Balance Overview Cards --}}
            <div class="row mb-4">
                @foreach($providers as $provider)
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full border-{{ $provider->is_active ? 'success' : 'secondary' }} border-opacity-25">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $provider->name }}</h6>
                                    <span class="badge {{ $provider->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                        {{ $provider->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="badge bg-soft-primary text-primary ms-1">Priority {{ $provider->priority }}</span>
                                </div>
                                <div class="avatar-text avatar-md {{ $provider->is_active ? 'bg-success-subtle' : 'bg-secondary-subtle' }}">
                                    <i class="feather-server {{ $provider->is_active ? 'text-success' : 'text-secondary' }}"></i>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="fs-3 fw-bold text-dark">
                                    @if($provider->cached_balance !== null)
                                        ₦{{ number_format($provider->cached_balance, 2) }}
                                    @else
                                        <span class="text-muted fs-5">Unavailable</span>
                                    @endif
                                </div>
                                <div class="text-muted fs-11">
                                    @if($provider->balance_checked_at)
                                        Updated {{ $provider->balance_checked_at->diffForHumans() }}
                                    @else
                                        Never refreshed
                                    @endif
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <div class="text-muted fs-11 text-truncate mb-2">{{ $provider->api_url }}</div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('admin.providers.refresh-balance', $provider->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-light" title="Refresh Balance">
                                            <i class="feather-refresh-cw"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.providers.toggle', $provider->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-xs {{ $provider->is_active ? 'btn-warning' : 'btn-success' }}" title="{{ $provider->is_active ? 'Disable' : 'Enable' }}">
                                            <i class="feather-{{ $provider->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.providers.edit', $provider->id) }}" class="btn btn-xs btn-info" title="Edit">
                                        <i class="feather-edit-2"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.providers.destroy', $provider->id) }}"
                                          onsubmit="return confirm('Delete provider [{{ $provider->name }}]? Orders using this provider will NOT be deleted.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($providers->isEmpty())
                <div class="col-12">
                    <div class="alert alert-info">
                        No providers added yet. <a href="{{ route('admin.providers.create') }}">Add your first provider</a>.
                    </div>
                </div>
                @endif
            </div>

            {{-- Providers Table --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Providers</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Priority</th>
                                    <th>Name</th>
                                    <th>API URL</th>
                                    <th>Balance</th>
                                    <th>Orders</th>
                                    <th>Status</th>
                                    <th>Last Checked</th>
                                    <th>Notes</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($providers as $provider)
                                <tr>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary fw-bold">{{ $provider->priority }}</span>
                                    </td>
                                    <td class="fw-bold">{{ $provider->name }}</td>
                                    <td>
                                        <span class="text-muted fs-11 text-truncate d-block" style="max-width:200px;" title="{{ $provider->api_url }}">
                                            {{ $provider->api_url }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($provider->cached_balance !== null)
                                            <span class="fw-bold text-success">₦{{ number_format($provider->cached_balance, 2) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.index', ['provider_id' => $provider->id]) }}" class="badge bg-soft-primary text-primary">
                                            {{ $provider->orders()->count() }} orders
                                        </a>
                                    </td>
                                    <td>
                                        @if($provider->is_active)
                                            <span class="badge bg-soft-success text-success"><i class="feather-check me-1"></i>Active</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger"><i class="feather-x me-1"></i>Inactive</span>
                                        @endif
                                    </td>
                                    <td class="fs-11 text-muted">
                                        {{ $provider->balance_checked_at ? $provider->balance_checked_at->diffForHumans() : 'Never' }}
                                    </td>
                                    <td class="fs-11 text-muted">{{ Str::limit($provider->notes, 40) ?: '—' }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <form method="POST" action="{{ route('admin.providers.refresh-balance', $provider->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-light" title="Refresh Balance">
                                                    <i class="feather-refresh-cw"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.providers.toggle', $provider->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $provider->is_active ? 'btn-warning' : 'btn-success' }}">
                                                    {{ $provider->is_active ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.providers.edit', $provider->id) }}" class="btn btn-sm btn-info">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.providers.destroy', $provider->id) }}"
                                                  onsubmit="return confirm('Delete [{{ $provider->name }}]?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        No providers yet. <a href="{{ route('admin.providers.create') }}">Add one now</a>.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>

@include('admin.components.footer')