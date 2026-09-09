@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Add Provider</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.providers.index') }}">Providers</a></li>
                    <li class="breadcrumb-item">Add</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-lg-7 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">New API Provider</h5>
                        </div>
                        <div class="card-body">

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.providers.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Provider Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" placeholder="e.g. Smartproxy, Oxylabs, IPRoyal" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Driver <span class="text-danger">*</span></label>
                                    <select name="driver" id="driver-select" class="form-select @error('driver') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('driver') ? '' : 'selected' }}>Select a driver...</option>
                                        @foreach($drivers as $class => $label)
                                            <option value="{{ $class }}" {{ old('driver') === $class ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        Pick "Configurable HTTP Provider" for a standard REST API — no code or deploy needed, just fill the JSON config below.
                                        Pick a named driver only if a developer has already added a class for this exact provider.
                                    </div>
                                    @error('driver')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">API Endpoint URL <span class="text-danger">*</span></label>
                                    <input type="url" name="api_url" class="form-control @error('api_url') is-invalid @enderror"
                                           value="{{ old('api_url') }}" placeholder="https://provider.com/api/v2" required>
                                    <div class="form-text">The full base API endpoint for this proxy provider (e.g. https://api.provider.com/v1).</div>
                                    @error('api_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">API Key <span class="text-danger">*</span></label>
                                    <input type="text" name="api_key" class="form-control @error('api_key') is-invalid @enderror"
                                           value="{{ old('api_key') }}" placeholder="Your secret API key" required>
                                    @error('api_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- Only shown/required for ConfigurableHttpProviderDriver --}}
                                <div class="mb-3" id="config-field-wrapper" style="display:none;">
                                    <label class="form-label fw-bold">Driver Config (JSON) <span class="text-danger">*</span></label>
                                    <textarea name="config" id="config-field" rows="14" class="form-control font-monospace @error('config') is-invalid @enderror"
                                              placeholder='{"auth":{"type":"bearer"},"endpoints":{"products":"/products","balance":"/account/balance","orders_create":"/orders","order_status":"/orders/{id}","order_proxies":"/orders/{id}/proxies"},"request_fields":{"product_id_key":"product_id","quantity_key":"quantity"},"response_paths":{"products_list":"data","product_id":"id","product_name":"name","product_rate":"price","balance":"balance","order_id":"order_id","order_status":"status","proxies_list":"data"},"currency":"USD","supports_extend":false}'>{{ old('config') }}</textarea>
                                    <div class="form-text">
                                        Describes this provider's endpoints, auth style, and JSON field names. See <code>ConfigurableHttpProviderDriver</code> docblock for the full schema.
                                    </div>
                                    @error('config')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                        <input type="number" name="priority" class="form-control @error('priority') is-invalid @enderror"
                                               value="{{ old('priority', 1) }}" min="1" max="100" required>
                                        <div class="form-text">Lower number = tried first. Providers at the same priority are picked randomly.</div>
                                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                   id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Active (accept orders)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"
                                              placeholder="Optional admin notes...">{{ old('notes') }}</textarea>
                                </div>

                                <div class="alert alert-info fs-12">
                                    <i class="feather-info me-2"></i>
                                    After saving, the system will attempt to fetch this provider's plans/catalog automatically.
                                    Verify the provider's current API docs before going live — endpoint paths and response shapes vary between providers.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i> Save Provider
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

<script>
    (function () {
        var CONFIGURABLE_DRIVER = "App\\ProxyProviders\\Drivers\\ConfigurableHttpProviderDriver";
        var select = document.getElementById('driver-select');
        var wrapper = document.getElementById('config-field-wrapper');
        var field = document.getElementById('config-field');

        function sync() {
            var isConfigurable = select.value === CONFIGURABLE_DRIVER;
            wrapper.style.display = isConfigurable ? '' : 'none';
            field.required = isConfigurable;
        }

        select.addEventListener('change', sync);
        sync();
    })();
</script>