@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Order Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/orders">Orders</a></li>
                    <li class="breadcrumb-item">#{{ substr($order->id, 0, 8) }}</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show" role="alert">
                    {{ session('alert')['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">

                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Order Information</h5>
                            <div>
                                @php
                                    $badgeClass = [
                                        \App\Types\OrderStatus::PENDING    => 'bg-warning',
                                        \App\Types\OrderStatus::PROCESSING => 'bg-info',
                                        \App\Types\OrderStatus::COMPLETED  => 'bg-success',
                                        \App\Types\OrderStatus::CANCELLED  => 'bg-danger',
                                        \App\Types\OrderStatus::REFUNDED   => 'bg-secondary',
                                    ];
                                    $statusLabels = \App\Types\OrderStatus::labels();
                                @endphp
                                <span class="badge {{ $badgeClass[$order->status] ?? 'bg-secondary' }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
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
                                    <span class="fs-12 fw-bold">₦{{ number_format($order->charge, 2) }}</span>
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
                        </div>
                        <div class="card-body">
                            @if($order->status !== 'completed')
                                <div class="text-muted fs-12">Proxy access details will appear here once this order completes.</div>
                            @elseif($order->isDataBasedProduct())
                                @php($access = $order->provider->config['static_proxy_access'] ?? null)
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
                                            <tr><th>IP</th><th>Port</th><th>Username</th><th>Password</th><th></th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->proxy_data as $proxy)
                                                @php
                                                    $ip = $proxy['ip'] ?? $proxy['host'] ?? $proxy['proxy_ip_address'] ?? '—';
                                                    $port = $proxy['port'] ?? $proxy['proxy_http_port'] ?? '—';
                                                    $user = $proxy['username'] ?? $proxy['login'] ?? $proxy['default_proxy_user_username'] ?? '—';
                                                    $pass = $proxy['password'] ?? $proxy['default_proxy_user_password'] ?? '—';
                                                @endphp
                                                <tr>
                                                    <td><code>{{ $ip }}</code></td>
                                                    <td><code>{{ $port }}</code></td>
                                                    <td><code>{{ $user }}</code></td>
                                                    <td><code>{{ $pass }}</code></td>
                                                    <td>
                                                        <button type="button" class="btn btn-xs btn-light"
                                                                onclick="navigator.clipboard.writeText('{{ $connString }}')">
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
        </div>
    </div>
</main>

@include('reseller.components.g-footer')