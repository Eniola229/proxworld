@include('reseller.components.g-header')
@include('reseller.components.nav')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Service Markups</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('reseller.manage.index') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reseller.manage.settings') }}">Manage</a></li>
                    <li class="breadcrumb-item">Services</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show" role="alert">
                    {{ session('alert')['message'] }}
                    @if(isset(session('alert')['total_services']))
                        <div class="mt-2">
                            <i class="feather-check-circle text-success me-1"></i>
                            <small>{{ session('alert')['total_services'] }} services updated successfully.</small>
                        </div>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                // Group by proxy type for the tabbed view. $service->type comes
                // straight from ProviderServiceCache (residential/datacenter/isp/mobile/etc).
                $groupedServices = $services->groupBy(fn ($service) => $service->type ?? 'other');

                $typeIcons = [
                    'residential' => 'fa-house-user',
                    'datacenter' => 'fa-server',
                    'isp' => 'fa-network-wired',
                    'mobile' => 'fa-mobile-screen',
                    'other' => 'fa-globe',
                ];
            @endphp

            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Configure Service Pricing</h5>
                    <div class="card-header-right">
                        <span class="badge bg-info">Default Markup: {{ $reseller->default_markup_percent }}%</span>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Default markup — separate field, saved on every submit regardless of active tab --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Default Markup (%)</label>
                            <div class="input-group">
                                <input type="number" name="default_markup_percent" step="0.01" min="0" max="200"
                                       class="form-control @error('default_markup_percent') is-invalid @enderror"
                                       value="{{ old('default_markup_percent', $reseller->default_markup_percent) }}"
                                       form="serviceForm" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('default_markup_percent')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Applied to any plan without its own override below.</div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-4" id="type-tab-container">
                        @foreach($groupedServices as $type => $group)
                            <button type="button"
                                    class="btn type-tab-btn {{ $loop->first ? 'btn-primary' : 'btn-outline-secondary' }}"
                                    data-type="{{ $type }}"
                                    onclick="selectMarkupType('{{ $type }}')">
                                <i class="fas {{ $typeIcons[$type] ?? 'fa-globe' }} me-2"></i>
                                {{ ucfirst($type) }}
                                <span class="badge bg-light text-dark ms-2">{{ $group->count() }}</span>
                            </button>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('reseller.manage.services.update') }}" id="serviceForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 d-flex justify-content-end align-items-center gap-2">
                            <button type="submit" class="btn btn-primary px-4 ajax-save-btn">
                                <i class="feather-save me-2"></i> Save All Changes
                            </button>
                        </div>

                        @foreach($groupedServices as $type => $group)
                            <div class="type-service-table" data-type="{{ $type }}" style="{{ $loop->first ? '' : 'display: none;' }}">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th>Base Rate (₦/1k)</th>
                                                <th>Markup (%)</th>
                                                <th>Your Price (₦/1k)</th>
                                                <th>Hidden</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($group as $service)
                                            @php
                                                $key = $service->provider_id . '_' . $service->external_service_id;
                                                $override = $overrides[$key] ?? null;
                                                $markup = (float) $reseller->markupPercentFor($service->provider_id, $service->external_service_id);
                                                $isHidden = $override?->is_hidden ?? false;
                                            @endphp
                                            <tr>
                                                <td>{{ $service->name }}</td>
                                                <td class="base-rate">₦{{ number_format($service->base_price, 2) }}</td>
                                                <td>
                                                    <input type="number" name="markups[{{ $key }}]" step="0.01"
                                                           class="form-control markup-input" style="width: 100px;"
                                                           value="{{ $markup }}"
                                                           placeholder="Default">
                                                </td>
                                                <td class="price-display">₦{{ number_format($service->your_price, 2) }}</td>
                                                <td>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="hidden[]" value="{{ $key }}"
                                                               class="form-check-input" {{ $isHidden ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 ajax-save-btn">
                                <i class="feather-save me-2"></i> Save Markups
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- Loading overlay --}}
<div id="ajaxLoadingOverlay" class="ajax-modal-overlay d-none">
    <div class="ajax-modal-box">
        <div class="ajax-modal-spinner"></div>
        <div class="ajax-progress-track">
            <div id="ajaxProgressBarFill" class="ajax-progress-fill" style="width: 0%;"></div>
        </div>
        <div id="ajaxProgressText" class="ajax-modal-text">Saving 0%...</div>
    </div>
</div>

{{-- Success overlay --}}
<div id="ajaxSuccessOverlay" class="ajax-modal-overlay d-none">
    <div class="ajax-modal-box">
        <div class="ajax-modal-success-icon"><i class="fas fa-check"></i></div>
        <div id="ajaxSuccessText" class="ajax-modal-text ajax-modal-text-strong">Saved successfully.</div>
        <button type="button" class="btn btn-primary btn-sm mt-3" id="ajaxSuccessCloseBtn">Close</button>
    </div>
