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
                    <li class="breadcrumb-item"><a href="{{ route('reseller.manage.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reseller.manage.settings') }}">Manage</a></li>
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
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
                            <form method="POST" action="{{ route('reseller.manage.settings.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Panel Logo</label>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($reseller->logo_path)
                                            <img src="{{ $reseller->logo_path }}" alt="logo" style="height:40px;">
                                        @endif
                                        <input type="file" name="logo" accept="image/*"
                                               class="form-control @error('logo') is-invalid @enderror">
                                    </div>
                                    @error('logo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">PNG, JPG, JPEG, SVG or WEBP — max 2MB. Leave empty to keep your current logo.</div>
                                </div>

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
                                    <label class="form-label">Support Email <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="email" name="support_email" class="form-control @error('support_email') is-invalid @enderror" 
                                           value="{{ old('support_email', $reseller->support_email) }}">
                                    @error('support_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Customers will see this email for support inquiries.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Support Telegram <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="text" name="support_telegram" class="form-control @error('support_telegram') is-invalid @enderror" 
                                           value="{{ old('support_telegram', $reseller->support_telegram) }}"
                                           placeholder="@yourhandle or https://t.me/yourhandle">
                                    @error('support_telegram')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Customers will see a Telegram support link on their dashboard.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Support WhatsApp <span class="text-muted fw-normal">(optional)</span></label>
                                    <input type="text" name="support_whatsapp" class="form-control @error('support_whatsapp') is-invalid @enderror" 
                                           value="{{ old('support_whatsapp', $reseller->support_whatsapp) }}"
                                           placeholder="+2348012345678">
                                    @error('support_whatsapp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Customers will see a WhatsApp support link on their dashboard.</div>
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