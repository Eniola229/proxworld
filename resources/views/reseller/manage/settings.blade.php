@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Panel Settings</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/manage/settings">Manage</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show" role="alert">
                    {{ session('alert')['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Panel Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/manage/settings">
                                @csrf
                                
                                <div class="mb-3">
                                    <label class="form-label">Panel Name</label>
                                    <input type="text" name="panel_name" class="form-control @error('panel_name') is-invalid @enderror" 
                                           value="{{ old('panel_name', $reseller->panel_name) }}" required>
                                    @error('panel_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This name will be shown to your customers.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <div class="input-group">
                                        <input type="color" name="primary_color" class="form-control form-control-color" 
                                               value="{{ old('primary_color', $reseller->primary_color) }}" 
                                               style="max-width: 60px;" required>
                                        <input type="text" id="color-hex" class="form-control" 
                                               value="{{ old('primary_color', $reseller->primary_color) }}" readonly>
                                    </div>
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Choose your panel's primary brand color.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Support Email</label>
                                    <input type="email" name="support_email" class="form-control @error('support_email') is-invalid @enderror" 
                                           value="{{ old('support_email', $reseller->support_email) }}">
                                    @error('support_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Customers will see this email for support inquiries.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Default Markup Percentage (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="default_markup_percent" step="0.01" 
                                               class="form-control @error('default_markup_percent') is-invalid @enderror" 
                                               value="{{ old('default_markup_percent', $reseller->default_markup_percent) }}" 
                                               min="0" max="200" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('default_markup_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This markup will be applied to all services by default. You can override per service.</div>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="feather-info me-2"></i>
                                    <strong>Subdomain:</strong> {{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}
                                    <br>
                                    <small class="text-muted">Subdomain cannot be changed. Contact support if you need to change it.</small>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="feather-save me-2"></i> Save Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')

<script>
document.querySelector('input[name="primary_color"]').addEventListener('input', function() {
    document.getElementById('color-hex').value = this.value;
});
</script>