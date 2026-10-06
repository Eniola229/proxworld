@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Order Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item">#{{ substr($order->id, 0, 8) }}</li>
                </ul>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show">
                {{ session('alert')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif 

        <div class="main-content">
            <div class="row">

                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Order Information</h5>
                            <div>
                                @php
                                    $badgeClass = [
                                        'pending' => 'bg-warning', 'processing' => 'bg-info',
                                        'completed' => 'bg-success', 'cancelled' => 'bg-danger', 'refunded' => 'bg-secondary',
                                    ];
                                @endphp
                                <span class="badge {{ $badgeClass[$order->status] ?? 'bg-secondary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Order ID:</span>
                                    <code class="fs-11">{{ $order->id }}</code>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Service:</span>
                                    <span class="fs-12 fw-bold text-end">{{ $order->service_name }}</span>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Type:</span>
                                    <span class="badge bg-secondary text-uppercase">{{ $order->product_type ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Quantity:</span>
                                    <span class="fs-12 fw-bold">{{ number_format($order->quantity) }}</span>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Amount:</span>
                                    <span class="fs-12 fw-bold">@money($order->charge, $order->currency)</span>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Date:</span>
                                    <span class="fs-12">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-8 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title"><i class="feather-key me-1"></i> Proxy Access Details</h5>
                            @if($extend)
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#extendModal">
                                    <i class="feather-clock me-1"></i> Extend Order
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($order->status !== 'completed')
                                <div class="text-muted fs-12">Proxy access details will appear here once this order completes.</div>
                            @elseif($order->isDataBasedProduct())
                                @php $access = $order->provider->config['static_proxy_access'] ?? null; @endphp
                                @if($access)
                                    <div class="alert alert-info fs-12">This is a shared account-wide connection:</div>
                                    <table class="table table-sm mb-0">
                                        <tr><td>Host</td><td><code>{{ $access['host'] }}</code></td></tr>
                                        <tr><td>Port</td><td><code>{{ $access['port'] }}</code></td></tr>
                                        <tr><td>Username</td><td><code>{{ $access['username'] }}</code></td></tr>
                                        <tr><td>Password</td><td><code>{{ $access['password'] }}</code></td></tr>
                                    </table>
                                    @if(!empty($access['username_format']))
                                        <div class="fs-11 text-muted mt-2">To target a country/session, format your username as: <code>{{ $access['username_format'] }}</code></div>
                                    @endif
                                @else
                                    <div class="alert alert-warning fs-12 mb-0">Proxy access hasn't been configured for this provider yet.</div>
                                @endif
                            @elseif($order->hasProxyCredentials())
                                <div class="table-responsive">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>IP</th>
                                                <th>Port</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($order->proxy_data as $proxy)
                                            @php
                                                $ip   = $proxy['ip'] ?? $proxy['host'] ?? $proxy['proxy_ip_address'] ?? '—';
                                                $port = $proxy['port'] ?? $proxy['proxy_http_port'] ?? '—';
                                                $user = $proxy['username'] ?? $proxy['login'] ?? $proxy['default_proxy_user_username'] ?? '—';
                                                $pass = $proxy['password'] ?? $proxy['default_proxy_user_password'] ?? '—';
                                                $connString = "{$ip}:{$port}:{$user}:{$pass}";
                                            @endphp
                                            <tr>
                                                <td><code>{{ $ip }}</code></td>
                                                <td><code>{{ $port }}</code></td>
                                                <td><code>{{ $user }}</code></td>
                                                <td><code>{{ $pass }}</code></td>
                                                <td>
                                                    <button type="button" class="btn btn-xs btn-light"
                                                            onclick="navigator.clipboard.writeText(@js($connString))">
                                                        <i class="feather-copy"></i> Copy
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning fs-12 mb-0">
                                    We couldn't fetch your proxy details yet. Please refresh in a moment, or contact support if this persists.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            @if($order->extensions->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header"><h5 class="card-title">Extension History</h5></div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Date</th><th>Quantity</th><th>Paid</th></tr></thead>
                        <tbody>
                        @foreach($order->extensions as $ext)
                            <tr>
                                <td>{{ $ext->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ number_format($ext->quantity) }}</td>
                                <td>@money($ext->charge, $ext->currency)</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</main>

@if($extend)
<div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('orders.extend', $order) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Extend Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fs-12 text-muted">{{ $order->service_name }}</p>
                <label class="form-label fs-12">Quantity ({{ $extend['unit'] ?? 'units' }})</label>
                <input type="number" name="quantity" id="extendQty" class="form-control"
                       min="1" max="10000" value="{{ $order->quantity }}" required>
                <div class="mt-3 d-flex justify-content-between">
                    <span class="fs-12 text-muted">Estimated cost:</span>
                    <span class="fs-12 fw-bold" id="extendTotal"></span>
                </div>
                <div class="fs-11 text-muted mt-1">Charged from your wallet at current rates.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm &amp; Pay</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const unitPrice = @js($extend['unit_price']);
        const currency = @js($extend['currency']);
        const qty = document.getElementById('extendQty');
        const total = document.getElementById('extendTotal');
        function render() {
            const n = Math.max(1, parseInt(qty.value || '1', 10));
            total.textContent = (unitPrice * n).toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' ' + currency;
        }
        qty.addEventListener('input', render);
        render();
    })();
</script>
@endif

@include('components.g-footer')