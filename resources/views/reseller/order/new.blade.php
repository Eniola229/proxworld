@include('reseller.components.g-header')
@include('reseller.components.nav')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
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
    cursor: pointer !important;
    font-size: 14px !important;
    color: #212529 !important;
    text-align: left !important;
    box-shadow: none !important;
    transition: border-color 0.15s, box-shadow 0.15s !important;
    line-height: 1.5 !important;
}
.service-picker-trigger:hover, .service-picker-trigger:focus {
    border-color: #adb5bd !important; outline: none !important;
}
.service-picker-trigger.open {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.12) !important;
    border-radius: 8px 8px 0 0 !important;
}
.service-picker-trigger.no-type { cursor: default !important; color: #6c757d !important; }
.service-picker-trigger .trigger-text {
    flex: 1 !important; overflow: hidden !important; text-overflow: ellipsis !important;
    white-space: nowrap !important; color: #212529 !important;
}
.service-picker-trigger .trigger-text.placeholder { color: #adb5bd !important; }
.service-picker-trigger .trigger-meta { display: flex !important; align-items: center !important; gap: 8px !important; margin-left: 10px !important; }
.service-picker-trigger .trigger-price {
    font-size: 12px !important; font-weight: 600 !important; color: #0d6efd !important;
    background: #e7f1ff !important; padding: 2px 8px !important; border-radius: 20px !important; white-space: nowrap !important;
}
.service-picker-trigger .trigger-flag { font-size: 16px !important; margin-right: 8px !important; }
.service-picker-trigger .trigger-chevron { font-size: 11px !important; color: #6c757d !important; transition: transform 0.2s; }
.service-picker-trigger.open .trigger-chevron { transform: rotate(180deg); }

.service-picker-panel {
    display: none; position: absolute; left: 0; right: 0; top: 100%; z-index: 1000;
    background: #fff; border: 1px solid #0d6efd; border-top: none;
    border-radius: 0 0 10px 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.10); overflow: hidden;
}
.service-picker-panel.open { display: block; animation: spSlideIn 0.15s ease; }
@keyframes spSlideIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

.service-picker-search { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-bottom: 1px solid #f0f0f0; background: #f8f9fa; }
.service-picker-search i { color: #adb5bd; font-size: 13px; }
.service-picker-search input { flex: 1; border: none; background: transparent; outline: none; font-size: 13px; color: #212529; }
.service-picker-search input::placeholder { color: #adb5bd; }
.sp-count { font-size: 11px; color: #adb5bd; white-space: nowrap; }

.service-picker-list { max-height: 300px; overflow-y: auto; overscroll-behavior: contain; min-height: 80px; position: relative; }
.service-picker-list::-webkit-scrollbar { width: 5px; }
.service-picker-list::-webkit-scrollbar-track { background: #f8f9fa; }
.service-picker-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 10px; }

.sp-item { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 11px 14px; cursor: pointer; transition: background 0.1s; border-bottom: 1px solid #f5f5f5; }
.sp-item:last-child { border-bottom: none; }
.sp-item:hover { background: #f0f7ff; }
.sp-item.selected { background: #e7f1ff; }
.sp-item-name { font-size: 13px; color: #212529; flex: 1; line-height: 1.4; display: flex; align-items: center; }
.sp-item-name mark { background: #fff3cd; color: #212529; border-radius: 2px; padding: 0 1px; }
.sp-item-name small { color: #adb5bd; }
.sp-item-right { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.sp-item-price { font-size: 12px; font-weight: 600; color: #0d6efd; background: #e7f1ff; padding: 3px 9px; border-radius: 20px; white-space: nowrap; }
.sp-item.selected .sp-item-price { background: #0d6efd; color: #fff; }
.sp-item-check { font-size: 12px; color: #0d6efd; display: none; }
.sp-item.selected .sp-item-check { display: block; }
.sp-empty { padding: 24px; text-align: center; color: #adb5bd; font-size: 13px; }
.sp-empty i { display: block; font-size: 22px; margin-bottom: 6px; }
.sp-loading { padding: 24px; text-align: center; color: #adb5bd; font-size: 13px; }
.sp-loading i { display: block; font-size: 18px; margin-bottom: 6px; animation: spSpin 0.8s linear infinite; }
@keyframes spSpin { to { transform: rotate(360deg); } }

.sp-pager { display: flex; align-items: center; justify-content: space-between; padding: 8px 14px; border-top: 1px solid #f0f0f0; background: #f8f9fa; font-size: 12px; color: #6c757d; }
.sp-pager button { border: none; background: transparent; color: #0d6efd; font-weight: 600; padding: 4px 8px; cursor: pointer; }
.sp-pager button:disabled { color: #ced4da; cursor: default; }

/* Country picker — reuses the same visual language as the service picker */
.country-picker-wrapper { position: relative; max-width: 320px; }
.cp-item-name { display: flex; align-items: center; gap: 8px; }
.cp-flag { font-size: 17px; line-height: 1; }
.cp-item-name mark { background: #fff3cd; color: #212529; border-radius: 2px; padding: 0 1px; }
</style>

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">New Order</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('storefront.dashboard') }}">Home</a></li>
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
                            <form method="POST" action="{{ route('storefront.orders.store') }}" id="order-form">
                                @csrf

                                <!-- Proxy Type Selection -->
                                <div class="mb-4">
                                    <label class="form-label mb-3">Select Proxy Type</label>
                                    <div class="d-flex flex-wrap gap-3" id="type-container">
                                        @foreach($types as $type)
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
                                    @if($types->isEmpty())
                                        <div class="alert alert-warning mt-3 mb-0">
                                            No proxy plans are available right now.
                                        </div>
                                    @endif
                                </div>

                                <!-- Country Selection -->
                                <div class="mb-3" id="country-section" style="display: none;">
                                    <label class="form-label mb-2">Filter by Country</label>
                                    <div class="country-picker-wrapper" id="country-picker-wrapper">
                                        <button type="button" class="service-picker-trigger no-type" id="cp-trigger" onclick="cpToggle()">
                                            <span class="trigger-text placeholder" id="cp-trigger-text">🌍 All countries</span>
                                            <div class="trigger-meta">
                                                <i class="fas fa-chevron-down trigger-chevron"></i>
                                            </div>
                                        </button>
                                        <div class="service-picker-panel" id="cp-panel">
                                            <div class="service-picker-search">
                                                <i class="fas fa-search"></i>
                                                <input type="text" id="cp-search" placeholder="Search country…" oninput="cpSearchInput(this.value)" autocomplete="off">
                                                <span class="sp-count" id="cp-count"></span>
                                            </div>
                                            <div class="service-picker-list" id="cp-list"></div>
                                        </div>
                                    </div>
                                    <div class="form-text mt-1">Optional — narrows plans down to one country.</div>
                                </div>

                                <!-- Plan Selection -->
                                <div class="mb-3">
                                    <label class="form-label">Select Plan</label>

                                    <select name="service_id" id="service_id" style="display:none;" required>
                                        <option value="">-- select --</option>
                                    </select>

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
                                                <input type="text" id="sp-search" placeholder="Search plans…" oninput="spSearchInput(this.value)" autocomplete="off">
                                                <span class="sp-count" id="sp-count"></span>
                                            </div>
                                            <div class="service-picker-list" id="sp-list"></div>
                                            <div class="sp-pager" id="sp-pager" style="display:none;">
                                                <span id="sp-pager-info"></span>
                                                <div>
                                                    <button type="button" id="sp-prev" onclick="spGoPage(-1)"><i class="fas fa-chevron-left"></i> Prev</button>
                                                    <button type="button" id="sp-next" onclick="spGoPage(1)">Next <i class="fas fa-chevron-right"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-text mt-1" id="service_info"></div>
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

                                            @if($reseller->support_email || $reseller->telegram_link || $reseller->whatsapp_link)
                                                <p class="fw-bold mb-2">Need help?</p>
                                                <ul class="list-unstyled mb-0">
                                                    @if($reseller->telegram_link)
                                                        <li class="mb-1">
                                                            <i class="fab fa-telegram me-1" style="color:#0088cc;"></i>
                                                            <a href="{{ $reseller->telegram_link }}" target="_blank">Message us on Telegram</a>
                                                        </li>
                                                    @endif
                                                    @if($reseller->whatsapp_link)
                                                        <li class="mb-1">
                                                            <i class="fab fa-whatsapp me-1" style="color:#25D366;"></i>
                                                            <a href="{{ $reseller->whatsapp_link }}" target="_blank">Message us on WhatsApp</a>
                                                        </li>
                                                    @endif
                                                    @if($reseller->support_email)
                                                        <li>
                                                            <i class="fas fa-envelope me-1"></i>
                                                            Email us at <a href="mailto:{{ $reseller->support_email }}">{{ $reseller->support_email }}</a>
                                                        </li>
                                                    @endif
                                                </ul>
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

@include('reseller.components.g-footer')

<script>
const servicesUrl  = "{{ route('storefront.orders.services') }}";
const countriesUrl = "{{ route('storefront.orders.countries') }}";

let currentType         = null;
let currentServiceId    = null;
let currentCountryCode  = '';   // '' = all countries
let spServices           = [];
let spOpen                = false;
let spPage                 = 1;
let spLastPage               = 1;
let spTotal                   = 0;
let spSearchTerm               = '';
let spSearchDebounce            = null;
let spRequestToken                = 0;

// country picker state
let cpCountries          = [];   // full list for the current type: [{code, name}]
let cpFilteredCountries    = [];
let cpOpen                  = false;
let cpSearchTerm             = '';
let cpRequestToken            = 0;

function highlight(text, query) {
    if (!query) return escHtml(text);
    const esc = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return escHtml(text).replace(new RegExp('(' + esc + ')', 'gi'), '<mark>$1</mark>');
}
function escHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Converts a 2-letter ISO country code into its flag emoji, e.g. "NG" -> 🇳🇬
function countryFlagEmoji(code) {
    if (!code || code.length !== 2) return '🏳️';
    return code.toUpperCase().replace(/./g, function (char) {
        return String.fromCodePoint(127397 + char.charCodeAt(0));
    });
}

/* ───────────────────────── Service picker ───────────────────────── */

function spToggle() {
    if (document.getElementById('sp-trigger').classList.contains('no-type')) {
        const c = document.getElementById('type-container');
        c.style.transition = 'transform 0.1s';
        c.style.transform = 'translateX(6px)';
        setTimeout(() => c.style.transform = 'translateX(-6px)', 100);
        setTimeout(() => c.style.transform = 'translateX(4px)', 200);
        setTimeout(() => c.style.transform = 'translateX(0)', 300);
        return;
    }
    spOpen ? spClose() : spOpen_();
}
function spOpen_() {
    spOpen = true;
    cpClose();
    document.getElementById('sp-trigger').classList.add('open');
    document.getElementById('sp-panel').classList.add('open');
    const search = document.getElementById('sp-search');
    search.value = spSearchTerm;
    setTimeout(() => search.focus(), 50);
}
function spClose() {
    spOpen = false;
    document.getElementById('sp-trigger').classList.remove('open');
    document.getElementById('sp-panel').classList.remove('open');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('service-picker-wrapper');
    if (wrapper && !wrapper.contains(e.target)) spClose();

    const cpWrapper = document.getElementById('country-picker-wrapper');
    if (cpWrapper && !cpWrapper.contains(e.target)) cpClose();
});

async function fetchServices(page) {
    const list = document.getElementById('sp-list');
    list.innerHTML = '<div class="sp-loading"><i class="fas fa-circle-notch"></i>Loading plans…</div>';
    document.getElementById('sp-pager').style.display = 'none';

    const myToken = ++spRequestToken;
    const params = new URLSearchParams({
        type: currentType,
        page: page,
        search: spSearchTerm,
    });
    if (currentCountryCode) {
        params.set('country_code', currentCountryCode);
    }

    try {
        const res = await fetch(servicesUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error('Request failed');
        const json = await res.json();

        if (myToken !== spRequestToken) return;

        spServices = json.data;
        spPage = json.current_page;
        spLastPage = json.last_page;
        spTotal = json.total;

        loadPickerServices(spServices);
        spRenderItems();
    } catch (e) {
        if (myToken !== spRequestToken) return;
        document.getElementById('sp-list').innerHTML =
            '<div class="sp-empty"><i class="fas fa-triangle-exclamation"></i>Couldn\'t load plans. Try again.</div>';
    }
}

function spRenderItems() {
    const list = document.getElementById('sp-list');
    const countEl = document.getElementById('sp-count');
    countEl.textContent = spTotal + ' plan' + (spTotal !== 1 ? 's' : '');

    if (spServices.length === 0) {
        list.innerHTML = '<div class="sp-empty"><i class="fas fa-search-minus"></i>No plans match "' + escHtml(spSearchTerm) + '"</div>';
        document.getElementById('sp-pager').style.display = 'none';
        return;
    }

    list.innerHTML = spServices.map(function(s) {
        const sel = s.id == currentServiceId ? 'selected' : '';
        return '<div class="sp-item ' + sel + '" data-id="' + s.id + '" onclick="spSelect(\'' + s.id + '\')">'
            + '<span class="sp-item-name">' + highlight(s.name, spSearchTerm) + ' <small>(' + escHtml(s.provider) + ')</small></span>'
            + '<div class="sp-item-right">'
            + '<span class="sp-item-price">&#8358;' + parseFloat(s.price).toFixed(2) + '/' + escHtml(s.unit) + '</span>'
            + '<i class="fas fa-check sp-item-check"></i>'
            + '</div></div>';
    }).join('');

    const pager = document.getElementById('sp-pager');
    if (spLastPage > 1) {
        pager.style.display = 'flex';
        document.getElementById('sp-pager-info').textContent = 'Page ' + spPage + ' of ' + spLastPage;
        document.getElementById('sp-prev').disabled = spPage <= 1;
        document.getElementById('sp-next').disabled = spPage >= spLastPage;
    } else {
        pager.style.display = 'none';
    }
}

function spGoPage(delta) {
    const target = spPage + delta;
    if (target < 1 || target > spLastPage) return;
    fetchServices(target);
}

function spSearchInput(val) {
    spSearchTerm = val;
    clearTimeout(spSearchDebounce);
    spSearchDebounce = setTimeout(() => fetchServices(1), 300);
}

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

function spSelect(serviceId) {
    const service = spServices.find(s => s.id == serviceId);
    if (!service) return;
    currentServiceId = serviceId;

    document.getElementById('service_id').value = serviceId;
    document.getElementById('service_name').value = service.name;

    const triggerText  = document.getElementById('sp-trigger-text');
    const triggerPrice = document.getElementById('sp-trigger-price');
    triggerText.textContent = service.name;
    triggerText.classList.remove('placeholder');
    triggerPrice.textContent = '\u20a6' + parseFloat(service.price).toFixed(2) + '/' + service.unit;
    triggerPrice.style.display = '';

    document.getElementById('service_info').innerHTML =
        `Provider: <strong>${escHtml(service.provider)}</strong> &nbsp;|&nbsp; Priced per <strong>${escHtml(service.unit)}</strong>`;

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
    spRenderItems();
}

function resetServiceSelection() {
    const trigger = document.getElementById('sp-trigger');
    trigger.classList.remove('no-type');
    const triggerText  = document.getElementById('sp-trigger-text');
    const triggerPrice = document.getElementById('sp-trigger-price');
    triggerText.textContent = '\ud83d\udd3d Click to select a plan';
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

/* ───────────────────────── Country picker ───────────────────────── */

function cpToggle() {
    if (document.getElementById('cp-trigger').classList.contains('no-type')
        && cpCountries.length === 0) {
        return;
    }
    cpOpen ? cpClose() : cpOpen_();
}
function cpOpen_() {
    cpOpen = true;
    spClose();
    document.getElementById('cp-trigger').classList.add('open');
    document.getElementById('cp-panel').classList.add('open');
    const search = document.getElementById('cp-search');
    search.value = cpSearchTerm;
    setTimeout(() => search.focus(), 50);
}
function cpClose() {
    cpOpen = false;
    document.getElementById('cp-trigger').classList.remove('open');
    document.getElementById('cp-panel').classList.remove('open');
}

// ── Fetch the full country list for the current type ─────────────────────
async function fetchCountries() {
    const list = document.getElementById('cp-list');
    list.innerHTML = '<div class="sp-loading"><i class="fas fa-circle-notch"></i>Loading countries…</div>';

    const myToken = ++cpRequestToken;
    const params = new URLSearchParams({ type: currentType });

    try {
        const res = await fetch(countriesUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json' },
        });
        if (!res.ok) throw new Error('Request failed');
        const json = await res.json();

        if (myToken !== cpRequestToken) return;

        cpCountries = json.data || [];
        cpSearchTerm = '';
        document.getElementById('cp-trigger').classList.remove('no-type');
        cpApplyFilter();
    } catch (e) {
        if (myToken !== cpRequestToken) return;
        cpCountries = [];
        document.getElementById('cp-list').innerHTML =
            '<div class="sp-empty"><i class="fas fa-triangle-exclamation"></i>Couldn\'t load countries.</div>';
    }
}

function cpApplyFilter() {
    const term = cpSearchTerm.trim().toLowerCase();
    cpFilteredCountries = term
        ? cpCountries.filter(c => c.name.toLowerCase().includes(term))
        : cpCountries;
    cpRenderItems();
}

function cpRenderItems() {
    const list = document.getElementById('cp-list');
    const countEl = document.getElementById('cp-count');
    countEl.textContent = cpCountries.length + ' countr' + (cpCountries.length !== 1 ? 'ies' : 'y');

    let html = '';

    const allSelected = currentCountryCode === '' ? 'selected' : '';
    html += '<div class="sp-item ' + allSelected + '" data-code="" onclick="cpSelect(\'\')">'
        + '<span class="sp-item-name cp-item-name"><span class="cp-flag">🌍</span>All countries</span>'
        + '<div class="sp-item-right"><i class="fas fa-check sp-item-check"></i></div>'
        + '</div>';

    if (cpFilteredCountries.length === 0 && cpSearchTerm) {
        html += '<div class="sp-empty"><i class="fas fa-search-minus"></i>No country matches "' + escHtml(cpSearchTerm) + '"</div>';
    } else {
        html += cpFilteredCountries.map(function(c) {
            const sel = c.code === currentCountryCode ? 'selected' : '';
            return '<div class="sp-item ' + sel + '" data-code="' + escHtml(c.code) + '" onclick="cpSelect(\'' + c.code + '\')">'
                + '<span class="sp-item-name cp-item-name"><span class="cp-flag">' + countryFlagEmoji(c.code) + '</span>' + highlight(c.name, cpSearchTerm) + '</span>'
                + '<div class="sp-item-right"><i class="fas fa-check sp-item-check"></i></div>'
                + '</div>';
        }).join('');
    }

    list.innerHTML = html;
}

function cpSearchInput(val) {
    cpSearchTerm = val;
    cpApplyFilter();
}

function cpSelect(code) {
    currentCountryCode = code;

    const triggerText = document.getElementById('cp-trigger-text');
    if (code === '') {
        triggerText.innerHTML = '🌍 All countries';
    } else {
        const country = cpCountries.find(c => c.code === code);
        const name = country ? country.name : code;
        triggerText.innerHTML = '<span class="trigger-flag">' + countryFlagEmoji(code) + '</span>' + escHtml(name);
    }
    triggerText.classList.remove('placeholder');

    cpClose();
    cpRenderItems();

    // Re-fetch plans filtered to this country, and reset any selected plan
    currentServiceId = null;
    resetServiceSelection();
    fetchServices(1);
}

function resetCountrySelection() {
    currentCountryCode = '';
    cpCountries = [];
    cpFilteredCountries = [];
    cpSearchTerm = '';

    const trigger = document.getElementById('cp-trigger');
    trigger.classList.add('no-type');
    const triggerText = document.getElementById('cp-trigger-text');
    triggerText.innerHTML = '🌍 All countries';
    triggerText.classList.add('placeholder');
    document.getElementById('cp-list').innerHTML = '';
    document.getElementById('cp-count').textContent = '';
}

/* ───────────────────────── Proxy type step ───────────────────────── */

function selectType(type) {
    currentType       = type;
    currentServiceId  = null;
    spSearchTerm = '';

    document.querySelectorAll('.type-btn').forEach(function(btn) {
        const active = btn.dataset.type === type;
        btn.classList.toggle('btn-primary', active);
        btn.classList.toggle('btn-outline-secondary', !active);
    });

    resetServiceSelection();
    resetCountrySelection();
    document.getElementById('country-section').style.display = '';
    fetchCountries();
    fetchServices(1);
}

function calculateTotal() {
    if (!currentServiceId) { updateTotalDisplay(0); return; }
    const service = spServices.find(s => s.id == currentServiceId);
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

document.getElementById('order-form').addEventListener('submit', function (e) {
    if (!document.getElementById('service_id').value) {
        e.preventDefault();
        alert('Please select a plan before placing your order.');
    }
});
</script>