</div>

@include('reseller.components.g-footer')

<script>
function selectMarkupType(type) {
    document.querySelectorAll('.type-tab-btn').forEach(btn => {
        const active = btn.dataset.type === type;
        btn.classList.toggle('btn-primary', active);
        btn.classList.toggle('btn-outline-secondary', !active);
    });
    document.querySelectorAll('.type-service-table').forEach(table => {
        table.style.display = table.dataset.type === type ? '' : 'none';
    });
}

document.querySelectorAll('.markup-input').forEach(input => {
    input.addEventListener('input', function() {
        const row = this.closest('tr');
        const baseRateText = row.querySelector('.base-rate').innerText.replace('₦', '').replace(/,/g, '');
        const baseRate = parseFloat(baseRateText);
        const markup = parseFloat(this.value) || 0;
        const price = baseRate * (1 + (markup / 100));
        row.querySelector('.price-display').innerText = '₦' + price.toFixed(2);
    });
});

(function() {
    const form = document.getElementById('serviceForm');
    const loadingOverlay = document.getElementById('ajaxLoadingOverlay');
    const successOverlay = document.getElementById('ajaxSuccessOverlay');
    const progressBarFill = document.getElementById('ajaxProgressBarFill');
    const progressText = document.getElementById('ajaxProgressText');
    const successText = document.getElementById('ajaxSuccessText');
    const saveButtons = document.querySelectorAll('.ajax-save-btn');

    function setButtonsDisabled(disabled) {
        saveButtons.forEach(btn => { btn.disabled = disabled; });
    }

    function showLoading() {
        progressBarFill.style.width = '0%';
        progressText.textContent = 'Saving 0%...';
        loadingOverlay.classList.remove('d-none');
    }

    function hideLoading() {
        loadingOverlay.classList.add('d-none');
    }

    function showSuccess(message) {
        successText.textContent = message;
        successOverlay.classList.remove('d-none');
    }

    function hideSuccess() {
        successOverlay.classList.add('d-none');
    }

    document.getElementById('ajaxSuccessCloseBtn').addEventListener('click', hideSuccess);

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const xhr = new XMLHttpRequest();

        // Method spoofing via the @method('PUT') hidden field — actual verb stays POST.
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        setButtonsDisabled(true);
        showLoading();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                progressBarFill.style.width = percent + '%';
                progressText.textContent = 'Saving ' + percent + '%...';
            }
        });

        xhr.upload.addEventListener('load', function() {
            progressBarFill.style.width = '100%';
            progressText.textContent = 'Processing...';
        });

        xhr.onload = function() {
            setButtonsDisabled(false);
            hideLoading();

            let response = null;
            try { response = JSON.parse(xhr.responseText); } catch (err) { /* not JSON */ }

            if (xhr.status >= 200 && xhr.status < 300 && response && response.success) {
                let message = response.message || 'Saved successfully.';
                if (typeof response.total_services !== 'undefined') {
                    message += ' (' + response.total_services + ' services updated)';
                }
                showSuccess(message);
            } else if (response && response.errors) {
                alert(Object.values(response.errors).flat().join('\n'));
            } else if (response && response.message) {
                alert(response.message);
            } else {
                alert('Something went wrong while saving. Please try again.');
            }
        };

        xhr.onerror = function() {
            setButtonsDisabled(false);
            hideLoading();
            alert('Network error. Please check your connection and try again.');
        };

        xhr.send(formData);
    });
})();
</script>

<style>
.btn-primary:disabled { opacity: 0.7; cursor: not-allowed; }
.markup-input { transition: all 0.2s ease; }
.markup-input:focus { border-color: #6366f1; box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25); }

.ajax-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 18, 25, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}
.ajax-modal-box {
    background: #fff;
    border-radius: 12px;
    padding: 32px 36px;
    min-width: 280px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
}
.ajax-modal-spinner {
    width: 38px;
    height: 38px;
    margin: 0 auto 16px;
    border: 4px solid #e5e7eb;
    border-top-color: #6366f1;
    border-radius: 50%;
    animation: ajax-spin 0.8s linear infinite;
}
@keyframes ajax-spin { to { transform: rotate(360deg); } }
.ajax-progress-track {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}
.ajax-progress-fill {
    height: 100%;
    background: #6366f1;
    transition: width 0.15s ease;
}
.ajax-modal-text { color: #4b5563; font-size: 14px; }
.ajax-modal-text-strong { color: #111827; font-size: 15px; font-weight: 600; }
.ajax-modal-success-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 14px;
    background: #22c55e;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
</style>