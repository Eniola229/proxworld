@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">API Access</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">API Access</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('api.docs') }}" class="btn btn-sm btn-light-brand" target="_blank">
                    <i class="feather-book me-1"></i> API Docs
                </a>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert.type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                {{ session('alert.message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="main-content">
            <div class="row">

                <!-- Generate Key -->
                <div class="col-lg-4">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Generate API Key</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('api.generate') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Key Name</label>
                                    <input type="text" name="name" class="form-control"
                                           placeholder="e.g. My Bot, Production" required maxlength="50">
                                    <small class="text-muted">Max 3 API keys allowed</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="feather-key me-2"></i>Generate Key
                                </button>
                            </form>

                            <hr>

                            <div class="alert alert-info mb-0">
                                <h6 class="fw-bold mb-2"><i class="feather-info me-2"></i>API Endpoint</h6>
                                <code class="d-block mb-2" style="font-size: 11px; word-break: break-all;">
                                    {{ url('/api/v2') }}
                                </code>
                                <small>All requests are POST. Pass <strong>key</strong> and <strong>action</strong> parameters.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keys List -->
                <div class="col-lg-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Your API Keys</h5>
                        </div>
                        <div class="card-body">
                            @forelse($apiKeys as $apiKey)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ $apiKey->name }}</h6>
                                            <small class="text-muted">
                                                Created {{ $apiKey->created_at->diffForHumans() }}
                                                @if($apiKey->last_used_at)
                                                    · Last used {{ $apiKey->last_used_at->diffForHumans() }}
                                                @else
                                                    · Never used
                                                @endif
                                            </small>
                                        </div>
                                        <span class="badge {{ $apiKey->status === 'active' ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }}">
                                            {{ ucfirst($apiKey->status) }}
                                        </span>
                                    </div>

                                    <!-- API Key Display -->
                                    <div class="input-group mb-2">
                                        <input type="password" class="form-control form-control-sm font-monospace"
                                               id="key_{{ $apiKey->id }}" value="{{ $apiKey->key }}" readonly>
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                onclick="toggleKeyVisibility('{{ $apiKey->id }}')">
                                            <i class="feather-eye" id="eye_{{ $apiKey->id }}"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                onclick="copyKey('{{ $apiKey->key }}')">
                                            <i class="feather-copy"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <form action="{{ route('api.toggle', $apiKey->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-xs {{ $apiKey->status === 'active' ? 'btn-warning' : 'btn-success' }}">
                                                {{ $apiKey->status === 'active' ? 'Disable' : 'Enable' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('api.destroy', $apiKey->id) }}" method="POST"
                                              onsubmit="return confirm('Delete this API key? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="feather-key fs-1 text-muted"></i>
                                    <p class="text-muted mt-3">No API keys yet. Generate one to get started.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

@include('components.g-footer')

<script>
function toggleKeyVisibility(id) {
    const input = document.getElementById('key_' + id);
    const eye = document.getElementById('eye_' + id);
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.replace('feather-eye', 'feather-eye-off');
    } else {
        input.type = 'password';
        eye.classList.replace('feather-eye-off', 'feather-eye');
    }
}

function copyKey(key) {
    navigator.clipboard.writeText(key).then(() => {
        alert('API key copied to clipboard!');
    });
}
</script>