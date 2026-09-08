@include('reseller.components.g-header')
@include('reseller.components.nav')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">New Order</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">New Order</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
                    <div class="toast show bg-white shadow-lg border-0" role="alert">
                        <div class="toast-header">
                            <strong class="me-auto text-uppercase">{{ session('alert')['type'] }}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">{{ session('alert')['message'] }}</div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Buy Proxies</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('reseller.order.store') }}" id="order-form">
                                @csrf

                                <!-- Proxy Type Selection -->
                                <div class="mb-4">
                                    <label class="form-label mb-3">Select Proxy Type</label>
                                    <div class="d-flex flex-wrap gap-3" id="type-container">
                                        @foreach($groupedServices as $type => $servicesOfType)
                                            <button type="button"
                                                    class="btn type-btn btn-outline-secondary d-flex flex-column align-items-center justify-content-center"
                                                    style="width: 110px; height: 80px; border-radius: 12px; padding: 10px 6px;"
                                                    data-type="{{ $type }}"
                                                    onclick="selectType('{{ $type }}')">
                                                <i class="{{ match($type) {
                                                    'residential' => 'fas fa-house-user',
                                                    'datacenter' => 'fas fa-server',
                                                    'isp' => 'fas fa-network-wired',
                                                    'mobile' => 'fas fa-mobile-screen',
                                                    default => 'fas fa-globe',
                                                } }} fa-lg" style="margin-bottom: 6px;"></i>
                                                <small class="mt-1" style="font-size: 11px; text-transform: capitalize;">{{ $type }}</small>
                                            </button>
                                        @endforeach
                                    </div>
                                    @if($groupedServices->isEmpty())
                                        <div class="alert alert-warning mt-3 mb-0">
                                            No proxy plans are available right now.
                                        </div>
                                    @endif
                                </div>

                                <!-- Service Selection — searchable dropdown -->
                                <div class="mb-3 position-relative" id="service-wrapper" style="display:none;">
                                    <label class="form-label">Select Plan</label>
                                    <input type="text"
                                           id="service_search"
                                           class="form-control"
                                           placeholder="Type to search plans..."
                                           autocomplete="off"
                                           disabled />
                                    <input type="hidden" name="service_id" id="service_id" required>

                                    <div id="service_dropdown"
                                         class="list-group position-absolute w-100 shadow-sm"
                                         style="max-height: 260px; overflow-y: auto; z-index: 1000; display: none;"></div>

                                    <div class="form-text" id="service_info"></div>
                                </div>

                                <!-- How to order guide -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="collapse" data-bs-target="#orderDescription">
                                        <i class="fas fa-info-circle me-1"></i> How to Place Orders
                                    </button>
                                    <div class="collapse mt-3" id="orderDescription">
                                        <div class="card card-body bg-light">
                                            <p class="mb-2">Welcome to {{ $reseller->panel_name }}. Pick a proxy type, choose a plan, and enter your quantity.</p>
                                            <h6 class="fw-bold mb-2">How to get started</h6>
                                            <ol class="mb-3">
                                                <li>Select a proxy type</li>
                                                <li>Search and select the exact plan</li>
                                                <li>Enter a quantity and place your order</li>
                                            </ol>
                                            @if($reseller->support_email)
                                                <p class="mb-0">Need help? Email us at <a href="mailto:{{ $reseller->support_email }}">{{ $reseller->support_email }}</a></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="service_name" id="service_name">

                                <div class="mb-3">
                                    <label class="form-label">Quantity <span id="quantity_unit_label"></span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control"
                                           placeholder="Select a plan first" disabled min="1" oninput="calculateTotal()">
                                    <div class="form-text" id="quantity_info">Select a plan to see pricing.</div>
                                </div>

                                <div class="alert alert-info d-flex justify-content-between align-items-center">
                                    <span>Total Charge:</span>
                                    <h4 class="m-0 fw-bold text-primary">₦<span id="total_charge">0.00</span></h4>
                                </div>

                                <input type="hidden" name="charge" id="charge" value="0">

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
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
const PANEL_NAME = @json($reseller->panel_name);
// groupedData: { residential: [{id, name, price, unit, provider}, ...], datacenter: [...], ... }
const groupedData = {!! json_encode($groupedServices->map(fn ($services) => $services->map(fn ($s) => [
    'id' => $s->id,
    'name' => $s->name,
    'provider' => $s->provider?->name ?? 'Provider',
    'price' => $s->display_price,
    'unit' => $s->unit,
]))) !!};

