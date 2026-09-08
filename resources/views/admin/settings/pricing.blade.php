@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pricing Configuration</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Pricing Config</li>
                </ul>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>Please fix the errors below.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>PLEASE BE VERY CAREFUL HERE
            </div>

        <div class="main-content">
            <form action="{{ route('admin.settings.pricing.update') }}" method="POST">
                @csrf

                {{-- GLOBAL SETTINGS --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-settings me-2"></i>Global Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Default Markup (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100"
                                        name="default_markup"
                                        value="{{ old('default_markup', $pricing['default_markup']) }}"
                                        class="form-control @error('default_markup') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Fallback when no rule matches</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Minimum Markup (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100"
                                        name="minimum_markup"
                                        value="{{ old('minimum_markup', $pricing['minimum_markup']) }}"
                                        class="form-control @error('minimum_markup') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Never go below this</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Maximum Markup (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100"
                                        name="maximum_markup"
                                        value="{{ old('maximum_markup', $pricing['maximum_markup']) }}"
                                        class="form-control @error('maximum_markup') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Cap to stay competitive</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Currency Buffer (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="20"
                                        name="currency_buffer"
                                        value="{{ old('currency_buffer', $pricing['currency_buffer']) }}"
                                        class="form-control @error('currency_buffer') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Exchange rate buffer</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Round Prices</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="round_prices" value="1"
                                        {{ old('round_prices', $pricing['round_prices']) ? 'checked' : '' }}>
                                    <label class="form-check-label">Round to nearest whole number</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SERVICE TYPE MARKUP --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-tag me-2"></i>Service Type Markup
                            <span class="badge bg-soft-info text-info ms-2 fs-11">Priority 3</span>
                        </h5>
                        <small class="text-muted">Applied by keyword found in service name</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($pricing['service_type_markup'] as $type => $value)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <label class="form-label fw-semibold text-capitalize">{{ str_replace('_', ' ', $type) }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" min="0" max="100"
                                            name="service_type_markup[{{ $type }}]"
                                            value="{{ old('service_type_markup.'.$type, $value) }}"
                                            class="form-control">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- PLATFORM MARKUP --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-globe me-2"></i>Protocol Markup
                            <span class="badge bg-soft-warning text-warning ms-2 fs-11">Priority 2</span>
                        </h5>
                        <small class="text-muted">Applied per proxy protocol (e.g. HTTP, SOCKS5)</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($pricing['platform_markup'] as $platform => $value)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <label class="form-label fw-semibold text-capitalize">{{ str_replace('_', ' ', $platform) }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" min="0" max="100"
                                            name="platform_markup[{{ $platform }}]"
                                            value="{{ old('platform_markup.'.$platform, $value) }}"
                                            class="form-control">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- COMBINED MARKUP --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="feather-layers me-2"></i>Combined Markup (Platform + Service Type)
                            <span class="badge bg-soft-danger text-danger ms-2 fs-11">Priority 1 — Highest</span>
                        </h5>
                        <small class="text-muted">Most specific rules — checked first</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($pricing['combined_markup'] as $combo => $value)
                                <div class="col-md-3 col-sm-4 col-6">
                                    <label class="form-label fw-semibold text-capitalize">{{ str_replace('_', ' ', $combo) }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" min="0" max="100"
                                            name="combined_markup[{{ $combo }}]"
                                            value="{{ old('combined_markup.'.$combo, $value) }}"
                                            class="form-control">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="d-flex justify-content-end gap-2 mb-5">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light-brand">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>Save Pricing Configuration
                    </button>
                </div>

            </form>
        </div>
    </div>
</main>

@include('admin.components.footer')