@include('components.g-header')
@include('components.nav')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ── Custom Service Picker ─────────────────────────────────────────── */
.service-picker-wrapper { position: relative; }

.service-picker-trigger {
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 10px 14px !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    background-image: none !important;
    cursor: pointer !important;
    font-size: 14px !important;
    color: #212529 !important;
    text-align: left !important;
    box-shadow: none !important;
    transition: border-color 0.15s, box-shadow 0.15s !important;
    line-height: 1.5 !important;
    appearance: none !important;
    -webkit-appearance: none !important;
}
.service-picker-trigger:hover,
.service-picker-trigger:focus {
    border-color: #adb5bd !important;
    outline: none !important;
    box-shadow: none !important;
    background: #ffffff !important;
    color: #212529 !important;
}
.service-picker-trigger.open {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.12) !important;
    border-radius: 8px 8px 0 0 !important;
    background: #ffffff !important;
}
.service-picker-trigger.no-type {
    cursor: default !important;
    color: #6c757d !important;
    background: #ffffff !important;
}
.service-picker-trigger .trigger-text {
    flex: 1 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    display: block !important;
    color: #212529 !important;
}
.service-picker-trigger .trigger-text.placeholder { color: #adb5bd !important; }
.service-picker-trigger .trigger-meta {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    margin-left: 10px !important;
}
.service-picker-trigger .trigger-price {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #0d6efd !important;
    background: #e7f1ff !important;
    padding: 2px 8px !important;
    border-radius: 20px !important;
    white-space: nowrap !important;
}
.service-picker-trigger .trigger-chevron {
    font-size: 11px !important;
    color: #6c757d !important;
    transition: transform 0.2s;
    flex-shrink: 0 !important;
}
.service-picker-trigger.open .trigger-chevron { transform: rotate(180deg); }

/* Dropdown panel */
.service-picker-panel {
    display: none;
    position: absolute;
    left: 0; right: 0; top: 100%;
    z-index: 1000;
    background: #fff;
    border: 1px solid #0d6efd;
    border-top: none;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.10);
    overflow: hidden;
}
.service-picker-panel.open {
    display: block;
    animation: spSlideIn 0.15s ease;
}
@keyframes spSlideIn {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Search bar */
.service-picker-search {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-bottom: 1px solid #f0f0f0;
    background: #f8f9fa;
}
.service-picker-search i { color: #adb5bd; font-size: 13px; flex-shrink: 0; }
.service-picker-search input {
    flex: 1; border: none; background: transparent;
    outline: none; font-size: 13px; color: #212529;
}
.service-picker-search input::placeholder { color: #adb5bd; }
.sp-count { font-size: 11px; color: #adb5bd; flex-shrink: 0; }

/* List */
.service-picker-list {
    max-height: 300px;
    overflow-y: auto;
    overscroll-behavior: contain;
}
.service-picker-list::-webkit-scrollbar { width: 5px; }
.service-picker-list::-webkit-scrollbar-track { background: #f8f9fa; }
.service-picker-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 10px; }

/* Each item */
.sp-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 11px 14px;
    cursor: pointer;
    transition: background 0.1s;
    border-bottom: 1px solid #f5f5f5;
}
.sp-item:last-child { border-bottom: none; }
.sp-item:hover { background: #f0f7ff; }
.sp-item.selected { background: #e7f1ff; }
.sp-item-name {
    font-size: 13px; color: #212529; flex: 1; line-height: 1.4;
}
.sp-item-provider {
    font-size: 11px; color: #adb5bd; display: block; margin-top: 2px;
}
.sp-item-name mark {
    background: #fff3cd; color: #212529; border-radius: 2px; padding: 0 1px;
}
.sp-item-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.sp-item-price {
    font-size: 12px; font-weight: 600; color: #0d6efd;
    background: #e7f1ff; padding: 3px 9px;
    border-radius: 20px; white-space: nowrap;
}
.sp-item.selected .sp-item-price { background: #0d6efd; color: #fff; }
.sp-item-check { font-size: 12px; color: #0d6efd; display: none; }
.sp-item.selected .sp-item-check { display: block; }
.sp-empty { padding: 24px; text-align: center; color: #adb5bd; font-size: 13px; }
.sp-empty i { display: block; font-size: 22px; margin-bottom: 6px; }
</style>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">New Order</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Order</li>
                    <li class="breadcrumb-item">New</li>
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
                            <form method="POST" action="{{ route('order.store') }}">
                                @csrf

                                <!-- 1. Proxy Type Selection -->
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
                                            No proxy plans are available right now — check back shortly, or contact
                                            <a href="{{ route('support.index') }}">support</a>.
                                        </div>
                                    @endif
                                </div>

                                <!-- 2. Provider Selection (shown only when a type has more than one provider) -->
                                <div class="mb-3" id="provider-section" style="display: none;">
                                    <label class="form-label mb-2">Select Provider</label>
                                    <div class="d-flex flex-wrap gap-2" id="provider-container"></div>
                                    <div class="form-text mt-1">Same proxy type, different suppliers — pick whichever fits your budget.</div>
                                </div>

                                <!-- 3. Service/Plan Selection — Custom Picker -->
                                <div class="mb-3">
                                    <label class="form-label">Select Plan</label>

                                    <!-- Hidden real select (submitted with form) -->
                                    <select name="service_id" id="service_id" style="display:none;" required>
                                        <option value="">-- select --</option>
                                    </select>

                                    <!-- Custom picker UI -->
                                    <div class="service-picker-wrapper" id="service-picker-wrapper">
                                        <button type="button" class="service-picker-trigger no-type" id="sp-trigger" onclick="spToggle()">
                                            <span class="trigger-text placeholder" id="sp-trigger-text">👆 Select a proxy type first</span>
                                            <div class="trigger-meta">
                                                <span class="trigger-price" id="sp-trigger-price" style="display:none;"></span>
                                                <i class="fas fa-chevron-down trigger-chevron"></i>
                                            </div>
                                        </button>
                                        <div class="service-picker-panel" id="sp-panel">
                                            <div class="service-picker-search">
                                                <i class="fas fa-search"></i>
                                                <input type="text" id="sp-search" placeholder="Search plans…" oninput="spSearch(this.value)" autocomplete="off">
                                                <span class="sp-count" id="sp-count"></span>
                                            </div>
                                            <div class="service-picker-list" id="sp-list"></div>
                                        </div>
                                    </div>

                                    <div class="form-text mt-1" id="service_info"></div>
                                </div>

                                <!-- Description Button -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#orderDescription">
                                        <i class="fas fa-info-circle me-1"></i> How Proxy Orders Work
                                    </button>
                                    <div class="collapse mt-3" id="orderDescription">
                                        <div class="card card-body bg-light">
                                            <p class="mb-2">Pick a proxy type, choose a plan, and enter the quantity you need — {{ __('e.g. number of GB of bandwidth, or number of IPs, depending on the plan.') }}</p>
                                            <p class="mb-3">Once your order is placed, we provision it with the provider automatically. Your proxy credentials (host, port, username, password) will show up on the order page as soon as it's ready — usually within a few minutes.</p>
                                            <h6 class="fw-bold mb-2">Proxy types</h6>
                                            <ul class="mb-3">
                                                <li><strong>Residential</strong> — IPs from real home internet connections. Best for avoiding blocks.</li>
                                                <li><strong>Datacenter</strong> — Fast, cheap IPs hosted in data centers. Best for speed and cost.</li>
                                                <li><strong>ISP</strong> — Datacenter speed with residential-looking IPs.</li>
                                                <li><strong>Mobile</strong> — IPs from real mobile carrier networks.</li>
                                            </ul>
                                            <p class="mb-0">💬 Got questions before ordering? <a href="{{ route('support.index') }}" class="fw-bold">Chat our support</a> — we reply fast.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden Service Name -->
                                <input type="hidden" name="service_name" id="service_name">

                                <!-- Quantity -->
                                <div class="mb-3">
                                    <label class="form-label">Quantity <span id="quantity_unit_label"></span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Select a plan first" disabled min="1" oninput="calculateTotal()">
                                    <div class="form-text" id="quantity_info">Select a plan to see pricing.</div>
                                </div>

                                <!-- Total Charge Display -->
                                <div class="d-flex justify-content-between align-items-center mb-3 px-3 py-3 rounded-3"
                                     id="charge_alert"
                                     style="border: 1px solid #f7c1c1; background: #fcebeb; transition: all 0.3s ease;">
                                    <div>
                                        <div id="charge_status_text" style="font-size: 13px; font-weight: 500; color: #a32d2d;">
                                            <i class="fas fa-lock me-1"></i> Select a plan and quantity
                                        </div>
                                        <small id="charge_hint" style="color: #888;"></small>
                                    </div>
                                    <div class="text-end">
                                        <div style="font-size: 11px; color: #888; margin-bottom: 2px;">Total charge</div>
                                        <h4 class="m-0 fw-bold" id="total_charge_display" style="color: #a32d2d; transition: color 0.3s ease;">
                                            ₦<span id="total_charge">0.00</span>
                                        </h4>
                                    </div>
                                </div>

                                <input type="hidden" name="charge" id="charge" value="0">

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" id="submit_btn" class="btn btn-lg btn-secondary" disabled
                                            style="transition: all 0.3s ease;">
                                        <i class="fas fa-lock me-2"></i> Place Order
                                    </button>
                                </div>

                                @error('service_id')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror

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
// groupedData: { residential: [{id, name, price, unit, provider}, ...], datacenter: [...], ... }
const groupedData = {!! json_encode($groupedServices->map(fn ($services) => $services->map(fn ($s) => [
    'id' => $s->id,
    'name' => $s->name,
    'provider' => $s->provider?->name ?? 'Provider',
    'price' => $s->display_price,
    'unit' => $s->unit,
]))) !!};

let currentType      = null;
let currentProvider  = null;
let currentServiceId = null;
let spServices       = [];
let spOpen           = false;
let allTypeServices   = []; // full list for the current type, before provider filter

// ── Highlight search match ────────────────────────────────────────────────────
function highlight(text, query) {
    if (!query) return escHtml(text);
    const esc = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return escHtml(text).replace(new RegExp('(' + esc + ')', 'gi'), '<mark>$1</mark>');
}
function escHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Picker: open / close ──────────────────────────────────────────────────────
function spToggle() {
    if (document.getElementById('sp-trigger').classList.contains('no-type')) {
        const typeContainer = document.getElementById('type-container');
        typeContainer.style.transition = 'transform 0.1s';
        typeContainer.style.transform = 'translateX(6px)';
        setTimeout(function(){ typeContainer.style.transform = 'translateX(-6px)'; }, 100);
        setTimeout(function(){ typeContainer.style.transform = 'translateX(4px)'; },  200);
        setTimeout(function(){ typeContainer.style.transform = 'translateX(0)'; },    300);
        return;
    }
    spOpen ? spClose() : spOpen_();
}
function spOpen_() {
    spOpen = true;
    document.getElementById('sp-trigger').classList.add('open');
    document.getElementById('sp-panel').classList.add('open');
    const search = document.getElementById('sp-search');
    search.value = '';
    spRenderItems('');
    setTimeout(function(){ search.focus(); }, 50);
}
function spClose() {
    spOpen = false;
    document.getElementById('sp-trigger').classList.remove('open');
    document.getElementById('sp-panel').classList.remove('open');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('service-picker-wrapper');
    if (wrapper && !wrapper.contains(e.target)) spClose();
});

// ── Render picker list ────────────────────────────────────────────────────────
function spRenderItems(query) {
    const list = document.getElementById('sp-list');
    const countEl = document.getElementById('sp-count');
    const q = (query || '').toLowerCase().trim();
    const filtered = q ? spServices.filter(function(s){ return s.name.toLowerCase().includes(q); }) : spServices;
    countEl.textContent = filtered.length + ' plan' + (filtered.length !== 1 ? 's' : '');
    if (filtered.length === 0) {
        list.innerHTML = '<div class="sp-empty"><i class="fas fa-search-minus"></i>No plans match "' + escHtml(query) + '"</div>';
        return;
    }
    list.innerHTML = filtered.map(function(s) {
        const sel = s.id == currentServiceId ? 'selected' : '';
        return '<div class="sp-item ' + sel + '" data-id="' + s.id + '" onclick="spSelect(\'' + s.id + '\')">'
            + '<span class="sp-item-name">' + highlight(s.name, query) + '<span class="sp-item-provider">' + escHtml(s.provider) + '</span></span>'
            + '<div class="sp-item-right">'
            + '<span class="sp-item-price">&#8358;' + parseFloat(s.price).toFixed(2) + '/' + escHtml(s.unit) + '</span>'
            + '<i class="fas fa-check sp-item-check"></i>'
            + '</div></div>';
    }).join('');
}
function spSearch(val) { spRenderItems(val); }

// ── Select a plan ─────────────────────────────────────────────────────────────
function spSelect(serviceId) {
    const service = spServices.find(function(s){ return s.id == serviceId; });
    if (!service) return;
    currentServiceId = serviceId;

    document.getElementById('service_id').value = serviceId;
    document.getElementById('service_name').value = service.name;

    const triggerText  = document.getElementById('sp-trigger-text');
    const triggerPrice = document.getElementById('sp-trigger-price');
    triggerText.textContent = service.name + ' (' + service.provider + ')';
    triggerText.classList.remove('placeholder');
    triggerPrice.textContent = '\u20a6' + parseFloat(service.price).toFixed(2) + '/' + service.unit;
    triggerPrice.style.display = '';

    document.getElementById('service_info').innerHTML =
        'Provider: <strong>' + escHtml(service.provider) + '</strong>&nbsp;&nbsp;|&nbsp;&nbsp;Priced per <strong>' + escHtml(service.unit) + '</strong>';

    const qty = document.getElementById('quantity');
    qty.disabled = false;
    qty.min = 1;
    qty.placeholder = 'e.g. 5';
    qty.value = '';

    document.getElementById('quantity_unit_label').textContent = '(in ' + service.unit + ')';
    document.getElementById('quantity_info').innerHTML =
        'Total = quantity &times; \u20a6' + parseFloat(service.price).toFixed(2) + ' per ' + escHtml(service.unit) + '.';

    calculateTotal();
    spClose();
}

// ── Provider filter (only shown when a type has 2+ providers) ────────────────
function selectProvider(providerName) {
    currentProvider  = providerName;
    currentServiceId = null;

    document.querySelectorAll('.provider-btn').forEach(function(btn) {
        const active = btn.dataset.provider === providerName;
        btn.classList.toggle('btn-primary', active);
        btn.classList.toggle('btn-outline-secondary', !active);
    });

    spServices = providerName === '__all__'
        ? allTypeServices
        : allTypeServices.filter(function(s){ return s.provider === providerName; });

    loadPickerServices(spServices);
    resetServiceSelection(spServices.length);
}

// ── Sync hidden <select> options with current spServices ─────────────────────
function loadPickerServices(services) {
    const sel = document.getElementById('service_id');
    sel.innerHTML = '<option value="">-- select --</option>';
    services.forEach(function(s) {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.text  = s.name;
        opt.setAttribute('data-name', s.name);
        opt.setAttribute('data-rate', s.price);
        opt.setAttribute('data-unit', s.unit);
        sel.appendChild(opt);
    });
}

function resetServiceSelection(count) {
    const trigger = document.getElementById('sp-trigger');
    trigger.classList.remove('no-type');
    const triggerText  = document.getElementById('sp-trigger-text');
    const triggerPrice = document.getElementById('sp-trigger-price');
    triggerText.textContent = '\ud83d\udd3d Click to select a plan (' + count + ' available)';
    triggerText.classList.add('placeholder');
    triggerPrice.style.display = 'none';

    const qty = document.getElementById('quantity');
    qty.disabled = true;
    qty.value = '';
    document.getElementById('service_info').innerText = '';
    document.getElementById('quantity_unit_label').textContent = '';
    document.getElementById('quantity_info').innerText = 'Select a plan to see pricing.';
    document.getElementById('service_name').value = '';
    updateTotalDisplay(0);
}

// ── Step 1: Proxy type selected ───────────────────────────────────────────────
function selectType(type) {
    currentType     = type;
    currentProvider = null;
    currentServiceId = null;

    document.querySelectorAll('.type-btn').forEach(function(btn) {
        const active = btn.dataset.type === type;
        btn.classList.toggle('btn-primary', active);
        btn.classList.toggle('btn-outline-secondary', !active);
    });

    allTypeServices = groupedData[type] || [];

    // ── Provider filter ────────────────────────────────────────────────────
    const providerSection   = document.getElementById('provider-section');
    const providerContainer = document.getElementById('provider-container');
    providerContainer.innerHTML = '';

    const providers = [...new Set(allTypeServices.map(function(s){ return s.provider; }))];

    if (providers.length > 1) {
        const allBtn = document.createElement('button');
        allBtn.type = 'button';
        allBtn.className = 'btn btn-sm btn-primary provider-btn';
        allBtn.dataset.provider = '__all__';
        allBtn.textContent = 'All providers';
        allBtn.onclick = function(){ selectProvider('__all__'); };
        providerContainer.appendChild(allBtn);

        providers.forEach(function(p) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-secondary provider-btn';
            btn.dataset.provider = p;
            btn.textContent = p;
            btn.onclick = function(){ selectProvider(p); };
            providerContainer.appendChild(btn);
        });

        providerSection.style.display = '';
        spServices = allTypeServices;
    } else {
        providerSection.style.display = 'none';
        spServices = allTypeServices;
    }

    loadPickerServices(spServices);
    resetServiceSelection(spServices.length);
}

// ── Calculate total ───────────────────────────────────────────────────────────
function calculateTotal() {
    if (!currentServiceId) { updateTotalDisplay(0); return; }
    const service = spServices.find(function(s){ return s.id == currentServiceId; });
    if (!service) { updateTotalDisplay(0); return; }

    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const rate     = parseFloat(service.price) || 0;

    const total = quantity * rate;
    updateTotalDisplay(total, quantity > 0);
}

// ── Update charge display + submit button ─────────────────────────────────────
function updateTotalDisplay(total, ready) {
    const formattedTotal = (total || 0).toFixed(2);
    document.getElementById('total_charge').innerText = formattedTotal;
    document.getElementById('charge').value = formattedTotal;

    const btn           = document.getElementById('submit_btn');
    const alertBox       = document.getElementById('charge_alert');
    const statusText     = document.getElementById('charge_status_text');
    const hint           = document.getElementById('charge_hint');
    const chargeDisplay  = document.getElementById('total_charge_display');

    if (!ready) {
        alertBox.style.border     = '1px solid #f7c1c1';
        alertBox.style.background = '#fcebeb';
        statusText.style.color = '#a32d2d';
        statusText.innerHTML   = '<i class="fas fa-lock me-1"></i> Select a plan and quantity';
        hint.textContent       = '';
        chargeDisplay.style.color = '#a32d2d';
        btn.disabled = true;
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
        btn.innerHTML = '<i class="fas fa-lock me-2"></i> Place Order';
    } else {
        alertBox.style.border     = '1px solid #c0dd97';
        alertBox.style.background = '#eaf3de';
        statusText.style.color = '#3b6d11';
        statusText.innerHTML   = '<i class="fas fa-lock-open me-1"></i> Ready to go!';
        hint.textContent       = '';
        chargeDisplay.style.color = '#3b6d11';
        btn.disabled = false;
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-primary');
        btn.innerHTML = '<i class="fas fa-bolt me-2"></i> Place Order';
    }
}
</script>
