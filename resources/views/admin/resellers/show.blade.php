@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $reseller->panel_name }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.resellers.index') }}">Resellers</a></li>
            <li class="breadcrumb-item">{{ $reseller->panel_name }}</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto d-flex gap-2">
        @if($reseller->status === 'pending')
            {{-- Approval Form --}}
            <div class="alert alert-info mb-0 me-2">
                <i class="feather-info me-2"></i>
                <strong>Upon approval, this server IP will be assigned:</strong><br>
                <code class="fw-bold">{{ $detectedServerIp }}</code>
                <div class="small mt-1">This IP is automatically detected from your server.</div>
            </div>
            
            <form method="POST" action="{{ route('admin.resellers.approve', $reseller) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-sm btn-success" onclick="return confirm('Approve this panel? Server IP will be assigned.')">
                    <i class="feather-check-circle me-1"></i> Approve Panel
                </button>
            </form>
<!--             
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="feather-x-circle me-1"></i> Reject Panel
            </button>
        @else
            {{-- Quick status toggle for active/suspended --}}
            <form method="POST" action="{{ route('admin.resellers.status', $reseller) }}">
                @csrf @method('PATCH')
                @if($reseller->status === 'active')
                    <input type="hidden" name="status" value="suspended">
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="feather-pause-circle me-1"></i> Suspend Panel
                    </button>
                @elseif($reseller->status === 'suspended')
                    <input type="hidden" name="status" value="active">
                    <button class="btn btn-sm btn-outline-success">
                        <i class="feather-play-circle me-1"></i> Activate Panel
                    </button>
                @endif
            </form>
        @endif
         -->
        <a href="https://{{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}"
           target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="feather-external-link me-1"></i> Visit Panel
        </a>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.resellers.reject', $reseller) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reject Panel: {{ $reseller->panel_name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for rejection</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        <div class="form-text">This will be shown to the reseller.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Panel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="main-content">

    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-primary text-primary rounded">
                        <i class="feather-shopping-cart"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalOrders) }}</div>
                        <div class="fs-13 text-muted">Total Orders</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-success text-success rounded">
                        <i class="feather-dollar-sign"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">₦{{ number_format($totalRevenue, 2) }}</div>
                        <div class="fs-13 text-muted">Total Revenue</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-info text-info rounded">
                        <i class="feather-trending-up"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">₦{{ number_format($reseller->totalProfitEarned(), 2) }}</div>
                        <div class="fs-13 text-muted">Total Reseller Profit</div>
                        <p>Kindly note that this is not the available profit balance. Click withdrawal for that. </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded">
                        <i class="feather-users"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-dark">{{ number_format($totalCustomers) }}</div>
                        <div class="fs-13 text-muted">Customers</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Panel Details --}}
        <div class="col-lg-4">
            <div class="card stretch stretch-full">
                <div class="card-header"><h5 class="card-title">Panel Info</h5></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted fs-13">Panel Name</dt>
                        <dd class="col-sm-7 fw-semibold">{{ $reseller->panel_name }}</dd>

                        <dt class="col-sm-5 text-muted fs-13">Subdomain</dt>
                        <dd class="col-sm-7">
                            <code class="fs-12">{{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}</code>
                        </dd>

                        <dt class="col-sm-5 text-muted fs-13">Owner</dt>
                        <dd class="col-sm-7">
                            {{ $reseller->owner->name }}<br>
                            <small class="text-muted">{{ $reseller->owner->email }}</small>
                        </dd>

                        <dt class="col-sm-5 text-muted fs-13">Owner Balance</dt>
                        <dd class="col-sm-7 fw-semibold text-success">₦{{ number_format($ownerBalance, 2) }}</dd>

                        <dt class="col-sm-5 text-muted fs-13">Default Markup</dt>
                        <dd class="col-sm-7">{{ $reseller->default_markup_percent }}%</dd>

                        <dt class="col-sm-5 text-muted fs-13">Status</dt>
                        <dd class="col-sm-7">
                            @if($reseller->status === 'active')
                                <span class="badge bg-soft-success text-success">Active</span>
                            @elseif($reseller->status === 'suspended')
                                <span class="badge bg-soft-danger text-danger">Suspended</span>
                            @elseif($reseller->status === 'pending')
                                <span class="badge bg-soft-warning text-warning">Pending</span>
                            @elseif($reseller->status === 'rejected')
                                <span class="badge bg-soft-danger text-danger">Rejected</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5 text-muted fs-13">Server IP</dt>
                        <dd class="col-sm-7">
                            @if($reseller->server_ip)
                                <code class="fs-12 fw-bold text-primary">{{ $reseller->server_ip }}</code>
                                <button class="btn btn-sm btn-link p-0 ms-2" onclick="copyToClipboard('{{ $reseller->server_ip }}')">
                                    <i class="feather-copy"></i>
                                </button>
                            @else
                                <span class="text-muted">Not assigned yet (approve to auto-assign)</span>
                            @endif
                        </dd>

                        @if($reseller->approved_at)
                        <dt class="col-sm-5 text-muted fs-13">Approved At</dt>
                        <dd class="col-sm-7">
                            {{ $reseller->approved_at ? $reseller->approved_at->format('M d, Y H:i') : '—' }}
                        </dd>
                        @endif

                        @if($reseller->rejection_reason)
                        <dt class="col-sm-5 text-muted fs-13">Rejection Reason</dt>
                        <dd class="col-sm-7 text-danger">{{ $reseller->rejection_reason }}</dd>
                        @endif

                        <dt class="col-sm-5 text-muted fs-13">Support Email</dt>
                        <dd class="col-sm-7">{{ $reseller->support_email ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted fs-13">Accent Colour</dt>
                        <dd class="col-sm-7 d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-block"
                                  style="width:16px;height:16px;background:{{ $reseller->primary_color }};border:1px solid #dee2e6;"></span>
                            <code class="fs-12">{{ $reseller->primary_color }}</code>
                        </dd>

                        <dt class="col-sm-5 text-muted fs-13">Created</dt>
                        <dd class="col-sm-7">{{ $reseller->created_at->format('M d, Y') }}</dd>
                    </dl>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.resellers.customers', $reseller) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="feather-users me-1"></i> Customers
                        </a>
                        <a href="{{ route('admin.resellers.orders', $reseller) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="feather-briefcase me-1"></i> Orders
                        </a>
                        <a href="{{ route('admin.resellers.wallet', $reseller) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="feather-credit-card me-1"></i> Wallet
                        </a>
                    </div>
                    
                        <a href="{{ route('admin.resellers.withdrawals', $reseller) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="feather-arrow-up-circle me-1"></i> Withdrawals
                        </a>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-lg-8">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                    <a href="{{ route('admin.resellers.orders', $reseller) }}"
                       class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                       <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Qty</th>
                                <th>Charge</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td>{{ $order->user->name ?? 'Deleted User' }}</td>
                                    <td class="text-truncate" style="max-width:160px;">{{ $order->service_name }}</td>
                                    <td>{{ number_format($order->quantity) }}</td>
                                    <td>₦{{ number_format($order->charge, 2) }}</td>
                                    <td>
                                        @php
                                            $badges = [
                                                'completed'  => 'success',
                                                'processing' => 'info',
                                                'pending'    => 'warning',
                                                'cancelled'  => 'danger',
                                                'partial'    => 'primary',
                                            ];
                                            $color = $badges[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-soft-{{ $color }} text-{{ $color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</main>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Server IP copied to clipboard!');
    });
}
</script>

@include('admin.components.footer')