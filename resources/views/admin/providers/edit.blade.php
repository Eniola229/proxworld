@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Provider</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.providers.index') }}">Providers</a></li>
                    <li class="breadcrumb-item">{{ $provider->name }}</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-lg-7 mx-auto">

                    {{-- Stats bar --}}
                    <div class="card mb-3 bg-soft-primary">
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-4 align-items-center">
                                <div>
                                    <div class="fs-11 text-muted">Current Balance</div>
                                    <div class="fw-bold">
                                        {{ $provider->cached_balance !== null ? '₦'.number_format($provider->cached_balance,2) : 'Unknown' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="fs-11 text-muted">Total Orders</div>
                                    <div class="fw-bold">{{ $provider->orders()->count() }}</div>
                                </div>
                                <div>
                                    <div class="fs-11 text-muted">Last Checked</div>
                                    <div class="fw-bold">
                                        {{ $provider->balance_checked_at ? $provider->balance_checked_at->diffForHumans() : 'Never' }}
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('admin.providers.refresh-balance', $provider->id) }}" class="ms-auto">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="feather-refresh-cw me-1"></i> Refresh Balance
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Edit: {{ $provider->name }}</h5>
                        </div>
                        <div class="card-body">

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.providers.update', $provider->id) }}">
                                @csrf @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Provider Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $provider->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">API Endpoint URL <span class="text-danger">*</span></label>
                                    <input type="url" name="api_url" class="form-control @error('api_url') is-invalid @enderror"
                                           value="{{ old('api_url', $provider->api_url) }}" required>
                                    @error('api_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">API Key <span class="text-danger">*</span></label>
                                    <input type="text" name="api_key" class="form-control @error('api_key') is-invalid @enderror"
                                           value="{{ old('api_key', $provider->api_key) }}" required>
                                    @error('api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                        <input type="number" name="priority" class="form-control @error('priority') is-invalid @enderror"
                                               value="{{ old('priority', $provider->priority) }}" min="1" max="100" required>
                                        <div class="form-text">Lower = tried first. Same priority = random selection.</div>
                                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                   id="is_active" {{ old('is_active', $provider->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $provider->notes) }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Update Provider
                                    </button>
                                    <a href="{{ route('admin.providers.index') }}" class="btn btn-light">Cancel</a>
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