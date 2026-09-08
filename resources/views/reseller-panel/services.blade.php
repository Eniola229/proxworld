@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Service Markups</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reseller-panel.index') }}">Reseller Panel</a></li>
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

            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Configure Service Pricing (Scroll Sideways To Edit Price Markup)</h5>
                    <div class="card-header-right">
                        <span class="badge bg-info">Default Markup: {{ $reseller->default_markup_percent }}%</span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reseller-panel.services.update') }}" id="serviceForm">
                        @csrf
                        
                        {{-- Save Button at Top with Progress Indicator --}}
                        <div class="mb-3 d-flex justify-content-end align-items-center gap-2">
                            <div id="progressIndicator" class="d-none">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="small text-muted" id="progressText">Saving 0%...</span>
                            </div>
                            <button type="submit" class="btn btn-primary px-4" id="saveButton">
                                <i class="feather-save me-2"></i> Save All Changes
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    
                                        <th>Service ID</th>
                                        <th>Service Name</th>
                                        <th>Base Rate (₦/1k)</th>
                                        <th>Markup (%)</th>
                                        <th>Your Price (₦/1k)</th>
                                        <th>Hidden</th>
                                    </thead>
                                <tbody>
                               @foreach($services as $service)
                                @php
                                    // Check if service is array or object
                                    $serviceId = is_array($service) ? ($service['service'] ?? $service['id'] ?? null) : ($service->service ?? $service->id ?? null);
                                    $serviceName = is_array($service) ? ($service['name'] ?? $service['service_name'] ?? 'Unknown') : ($service->name ?? $service->service_name ?? 'Unknown');
                                    $serviceRate = is_array($service) ? ($service['rate'] ?? $service['price'] ?? 0) : ($service->rate ?? $service->price ?? 0);
                                    
                                    $override = $overrides[$serviceId] ?? null;
                                    $markup = $override ? $override->markup_percent : $reseller->default_markup_percent;
                                    $price = $serviceRate * (1 + ($markup / 100));
                                    $isHidden = $override ? $override->is_hidden : false;
                                @endphp
                                <tr>
                                    <td>{{ $serviceId }}</td>
                                    <td>{{ $serviceName }}</td>
                                    <td>₦{{ number_format($serviceRate, 2) }}</td>
                                    <td>
                                        <input type="hidden" name="markups[{{ $loop->index }}][service_id]" value="{{ $serviceId }}">
                                        <input type="number" name="markups[{{ $loop->index }}][markup]" step="0.01" 
                                               class="form-control markup-input" style="width: 100px;" 
                                               value="{{ $markup }}">
                                    </td>
                                    <td class="price-display">₦{{ number_format($price, 2) }}</td>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" name="markups[{{ $loop->index }}][hidden]" 
                                                   class="form-check-input" value="1" {{ $isHidden ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Save Button at Bottom --}}
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="feather-save me-2"></i> Save Markups
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')

<script>
// Real-time price calculation when markup changes
document.querySelectorAll('.markup-input').forEach(input => {
    input.addEventListener('input', function() {
        const row = this.closest('tr');
        const baseRateText = row.cells[2].innerText.replace('₦', '').replace(',', '');
        const baseRate = parseFloat(baseRateText);
        const markup = parseFloat(this.value) || 0;
        const price = baseRate * (1 + (markup / 100));
        row.querySelector('.price-display').innerText = '₦' + price.toFixed(2);
    });
});

// Form submission with progress indicator
document.getElementById('serviceForm').addEventListener('submit', function(e) {
    const saveButtons = document.querySelectorAll('button[type="submit"]');
    const progressIndicator = document.getElementById('progressIndicator');
    const progressText = document.getElementById('progressText');
    const totalServices = {{ count($services) }};
    
    // Disable all submit buttons
    saveButtons.forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="feather-loader me-2"></i> Saving...';
    });
    
    // Show progress indicator
    progressIndicator.classList.remove('d-none');
    
    // Simulate progress (since actual progress can't be tracked without AJAX)
    let progress = 0;
    const interval = setInterval(() => {
        progress += 5;
        if (progress <= 90) {
            progressText.textContent = `Saving ${progress}%...`;
        }
        if (progress >= 100) {
            clearInterval(interval);
        }
    }, 100);
    
    // Allow form to submit normally
    // The progress indicator will show until page redirects
    setTimeout(() => {
        clearInterval(interval);
        progressText.textContent = 'Processing complete!';
    }, 5000);
});
</script>

<style>
.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.feather-loader {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.markup-input {
    transition: all 0.2s ease;
}

.markup-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}
</style>