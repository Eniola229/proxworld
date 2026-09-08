@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Create Reseller Panel</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Reseller Panel</li>
                    <li class="breadcrumb-item">Create</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-xxl-7 col-lg-9">

                    {{-- Alert --}}
                    @if(session('alert'))
                        <div class="alert alert-{{ session('alert.type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show mb-4" role="alert">
                            {{ session('alert.message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Intro Card --}}
                    <div class="card mb-4" style="border-left: 4px solid #6366f1;">
                        <div class="card-body d-flex align-items-center gap-4 py-3">
                            <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded-circle">
                                <i class="feather-globe fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Launch Your Own Proxy Reseller Panel</h5>
                                <p class="text-muted mb-0 fs-13">
                                    Set up your branded panel, add a markup on services, and resell to your customers.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- DNS Information - For Future Reference --}}
                    <div class="alert alert-info mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="feather-info me-2"></i>Custom Domain (Optional - After Approval)
                        </h6>
                        
                        <div class="bg-white p-3 rounded">
                            <div class="alert alert-warning mb-3">
                                <i class="feather-alert-circle me-2"></i>
                                <strong>Note:</strong> You can only set up a custom domain AFTER your panel is approved. 
                                The server IP address will be shown in your dashboard after approval.
                            </div>
                            
                            <h6 class="fw-bold mb-2">What you'll need to do after approval:</h6>
                            <ol class="small mb-3">
                                <li>Go to your panel dashboard → Domain Settings</li>
                                <li>Copy the server IP address shown there</li>
                                <li>Add an A record at your domain registrar pointing to that IP</li>
                                <li>Enter your domain in the panel and verify</li>
                            </ol>
                            
                            <div class="alert alert-success mb-0 small">
                                <i class="feather-check-circle me-2"></i>
                                <strong>While waiting for approval:</strong> Your panel will be available at:<br>
                                <code>{{ $subdomainPreview ?? 'yourpanel' }}.{{ config('proxworld.base_domain') }}</code>
                            </div>
                        </div>
                    </div>
                    {{-- Form Card --}}
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Panel Setup</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reseller-panel.store') }}" method="POST">
                                @csrf

                                {{-- Panel Name --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Panel Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="panel_name"
                                           class="form-control @error('panel_name') is-invalid @enderror"
                                           placeholder="e.g. SocialBoost Pro"
                                           value="{{ old('panel_name') }}" />
                                    @error('panel_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">This will be shown to your customers as the panel brand name.</div>
                                </div>

                                {{-- Subdomain --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Subdomain <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               name="subdomain"
                                               id="subdomain-input"
                                               class="form-control @error('subdomain') is-invalid @enderror"
                                               placeholder="yourpanel"
                                               value="{{ old('subdomain') }}"
                                               oninput="updatePreview(this.value)" />
                                        <span class="input-group-text fw-semibold text-muted">.{{ config('proxworld.base_domain') }}</span>
                                        @error('subdomain')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Your panel URL: <span id="subdomain-preview" class="fw-semibold text-primary">yourpanel.{{ config('proxworld.base_domain') }}</span>
                                    </div>
                                </div>

                                {{-- Markup --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Default Markup <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number"
                                               name="default_markup_percent"
                                               class="form-control @error('default_markup_percent') is-invalid @enderror"
                                               placeholder="20"
                                               min="1"
                                               max="200"
                                               value="{{ old('default_markup_percent', 20) }}" />
                                        <span class="input-group-text">%</span>
                                        @error('default_markup_percent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text text-muted">Percentage added on top of our base price for your customers. You can change this per service later.</div>
                                </div>

                                {{-- Row: Color + Support Email --}}
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Brand Color</label>
                                        <div class="input-group">
                                            <input type="color"
                                                   name="primary_color"
                                                   class="form-control form-control-color"
                                                   value="{{ old('primary_color', '#6366f1') }}"
                                                   style="max-width: 60px;" />
                                            <input type="text"
                                                   id="color-hex"
                                                   class="form-control"
                                                   value="{{ old('primary_color', '#6366f1') }}"
                                                   readonly />
                                        </div>
                                        <div class="form-text text-muted">Used as your panel's primary colour.</div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Support Email</label>
                                        <input type="email"
                                               name="support_email"
                                               class="form-control @error('support_email') is-invalid @enderror"
                                               placeholder="support@yourpanel.com"
                                               value="{{ old('support_email', auth()->user()->email) }}" />
                                        @error('support_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text text-muted">Shown to your customers for support inquiries.</div>
                                    </div>
                                </div>

                                {{-- Info Note --}}
                                <div class="alert alert-soft-warning d-flex align-items-start gap-3 mb-4">
                                    <i class="feather-info fs-5 mt-1 text-warning"></i>
                                    <div class="fs-13">
                                        <strong>Pending Approval:</strong> Your panel will be reviewed by our team before going live. This usually takes under 24 hours.
                                    </div>
                                </div>

                                <div class="d-flex gap-3">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="feather-check me-2"></i> Create My Panel
                                    </button>
                                    <a href="{{ route('reseller-panel.index') }}" class="btn btn-light px-4">Cancel</a>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</main>

@include('components.g-footer')

<script>
    function updatePreview(val) {
        const clean = val.toLowerCase().replace(/[^a-z0-9-_]/g, '');
        const preview = (clean || 'yourpanel') + '.{{ config('proxworld.base_domain') }}';
        document.getElementById('subdomain-preview').textContent = preview;
        
        // Also update the note about pending URL
        const pendingNote = document.querySelector('.alert-warning code');
        if (pendingNote) {
            pendingNote.textContent = preview;
        }
    }

    document.querySelector('input[name="primary_color"]').addEventListener('input', function () {
        document.getElementById('color-hex').value = this.value;
    });
    
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Optional: Show a temporary tooltip
            alert('Server IP copied to clipboard!');
        });
    }
</script>