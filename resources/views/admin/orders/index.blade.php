@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

@php $isSupport = auth('admin')->user()->isSupport(); @endphp

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Orders Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Orders</li>
                </ul>
            </div>
        </div>

        <div class="main-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="feather-alert-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4 mt-4">
                @php
                    $cards = [
                        ['Total Orders', $totalOrders,      'text-dark',    'primary', 'shopping-cart'],
                        ['Pending',      $pendingOrders,    'text-warning', 'warning', 'clock'],
                        ['Processing',   $processingOrders, 'text-info',    'info',    'loader'],
                        ['Completed',    $completedOrders,  'text-success', 'success', 'check-circle'],
                        ['Cancelled / Refunded', $cancelledOrders, 'text-danger', 'danger', 'x-circle'],
                    ];
                @endphp
                @foreach($cards as [$label, $value, $textClass, $color, $icon])
                <div class="col-xxl-2 col-md-4 col-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold mb-1">{{ $label }}</div>
                                    <div class="fs-4 fw-bold {{ $textClass }}">{{ number_format($value) }}</div>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-{{ $color }} text-{{ $color }}">
                                    <i class="feather-{{ $icon }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @if(!$isSupport)
                <div class="col-xxl-2 col-md-4 col-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold mb-1">Revenue</div>
                                    <div class="fs-4 fw-bold text-primary">₦{{ number_format($totalRevenue, 0) }}</div>
                                    <small class="text-muted fs-11">orders + extensions</small>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary">
                                    <i class="feather-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            @if(!$isSupport)
            <!-- Profit Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-primary text-dark">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="text-dark mb-1">
                                        <i class="feather-trending-up me-2"></i>
                                        @if(request('date_from') || request('date_to'))
                                            Profit (Filtered Period)
                                        @else
                                            Profit ({{ now()->format('F Y') }})
                                        @endif
                                    </h5>
                                    <p class="text-dark-50 mb-0">
                                        @if(request('date_from') && request('date_to'))
                                            {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }} -
                                            {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        @else
                                            Revenue minus API costs, including extensions
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <h2 class="text-dark mb-0">₦{{ number_format($totalProfit, 2) }}</h2>
                                    <small class="text-dark-50">
                                        {{ number_format($periodOrderCount) }} completed orders
                                        @if($periodExtCount)
                                            · {{ number_format($periodExtCount) }} extensions (₦{{ number_format($periodExtProfit, 2) }})
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Orders Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Orders</h5>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search order ID, service, customer..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach(['pending','processing','completed','partial','refunded','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="provider_id" class="form-select">
                                <option value="">All Providers</option>
                                @foreach($providers as $prov)
                                    <option value="{{ $prov->id }}" {{ request('provider_id') == $prov->id ? 'selected' : '' }}>
                                        {{ $prov->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" title="Filter"><i class="feather-filter"></i></button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary" title="Clear"><i class="feather-x"></i></a>
                            </div>
                        </div>
                    </form>

                    @if(request('provider_id'))
                        @php $filteredProv = $providers->firstWhere('id', request('provider_id')); @endphp
                        @if($filteredProv)
                        <div class="mt-2">
                            <span class="badge bg-info text-white fs-12 px-3 py-2">
                                <i class="feather-server me-1"></i>
                                Filtered by provider: <strong>{{ $filteredProv->name }}</strong>
                                <a href="{{ route('admin.orders.index', \Illuminate\Support\Arr::except(request()->query(), ['provider_id'])) }}"
                                   class="text-white ms-2"><i class="feather-x"></i></a>
                            </span>
                        </div>
                        @endif
                    @endif
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="border-b">
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Qty</th>
                                    <th>Charge</th>
                                    @if(!$isSupport)<th>Profit</th>@endif
                                    <th>Provider</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $orderItem)
                                @php
                                    $baseProfit = $orderItem->profit ?? \App\Services\PricingService::calculateProfit(
                                        $orderItem->charge_ngn, $orderItem->quantity, $orderItem->service_name
                                    );
                                    $rowExtProfit  = (float) ($orderItem->extensions_profit ?? 0);
                                    $rowExtRevenue = (float) ($orderItem->extensions_revenue ?? 0);
                                    $rowExtCount   = (int) ($orderItem->extensions_count ?? 0);

                                    $rowProfit  = $baseProfit + $rowExtProfit;
                                    $rowRevenue = $orderItem->charge_ngn + $rowExtRevenue;
                                    $profitPct  = $rowRevenue > 0 ? ($rowProfit / $rowRevenue * 100) : 0;
                                @endphp
                                    <tr>
                                        <td><code class="fs-11">#{{ substr($orderItem->id, 0, 8) }}</code></td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $orderItem->user_id) }}" class="text-dark">
                                                {{ $orderItem->user->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width:180px;" title="{{ $orderItem->service_name }}">
                                                {{ $orderItem->service_name }}
                                            </span>
                                            @if($rowExtCount)
                                                <small class="d-block text-muted fs-11"><i class="feather-clock me-1"></i>{{ $rowExtCount }} extension{{ $rowExtCount > 1 ? 's' : '' }}</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($orderItem->quantity) }}</td>
                                        <td class="text-success fw-bold">@money($orderItem->charge, $orderItem->currency)</td>
                                        @if(!$isSupport)
                                        <td>
                                            <span class="text-primary fw-bold">₦{{ number_format($rowProfit, 2) }}</span>
                                            <small class="text-muted d-block">{{ number_format($profitPct, 1) }}%</small>
                                            @if($rowExtCount)
                                                <small class="text-muted d-block fs-11">incl. ₦{{ number_format($rowExtProfit, 2) }} from ext.</small>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @if($orderItem->provider)
                                                <a href="{{ route('admin.orders.index', ['provider_id' => $orderItem->provider_id]) }}"
                                                   class="badge bg-soft-info text-info" title="Filter by this provider">
                                                    <i class="feather-server me-1"></i>{{ $orderItem->provider->name }}
                                                </a>
                                            @else
                                                <span class="text-muted fs-11">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($orderItem->status == 'completed')
                                                <span class="badge bg-soft-success text-success">Completed</span>
                                            @elseif($orderItem->status == 'processing')
                                                <span class="badge bg-soft-info text-info">Processing</span>
                                            @elseif($orderItem->status == 'pending')
                                                <span class="badge bg-soft-warning text-warning">Pending</span>
                                            @elseif($orderItem->status == 'partial')
                                                <span class="badge bg-soft-primary text-primary">Partial</span>
                                            @elseif($orderItem->status == 'refunded')
                                                <span class="badge bg-soft-secondary text-secondary">Refunded</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $orderItem->created_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.orders.show', $orderItem->id) }}" class="btn btn-sm btn-light-brand">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isSupport ? 9 : 10 }}" class="text-center py-4 text-muted">
                                            <i class="feather-inbox fs-3 d-block mb-2"></i> No orders found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($orders->hasPages())
                <div class="card-footer">{{ $orders->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</main>

@include('admin.components.footer')