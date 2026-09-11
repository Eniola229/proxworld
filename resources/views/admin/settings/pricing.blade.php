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

            {{-- GLOBAL SETTINGS --}}
            <form action="{{ route('admin.settings.pricing.update') }}" method="POST">
                @csrf

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
                                    <input type="number" step="0.1" min="0" max="1000"
                                        name="default_markup"
                                        value="{{ old('default_markup', $pricing['default_markup']) }}"
                                        class="form-control @error('default_markup') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Fallback when no rule below matches</small>
                                @error('default_markup')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Minimum Markup (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="1000"
                                        name="minimum_markup"
                                        value="{{ old('minimum_markup', $pricing['minimum_markup']) }}"
                                        class="form-control @error('minimum_markup') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Never go below this, even with rules</small>
                                @error('minimum_markup')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Maximum Markup (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="1000"
                                        name="maximum_markup"
                                        value="{{ old('maximum_markup', $pricing['maximum_markup']) }}"
                                        class="form-control @error('maximum_markup') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Cap to stay competitive, even with rules</small>
                                @error('maximum_markup')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Currency Buffer (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.1" min="0" max="100"
                                        name="currency_buffer"
                                        value="{{ old('currency_buffer', $pricing['currency_buffer']) }}"
                                        class="form-control @error('currency_buffer') is-invalid @enderror">
                                    <span class="input-group-text">%</span>
                                </div>
                                <small class="text-muted">Always added on top of whichever markup wins</small>
                                @error('currency_buffer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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

                <div class="d-flex justify-content-end gap-2 mb-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-2"></i>Save Global Settings
                    </button>
                </div>
            </form>

            {{-- MARKUP RULES --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title"><i class="feather-tag me-2"></i>Markup Rules</h5>
                    <small class="text-muted">Most specific rule wins — Provider + Type beats Provider alone beats Product Type/Protocol beats the global default above</small>
                </div>
                <div class="card-body">

                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Scope</th><th>Product Type</th><th>Protocol</th><th>Provider</th>
                                    <th>Markup</th><th>Status</th><th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                    <tr>
                                        <td class="text-capitalize">{{ str_replace('_', ' ', $rule->scope_type) }}</td>
                                        <td>{{ $rule->product_type ? ucfirst($rule->product_type) : '—' }}</td>
                                        <td>{{ $rule->protocol ? strtoupper($rule->protocol) : '—' }}</td>
                                        <td>{{ $rule->provider?->name ?? '—' }}</td>
                                        <td class="fw-bold">{{ $rule->markup_percentage }}%</td>
                                        <td>
                                            <span class="badge {{ $rule->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                                {{ $rule->is_active ? 'Active' : 'Disabled' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <form method="POST" action="{{ route('admin.pricing-rules.toggle', $rule->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs {{ $rule->is_active ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $rule->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.pricing-rules.destroy', $rule->id) }}"
                                                      onsubmit="return confirm('Remove this markup rule?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-danger"><i class="feather-trash-2"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-3">No markup rules yet — every order uses the global default above.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Add a Rule</h6>
                    <form method="POST" action="{{ route('admin.pricing-rules.store') }}" class="row g-3 align-items-end">
                        @csrf

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Scope</label>
                            <select name="scope_type" id="rule-scope" class="form-select" required>
                                <option value="product_type">Product Type only</option>
                                <option value="protocol">Protocol only</option>
                                <option value="combined">Product Type + Protocol</option>
                                <option value="provider">Provider only</option>
                                <option value="provider_product_type">Provider + Product Type</option>
                            </select>
                        </div>

                        <div class="col-md-3" data-field="product_type">
                            <label class="form-label fw-semibold">Product Type</label>
                            <select name="product_type" class="form-select">
                                @foreach($productTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3" data-field="protocol">
                            <label class="form-label fw-semibold">Protocol</label>
                            <input type="text" name="protocol" class="form-control" placeholder="e.g. http, socks5">
                        </div>

                        <div class="col-md-3" data-field="provider_id">
                            <label class="form-label fw-semibold">Provider</label>
                            <select name="provider_id" class="form-select">
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Markup (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" min="0" max="1000" name="markup_percentage" class="form-control" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="notes" class="form-control" placeholder="Why this rule exists">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100"><i class="feather-plus me-2"></i>Add Rule</button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light-brand">Back to Dashboard</a>
            </div>

        </div>
    </div>
</main>

@include('admin.components.footer')

<script>
    (function () {
        var scopeToFields = {
            product_type: ['product_type'],
            protocol: ['protocol'],
            combined: ['product_type', 'protocol'],
            provider: ['provider_id'],
            provider_product_type: ['provider_id', 'product_type'],
        };
        var select = document.getElementById('rule-scope');
        var fieldWrappers = document.querySelectorAll('[data-field]');

        function sync() {
            var visible = scopeToFields[select.value] || [];
            fieldWrappers.forEach(function (el) {
                el.style.display = visible.includes(el.dataset.field) ? '' : 'none';
            });
        }

        select.addEventListener('change', sync);
        sync();
    })();
</script>