let currentServices = []; // plans in the currently selected proxy type

function resetServiceStage() {
    document.getElementById('service_search').value = '';
    document.getElementById('service_search').disabled = true;
    document.getElementById('service_id').value = '';
    document.getElementById('service_name').value = '';
    document.getElementById('service_dropdown').style.display = 'none';
    document.getElementById('service_dropdown').innerHTML = '';
    document.getElementById('service_info').innerHTML = '';

    const qty = document.getElementById('quantity');
    qty.disabled = true;
    qty.value = '';
    document.getElementById('quantity_unit_label').textContent = '';
    document.getElementById('quantity_info').innerHTML = 'Select a plan to see pricing.';
    updateTotalDisplay(0);
}

function selectType(type) {
    document.querySelectorAll('.type-btn').forEach(btn => {
        btn.classList.toggle('btn-primary', btn.dataset.type === type);
        btn.classList.toggle('btn-outline-secondary', btn.dataset.type !== type);
    });

    currentServices = groupedData[type] || [];

    document.getElementById('service-wrapper').style.display = '';
    document.getElementById('service_search').disabled = false;
    resetServiceStage();
    document.getElementById('service_search').disabled = false;
    renderServiceDropdown(currentServices);
}

function renderServiceDropdown(services) {
    const dropdown = document.getElementById('service_dropdown');
    dropdown.innerHTML = '';

    if (services.length === 0) {
        dropdown.innerHTML = '<div class="list-group-item text-muted">No plans found.</div>';
        return;
    }

    services.forEach(service => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'list-group-item list-group-item-action service-option';
        item.innerHTML = `<div class="d-flex justify-content-between">
                             <span>${service.name} <small class="text-muted">(${service.provider})</small></span>
                             <span class="text-primary fw-semibold">₦${parseFloat(service.price).toFixed(2)}/${service.unit}</span>
                           </div>`;

        item.addEventListener('click', () => chooseService(service));
        dropdown.appendChild(item);
    });
}

function chooseService(service) {
    document.getElementById('service_search').value = service.name;
    document.getElementById('service_id').value      = service.id;
    document.getElementById('service_name').value    = service.name;
    document.getElementById('service_dropdown').style.display = 'none';

    const qty = document.getElementById('quantity');
    qty.disabled = false;
    qty.min = 1;
    qty.placeholder = 'e.g. 5';
    qty.value = '';

    document.getElementById('quantity_unit_label').textContent = '(in ' + service.unit + ')';
    document.getElementById('service_info').innerHTML =
        `Provider: <strong>${service.provider}</strong> &nbsp;|&nbsp; Priced per <strong>${service.unit}</strong>`;
    document.getElementById('quantity_info').innerHTML =
        `Total = quantity &times; ₦${parseFloat(service.price).toFixed(2)} per ${service.unit}.`;

    calculateTotal();
}

// Search-as-you-type inside the currently selected type
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('service_search');
    const dropdown     = document.getElementById('service_dropdown');

    searchInput.addEventListener('focus', () => {
        if (currentServices.length > 0) {
            renderServiceDropdown(currentServices);
            dropdown.style.display = 'block';
        }
    });

    searchInput.addEventListener('input', () => {
        document.getElementById('service_id').value = '';
        document.getElementById('service_name').value = '';

        const query = searchInput.value.toLowerCase().trim();
        const filtered = currentServices.filter(service =>
            service.name.toLowerCase().includes(query)
        );

        renderServiceDropdown(filtered);
        dropdown.style.display = 'block';
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#service_search') && !e.target.closest('#service_dropdown')) {
            dropdown.style.display = 'none';
        }
    });
});

function calculateTotal() {
    const serviceId = document.getElementById('service_id').value;
    if (!serviceId) { updateTotalDisplay(0); return; }

    const service = currentServices.find(s => String(s.id) === String(serviceId));
    if (!service) { updateTotalDisplay(0); return; }

    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const rate     = parseFloat(service.price) || 0;

    updateTotalDisplay(quantity * rate);
}

function updateTotalDisplay(total) {
    const formatted = total.toFixed(2);
    document.getElementById('total_charge').innerText = formatted;
    document.getElementById('charge').value           = formatted;
}

// Guard against submitting without an actual plan selected
document.getElementById('order-form').addEventListener('submit', function (e) {
    if (!document.getElementById('service_id').value) {
        e.preventDefault();
        alert('Please select a plan from the dropdown before placing your order.');
    }
});
</script>
