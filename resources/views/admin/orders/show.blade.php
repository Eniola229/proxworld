@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Order Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                    <li class="breadcrumb-item">#{{ substr($order->id, 0, 8) }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto d-flex gap-2">
                @if($order->api_order_id && in_array($order->status, ['pending', 'processing']))
                    <form method="POST" action="{{ route('admin.orders.check-status', $order->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-info">
                            <i class="feather-refresh-cw me-2"></i> Check Status
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.customers.show', $order->user_id) }}" class="btn btn-sm btn-light-brand">
                    <i class="feather-user me-2"></i> View Customer
                </a>
            </div>
        </div>

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

        <div class="main-content">
            <div class="row">

                <!-- Order Info -->
                <div class="col-xxl-4 col-xl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Order Information</h5>
                            <div>
                                @if($order->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->status == 'refunded')
                                    <span class="badge bg-primary">Refunded</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Order ID:</span>
                                    <code class="fs-11">{{ $order->id }}</code>
                                </div>
                            </div>

                            @if($order->api_order_id)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">API Order ID:</span>
                                    <code class="fs-11">{{ $order->api_order_id }}</code>
                                </div>
                            </div>
                            @endif

                            {{-- PROVIDER INFO --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-12 text-muted"><i class="feather-server me-1"></i>Provider:</span>
                                    @if($order->provider)
                                        <div class="text-end">
                                            <a href="{{ route('admin.providers.edit', $order->provider_id) }}"
                                               class="badge bg-soft-info text-info fw-bold">
                                                {{ $order->provider->name }}
                                            </a>
                                            <br>
                                            <small class="text-muted fs-11">
                                                Balance: {{ $order->provider->cached_balance !== null ? '₦'.number_format($order->provider->cached_balance,2) : 'Unknown' }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted fs-11">Unknown / Legacy</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Service:</span>
                                    <span class="fs-12 fw-bold text-end" style="max-width:60%;">{{ $order->service_name }}</span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-12 text-muted">Link/URL:</span>
                                </div>
                                <a href="{{ $order->link }}" target="_blank" class="fs-11 text-primary text-break">
                                    {{ $order->link }}
                                </a>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Quantity:</span>
                                    <span class="fs-12 fw-bold">{{ number_format($order->quantity) }}</span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Charge:</span>
                                    <span class="fs-12 fw-bold text-success">₦{{ number_format($order->charge, 2) }}</span>
                                </div>
                            </div>

                            @php
                                if ($order->profit !== null) {
                                    $profitAmount   = $order->profit;
                                    $profitMargin   = $order->charge > 0 ? ($profitAmount / $order->charge) * 100 : 0;
                                    $profitBreakdown = [
                                        'profit_amount'     => $profitAmount,
                                        'profit_margin'     => $profitMargin,
                                        'original_cost'     => $order->charge - $profitAmount,
                                        'markup_percentage' => $order->markup_percentage ?? \App\Services\PricingService::getMarkupPercentage($order->service_name),
                                    ];
                                } else {
                                    $profitBreakdown = \App\Services\PricingService::getProfitBreakdown(
                                        $order->charge, $order->quantity, $order->service_name
                                    );
                                }
                            @endphp

                            <div class="mb-3 pb-3 border-bottom bg-soft-primary rounded p-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-12 text-muted"><i class="feather-trending-up me-1"></i> Profit:</span>
                                    <span class="fs-12 fw-bold text-primary">₦{{ number_format($profitBreakdown['profit_amount'], 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-11 text-muted">Margin:</span>
                                    <span class="fs-11 fw-bold text-primary">{{ number_format($profitBreakdown['profit_margin'], 1) }}%</span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fs-11 text-muted">API Cost:</span>
                                    <span class="fs-11">₦{{ number_format($profitBreakdown['original_cost'], 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-11 text-muted">Markup Applied:</span>
                                    <span class="fs-11">{{ number_format($profitBreakdown['markup_percentage'], 0) }}%</span>
                                </div>
                            </div>

                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Created:</span>
                                    <span class="fs-12 fw-bold">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-12 text-muted">Last Updated:</span>
                                    <span class="fs-12 fw-bold">{{ $order->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card mt-3">
                        <div class="card-header"><h5 class="card-title">Customer Information</h5></div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary me-3">
                                    {{ substr($order->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $order->user->name }}</h6>
                                    <p class="fs-12 text-muted mb-0">{{ $order->user->email }}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="fs-12 text-muted">Balance:</span>
                                <span class="fs-12 fw-bold text-success">₦{{ number_format($customerBalance, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fs-12 text-muted">Member Since:</span>
                                <span class="fs-12 fw-bold">{{ $order->user->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions & Logs -->
                <div class="col-xxl-8 col-xl-6">

                    @if(auth('admin')->user()->canEditOrders())
                    <div class="card mb-3">
                        <div class="card-header bg-soft-warning">
                            <h5 class="card-title text-warning">
                                <i class="feather-shield me-2"></i> Admin Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                                        @csrf
                                        <label class="form-label fw-bold">Update Status</label>
                                        <div class="input-group">
                                            <select name="status" class="form-select" required>
                                                <option value="pending"    {{ $order->status == 'pending'    ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="completed"  {{ $order->status == 'completed'  ? 'selected' : '' }}>Completed</option>
                                                <option value="refunded"   {{ $order->status == 'refunded'   ? 'selected' : '' }}>Refunded</option>
                                                <option value="cancelled"  {{ $order->status == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            <button type="submit" class="btn btn-warning">Update</button>
                                        </div>
                                    </form>
                                </div>
                                @if(auth('admin')->user()->canDeleteOrders() && in_array($order->status, ['cancelled', 'completed', 'refunded']))
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Delete Order</label>
                                    <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}"
                                          onsubmit="return confirm('Delete this order permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="feather-trash-2 me-2"></i> Delete
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($order->api_response)
                    <div class="card mb-3">
                        <div class="card-header"><h5 class="card-title">API Response</h5></div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded" style="max-height:300px;overflow-y:auto;"><code>{{ json_encode(json_decode($order->api_response), JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                    </div>
                    @endif

                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Order Activity Logs</h5></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Type</th>
                                            <th>Method</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs as $log)
                                            <tr>
                                                <td><span class="badge bg-soft-primary text-primary">{{ ucfirst($log->type) }}</span></td>
                                                <td><code class="fs-11">{{ $log->method }}</code></td>
                                                <td>{{ Str::limit($log->description, 60) }}</td>
                                                <td>
                                                    @if($log->status == 'success')
                                                        <span class="badge bg-soft-success text-success">Success</span>
                                                    @elseif($log->status == 'failed')
                                                        <span class="badge bg-soft-danger text-danger">Failed</span>
                                                    @else
                                                        <span class="badge bg-soft-warning text-warning">{{ ucfirst($log->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $log->created_at->format('M d, H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center py-3 text-muted">No activity logs</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($logs->hasPages())
                        <div class="card-footer">{{ $logs->links() }}</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')