@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

@php $canManage = auth('admin')->user()->can('tracking.manage'); @endphp

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Tracking Links</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.traffic.index') }}">Traffic Sources</a></li>
                    <li class="breadcrumb-item">Tracking Links</li>
                </ul>
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
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @if($canManage)
            <div class="card mt-4">
                <div class="card-header"><h5 class="card-title">Create Tracking Link</h5></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tracking-links.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Website / Source <span class="text-danger">*</span></label>
                            <input type="text" name="source" class="form-control" placeholder="eg, x.com or x" value="{{ old('source') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Label</label>
                            <input type="text" name="label" class="form-control" placeholder="eg, Twitter bio link" value="{{ old('label') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Landing Path</label>
                            <input type="text" name="landing_path" class="form-control" placeholder="/ or /register" value="{{ old('landing_path', '/') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Medium</label>
                            <input type="text" name="medium" class="form-control" placeholder="social, email, referral" value="{{ old('medium') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Campaign</label>
                            <input type="text" name="campaign" class="form-control" placeholder="launch" value="{{ old('campaign') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Content</label>
                            <input type="text" name="content" class="form-control" value="{{ old('content') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Term</label>
                            <input type="text" name="term" class="form-control" value="{{ old('term') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="feather-plus me-2"></i> Generate Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="card mt-4">
                <div class="card-header"><h5 class="card-title">All Links</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="border-b">
                                    <th>Source</th>
                                    <th>Label</th>
                                    <th>Generated Link</th>
                                    <th>Sessions</th>
                                    <th>Hits</th>
                                    <th>Created</th>
                                    @if($canManage)<th class="text-end">Actions</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($links as $link)
                                @php
                                    $stat = $stats[$link->source . '|' . $link->medium . '|' . $link->campaign] ?? null;
                                @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.traffic.index', ['source' => $link->source]) }}">
                                                <span class="badge bg-soft-primary text-primary">{{ $link->source }}</span>
                                            </a>
                                        </td>
                                        <td>{{ $link->label ?: '—' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <code class="fs-11 text-truncate d-inline-block" style="max-width:380px;" title="{{ $link->generated_url }}">{{ $link->generated_url }}</code>
                                                <button type="button" class="btn btn-xs btn-light"
                                                        onclick="navigator.clipboard.writeText(@js($link->generated_url))">
                                                    <i class="feather-copy"></i> Copy
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ number_format($stat->sessions ?? 0) }}</td>
                                        <td>{{ number_format($stat->hits ?? 0) }}</td>
                                        <td>{{ $link->created_at->format('M d, Y') }}</td>
                                        @if($canManage)
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('admin.tracking-links.destroy', $link->id) }}"
                                                  class="d-inline" onsubmit="return confirm('Delete this link? Recorded traffic is kept.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="feather-trash-2"></i></button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canManage ? 7 : 6 }}" class="text-center py-4 text-muted">
                                            <i class="feather-inbox fs-3 d-block mb-2"></i> No tracking links yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($links->hasPages())
                <div class="card-footer">{{ $links->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</main>

@include('admin.components.footer')