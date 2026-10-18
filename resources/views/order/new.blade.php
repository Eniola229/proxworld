@include('components.g-header')
@include('components.nav')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ───────────── Proxy type tabs (top of page) ───────────── */
.type-tabs {
    display: flex; flex-wrap: wrap; gap: 6px; padding: 6px; margin-bottom: 24px;
    background: #f4f5f7; border: 1px solid #e5e7eb; border-radius: 12px;
}
.type-tab {
    flex: 1 1 160px; display: flex; align-items: center; gap: 10px;
    padding: 8px 12px; border: 0; border-radius: 10px; background: transparent;
    font-size: 14px; font-weight: 600; color: #212529; text-align: left; cursor: pointer;
    transition: background .15s, box-shadow .15s;
}
.type-tab:hover { background: rgba(255,255,255,.65); }
.type-tab.active { background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.type-tab .tt-ico {
    width: 34px; height: 34px; flex-shrink: 0; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    background: #e9ecff; color: var(--bs-primary, #3454d1); font-size: 14px;
}
.type-tab.active .tt-ico { background: var(--bs-primary, #3454d1); color: #fff; }

/* ───────────── Product info (left column) ───────────── */
.po-card { background: #fff; border: 1px solid #e9ecef; border-radius: 12px; padding: 24px; }
.po-head { display: flex; gap: 20px; align-items: flex-start; }
.po-logo {
    width: 96px; height: 96px; flex-shrink: 0; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    background: #eef0fb; color: var(--bs-primary, #3454d1); font-size: 40px;
}
.po-title { margin: 0 0 6px; font-size: 26px; font-weight: 600; color: #1b2030; }
.po-intro { margin: 0; font-size: 15px; line-height: 1.6; color: #5f6675; }
.po-from { margin-top: 10px; font-size: 13px; color: #5f6675; }
.po-from strong { color: #1b2030; }
.po-buy { margin-top: 20px; }
.po-buy .btn { width: 100%; padding: 12px; font-weight: 600; }
.po-section { margin-top: 20px; padding: 20px; background: #f8f9fb; border-radius: 12px; }
.po-section h3 { margin: 0 0 16px; font-size: 18px; font-weight: 600; color: #1b2030; }
.po-ico {
    width: 52px; height: 52px; flex-shrink: 0; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: #eef0fb; color: var(--bs-primary, #3454d1); font-size: 20px;
}
.po-uses { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 20px; }
.po-use { display: flex; align-items: center; gap: 14px; font-size: 15px; font-weight: 500; color: #1b2030; }
.po-features { display: flex; flex-direction: column; gap: 14px; }
.po-feature { display: flex; align-items: center; gap: 14px; }
.po-feature h4 { margin: 0 0 2px; font-size: 16px; font-weight: 600; color: #1b2030; }
.po-feature p { margin: 0; font-size: 13.5px; color: #5f6675; line-height: 1.45; }
.po-specs { display: grid; grid-template-columns: 1fr 1fr; gap: 0 28px; }
.po-spec { display: flex; justify-content: space-between; gap: 12px; padding: 9px 0; border-bottom: 1px solid #e9ecef; font-size: 13.5px; }
.po-spec span:first-child { color: #5f6675; }
.po-spec span:last-child { font-weight: 600; color: #1b2030; text-align: right; }
.type-info { animation: tiFade .25s ease; }
@keyframes tiFade { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 575.98px) {
    .po-head { flex-direction: column; }
    .po-uses, .po-specs { grid-template-columns: 1fr; }
    .po-title { font-size: 22px; }
}

/* ───────────── Resources card (right column) ───────────── */
.res-card {
    margin-top: 24px; padding: 24px; border-radius: 12px; color: #fff;
    background: linear-gradient(135deg, var(--bs-primary, #3454d1) 0%, color-mix(in srgb, var(--bs-primary, #3454d1) 65%, #fff) 100%);
}
.res-card h3 { margin: 0 0 16px; font-size: 20px; font-weight: 600; color: #fff; }
.res-link {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    padding: 12px 14px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,.35); border-radius: 8px;
    color: #fff; font-size: 14px; font-weight: 600; text-decoration: none; transition: background .15s;
}
.res-link:last-child { margin-bottom: 0; }
.res-link:hover { background: rgba(255,255,255,.14); color: #fff; }
.res-link i { font-size: 13px; opacity: .9; }

/* ───────────── Service / country pickers (unchanged) ───────────── */
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
.service-picker-trigger .trigger-flag { width: 18px !important; height: 13px !important; object-fit: cover !important; border-radius: 2px !important; margin-right: 8px !important; vertical-align: middle !important; box-shadow: 0 0 0 1px rgba(0,0,0,0.08); }
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
.cp-flag { width: 20px; height: 15px; object-fit: cover; border-radius: 2px; flex-shrink: 0; box-shadow: 0 0 0 1px rgba(0,0,0,0.08); }
.cp-item-name mark { background: #fff3cd; color: #212529; border-radius: 2px; padding: 0 1px; }
</style>

@php
    // All the descriptive content (intro, use cases, features, specs) comes from config/products.php.
    $catalog = config('products');
    $useIcons = ['fa-chart-line', 'fa-magnifying-glass', 'fa-desktop', 'fa-bolt', 'fa-bullseye', 'fa-layer-group'];
@endphp

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title"><h5 class="m-b-10">New Order</h5></div>
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

            <!-- 1. Proxy type tabs -->
            <div class="type-tabs" id="type-container">
                @foreach($providersByType as $type => $entries)
                    @php $tp = $catalog[$type] ?? null; @endphp
                    <button type="button" class="type-tab type-btn" data-type="{{ $type }}" onclick="selectType('{{ $type }}')">
                        <span class="tt-ico"><i class="fas {{ $tp['icon'] ?? 'fa-globe' }}"></i></span>
                        <span>{{ $tp['short'] ?? ucfirst($type) }}</span>
                    </button>
                @endforeach
            </div>

            @if($providersByType->isEmpty())
                <div class="alert alert-warning mb-4">
                    No proxy plans are available right now — check back shortly, or contact
                    <a href="{{ route('support.index') }}">support</a>.
                </div>
            @endif

            <div class="row g-4">

                <!-- ───────── Left: info for the selected type ───────── -->
                <div class="col-xl-7">
                    @foreach($providersByType as $type => $entries)
                        @php $p = $catalog[$type] ?? null; @endphp
                        <div class="type-info" data-type="{{ $type }}" style="display:none;">
                            <div class="po-card">
                                <div class="po-head">
                                    <div class="po-logo"><i class="fas {{ $p['icon'] ?? 'fa-globe' }}"></i></div>
                                    <div>
                                        <h2 class="po-title">{{ $p['name'] ?? ucfirst($type) . ' Proxies' }}</h2>
                                        <p class="po-intro">{{ $p['intro'] ?? ($p['tagline'] ?? 'Choose a plan on the right to see pricing.') }}</p>
                                        @if(!empty($p['from']))
                                            <div class="po-from">From <strong>{{ $p['from'] }}</strong> / {{ $p['unit'] ?? '' }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="po-buy">
                                    <button type="button" class="btn btn-primary" onclick="scrollToOrder()">
                                        <i class="fas fa-cart-shopping me-2"></i> Purchase Now
                                    </button>
                                </div>

                                @if(!empty($p['best_for']))
                                    <div class="po-section">
                                        <h3>Use cases</h3>
                                        <div class="po-uses">
                                            @foreach($p['best_for'] as $item)
                                                <div class="po-use">
                                                    <span class="po-ico"><i class="fas {{ $useIcons[$loop->index % count($useIcons)] }}"></i></span>
                                                    <span>{{ $item }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($p['features']))
                                    <div class="po-section">
                                        <h3>Features</h3>
                                        <div class="po-features">
                                            @foreach($p['features'] as $f)
                                                <div class="po-feature">
                                                    <span class="po-ico"><i class="fas {{ $f[0] }}"></i></span>
                                                    <div>
                                                        <h4>{{ $f[1] }}</h4>
                                                        <p>{{ $f[2] }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($p['specs']))
                                    <div class="po-section">
                                        <h3>At a glance</h3>
                                        <div class="po-specs">
                                            @foreach($p['specs'] as $label => $value)
                                                <div class="po-spec"><span>{{ $label }}</span><span>{{ $value }}</span></div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- ───────── Right: the order form + resources ───────── -->
                <div class="col-xl-5">
                    <div class="card stretch stretch-full" id="order-card">
                        <div class="card-header"><h5 class="card-title">Buy Proxies</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('order.store') }}">
                                @csrf

                                <!-- 2. Provider Selection -->
                                <div class="mb-3" id="provider-section" style="display: none;">
                                    <label class="form-label mb-2">Select Provider</label>
                                    <div class="d-flex flex-wrap gap-2" id="provider-container"></div>
                                    <div class="form-text mt-1">Same proxy type, different suppliers — pick whichever fits your budget.</div>
                                </div>

                                <!-- 3. Country Selection -->
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

                                <!-- 4. Plan Selection -->
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

                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#orderDescription">
                                        <i class="fas fa-info-circle me-1"></i> How Proxy Orders Work
                                    </button>
                                    <div class="collapse mt-3" id="orderDescription">
                                        <div class="card card-body bg-light">
                                            <p class="mb-2">Pick a proxy type, narrow it down by country if you want (where applicable), then choose a plan and enter the quantity you need.</p>
                                            <p class="mb-3">Once your order is placed, we provision it with the right provider automatically. Your proxy credentials will show up on the order page as soon as it's ready.</p>
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

                                <input type="hidden" name="service_name" id="service_name">

                                <div class="mb-3">
                                    <label class="form-label">Quantity <span id="quantity_unit_label"></span></label>
                                    <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Select a plan first" disabled min="1" oninput="calculateTotal()">
                                    <div class="form-text" id="quantity_info">Select a plan to see pricing.</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3 px-3 py-3 rounded-3"
                                     id="charge_alert" style="border: 1px solid #f7c1c1; background: #fcebeb; transition: all 0.3s ease;">
                                    <div>
                                        <div id="charge_status_text" style="font-size: 13px; font-weight: 500; color: #a32d2d;">
                                            <i class="fas fa-lock me-1"></i> Select a plan and quantity
                                        </div>
                                        <small id="charge_hint" style="color: #888;"></small>
                                    </div>
                                    <div class="text-end">
                                        <div style="font-size: 11px; color: #888; margin-bottom: 2px;">Total charge</div>
                                        <h4 class="m-0 fw-bold" id="total_charge_display" style="color: #a32d2d; transition: color 0.3s ease;">
                                           {{ $currencyDisplay['prefix'] }}<span id="total_charge">0.00</span>
                                        </h4>
                                    </div>
                                </div>

                                <input type="hidden" name="charge" id="charge" value="0">

                                <div class="d-grid">
                                    <button type="submit" id="submit_btn" class="btn btn-lg btn-secondary" disabled style="transition: all 0.3s ease;">
                                        <i class="fas fa-lock me-2"></i> Place Order
                                    </button>
                                </div>

                                @error('service_id')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>
                    </div>

                    <!-- Resources -->
                    <div class="res-card">
                        <h3>Resources</h3>
                        <a href="{{ route('guides') }}" target="_blank" rel="noopener" class="res-link">
                            <span>Setup Guides</span><i class="fas fa-up-right-from-square"></i>
                        </a>
                        <a href="{{ route('api.docs') }}" target="_blank" rel="noopener" class="res-link">
                            <span>API Docs</span><i class="fas fa-up-right-from-square"></i>
                        </a>
                        <a href="{{ route('faq') }}" target="_blank" rel="noopener" class="res-link">
                            <span>FAQ</span><i class="fas fa-up-right-from-square"></i>
                        </a>
                        <a href="{{ route('support.index') }}" class="res-link">
                            <span>Chat with support</span><i class="fas fa-comments"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')

<script>

const CUR = @json($currencyDisplay);
function fmtUnit(p) {
    p = parseFloat(p) || 0;
    const d = CUR.decimals === 0 ? 0 : (p > 0 && p < 0.1 ? 4 : 2);
    return CUR.prefix + p.toFixed(d);
}

// providersByType: { residential: [{type, provider_id, provider_name}, ...], ... }
const providersByType = {!! json_encode($providersByType) !!};
const servicesUrl  = "{{ route('order.services') }}";
const countriesUrl = "{{ route('order.countries') }}";

let currentType        = null;
let currentProvider     = null;
let currentServiceId     = null;
let currentCountryCode    = '';   // '' = all countries
let spServices              = [];   // current page of services
let spOpen                    = false;
let spPage                     = 1;
let spLastPage                  = 1;
let spTotal                      = 0;
let spSearchTerm                  = '';
let spSearchDebounce               = null;
let spRequestToken                  = 0; // guards against out-of-order AJAX responses

// country picker state
let cpCountries      = [];   // full list for the current provider+type: [{code, name}]
let cpFilteredCountries = [];
let cpOpen            = false;
let cpSearchTerm       = '';
let cpRequestToken      = 0;

function highlight(text, query) {
    if (!query) return escHtml(text);
    const esc = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return escHtml(text).replace(new RegExp('(' + esc + ')', 'gi'), '<mark>$1</mark>');
}
function escHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Converts a 2-letter ISO country code into its flag emoji, e.g. "NG" -> 🇳🇬
// Used only as a fallback if the flagcdn.com image fails to load.
function countryFlagEmoji(code) {
    if (!code || code.length !== 2) return '🏳️';
    return code.toUpperCase().replace(/./g, function (char) {
        return String.fromCodePoint(127397 + char.charCodeAt(0));
    });
}

// Builds a real flag image URL from flagcdn.com for a 2-letter ISO country code
// e.g. "ng" -> https://flagcdn.com/24x18/ng.png
function countryFlagUrl(code) {
    if (!code) return '';
    return 'https://flagcdn.com/24x18/' + code.toLowerCase() + '.png';
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

// ── Fetch a page of services from the server ──────────────────────────────
async function fetchServices(page) {
    const list = document.getElementById('sp-list');
    list.innerHTML = '<div class="sp-loading"><i class="fas fa-circle-notch"></i>Loading plans…</div>';
    document.getElementById('sp-pager').style.display = 'none';

    const myToken = ++spRequestToken;
    const params = new URLSearchParams({
        provider_id: currentProvider,
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

        if (myToken !== spRequestToken) return; // a newer request already superseded this one

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
            + '<span class="sp-item-name">' + highlight(s.name, spSearchTerm) + '</span>'
            + '<div class="sp-item-right">'
            + '<span class="sp-item-price">' + escHtml(fmtUnit(s.price)) + '/' + escHtml(s.unit) + '</span>'
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

// ── Sync hidden <select> with current page's services ─────────────────────
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

// ── Select a plan ──────────────────────────────────────────────────────────
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
    triggerPrice.textContent = fmtUnit(service.price) + '/' + service.unit;
    triggerPrice.style.display = '';

    document.getElementById('service_info').innerHTML =
        'Priced per <strong>' + escHtml(service.unit) + '</strong>';

    const qty = document.getElementById('quantity');
    qty.disabled = false;
    qty.min = 1;
    qty.placeholder = 'e.g. 5';
    qty.value = '';

    document.getElementById('quantity_unit_label').textContent = '(in ' + service.unit + ')';
    document.getElementById('quantity_info').innerHTML =
        'Total = quantity &times; ' + escHtml(fmtUnit(service.price)) + ' per ' + escHtml(service.unit) + '.';

    calculateTotal();
    spClose();
    spRenderItems(); // re-render to show the "selected" state
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

// ── Fetch the full country list for the current provider + type ──────────
async function fetchCountries() {
    const list = document.getElementById('cp-list');
    list.innerHTML = '<div class="sp-loading"><i class="fas fa-circle-notch"></i>Loading countries…</div>';

    const myToken = ++cpRequestToken;
    const params = new URLSearchParams({
        provider_id: currentProvider,
        type: currentType,
    });

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

    // "All countries" option always sits at the top
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
                + '<span class="sp-item-name cp-item-name">'
                + '<img class="cp-flag" src="' + countryFlagUrl(c.code) + '" alt="' + escHtml(c.code) + '" loading="lazy" onerror="this.outerHTML=\'<span class=&quot;cp-flag&quot;>' + countryFlagEmoji(c.code) + '</span>\'">'
                + highlight(c.name, cpSearchTerm) + '</span>'
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
        triggerText.innerHTML = '<img class="trigger-flag" src="' + countryFlagUrl(code) + '" alt="' + escHtml(code) + '" onerror="this.outerHTML=\'' + countryFlagEmoji(code) + '\'">' + escHtml(name);
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

/* ───────────────────────── Provider / type steps ───────────────────────── */

// ── Provider filter ─────────────────────────────────────────────────────────
function selectProvider(providerId, providerName) {
    currentProvider  = providerId;
    currentServiceId = null;
    spSearchTerm = '';

    document.querySelectorAll('.provider-btn').forEach(function(btn) {
        const active = btn.dataset.provider === String(providerId);
        btn.classList.toggle('btn-primary', active);
        btn.classList.toggle('btn-outline-secondary', !active);
    });

    resetServiceSelection();
    resetCountrySelection();
    document.getElementById('country-section').style.display = '';
    fetchCountries();
    fetchServices(1);
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

// ── Proxy type info (rendered server-side from config/products.php) ───────
// Each type has a hidden .type-info block; we just show the matching one.
function showTypeInfo(type) {
    document.querySelectorAll('.type-info').forEach(function (el) {
        el.style.display = el.dataset.type === type ? '' : 'none';
    });
}

// "Purchase Now" button: bring the order form into view (matters on phones, where it sits below the info).
function scrollToOrder() {
    const card = document.getElementById('order-card');
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Step 1: Proxy type selected ───────────────────────────────────────────
function selectType(type) {
    currentType     = type;
    currentProvider = null;
    currentServiceId = null;
    spSearchTerm = '';

    document.querySelectorAll('.type-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.type === type);
    });

    showTypeInfo(type);

    // Keep the URL in sync so the page can be shared / refreshed on the same type.
    try {
        const url = new URL(window.location.href);
        url.searchParams.set('type', type);
        window.history.replaceState({}, '', url);
    } catch (e) { /* non-critical */ }

    const entries = providersByType[type] || [];

    const providerSection   = document.getElementById('provider-section');
    const providerContainer = document.getElementById('provider-container');
    const countrySection    = document.getElementById('country-section');
    providerContainer.innerHTML = '';
    providerSection.style.display = 'none'; // provider is always auto-picked, never shown to the user

    resetCountrySelection();
    countrySection.style.display = 'none';

    if (entries.length >= 1) {
        // Multiple providers can still exist behind a single type — we just
        // don't surface that choice to the customer, so pick the first one.
        selectProvider(entries[0].provider_id, entries[0].provider_name);
    } else {
        resetServiceSelection();
    }
}

// ── Calculate total ────────────────────────────────────────────────────────
function calculateTotal() {
    if (!currentServiceId) { updateTotalDisplay(0); return; }
    const service = spServices.find(s => s.id == currentServiceId);
    if (!service) { updateTotalDisplay(0); return; }

    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const rate     = parseFloat(service.price) || 0;
    updateTotalDisplay(quantity * rate, quantity > 0);
}

function updateTotalDisplay(total, ready) {
    const formattedTotal = (total || 0).toFixed(CUR.decimals);
    document.getElementById('total_charge').innerText = formattedTotal;
    document.getElementById('charge').value = formattedTotal;

    const btn          = document.getElementById('submit_btn');
    const alertBox      = document.getElementById('charge_alert');
    const statusText     = document.getElementById('charge_status_text');
    const hint            = document.getElementById('charge_hint');
    const chargeDisplay    = document.getElementById('total_charge_display');

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

// ── On load: pre-select the type from ?type=… (e.g. the dashboard "Buy Now" buttons) ──
// If there is no valid ?type, fall back to the first available type so the info panel is never empty.
document.addEventListener('DOMContentLoaded', function () {
    const wanted = (new URLSearchParams(window.location.search).get('type') || '').toLowerCase();
    const available = Object.keys(providersByType);

    if (wanted && available.indexOf(wanted) !== -1) {
        selectType(wanted);
    } else if (available.length) {
        selectType(available[0]);
    }
});
</script>