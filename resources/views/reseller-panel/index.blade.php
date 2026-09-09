@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">My Reseller Panel</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Reseller Panel</li>
                </ul>
            </div>
        </div>

        <div class="main-content">

            {{-- Alert --}}
            @if(session('alert'))
                <div class="alert alert-{{ session('alert.type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show mb-4" role="alert">
                    {{ session('alert.message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
                <div class="alert alert-{{ session('alert.type') === 'success' ? 'success' : 'danger' }} alert-dismissible fade show mb-4" role="alert">
                   WE ADVIDE YOU LOGIN TO YOUR PANEL USING YOUR CURRENT LOGIN DETAILS FOR THIS ACCOUNT, TO SEE FULL BREAK DOWN OF ALL ORDERS, PROFIT, REVENUE, PENDING, PROCESSIONG AND OTHERS
                </div>
            @if(!$reseller)
            {{-- No panel yet --}}
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-lg-8">
                    <div class="card stretch text-center py-5">
                        <div class="card-body">
                            <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-4" style="width:72px;height:72px;font-size:2rem;">
                                <i class="feather-globe"></i>
                            </div>
                            <h4 class="fw-bold mb-2">You don't have a panel yet</h4>
                            <p class="text-muted mb-4 fs-14">
                                Create your own branded proxy reseller panel with a custom domain or subdomain, set your own prices, and start earning today.
                            </p>
                            <a href="{{ route('reseller-panel.create') }}" class="btn btn-primary btn-lg px-5">
                                <i class="feather-plus me-2"></i> Create My Panel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            @else
            {{-- Has a panel --}}
            <div class="row">

                {{-- Status + URL Card --}}
                <div class="col-xxl-4 col-md-6 mb-4">
                    <div class="card stretch stretch-full h-100">
                        <div class="card-body">

                            <form action="{{ route('reseller-panel.update-logo') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            @method('PUT')
                            <label class="form-label fw-semibold">Panel Logo</label>
                            <div class="d-flex align-items-center gap-3">
                                @if($reseller->logo_path)
                                    <img src="{{ $reseller->logo_path }}" alt="logo" style="height:40px;">
                                @endif
                                <input type="file" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                            @error('logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </form>
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="avatar-text avatar-lg bg-gray-200">
                                        <i class="feather-globe"></i>
                                    </div>
                                    <div>
                                        <div class="fs-5 fw-bold text-dark">{{ $reseller->panel_name }}</div>
                                        <span class="fs-12 text-muted">Panel Name</span>
                                    </div>
                                </div>
                                @if($reseller->status === 'active')
                                    <span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i>Active</span>
                                @elseif($reseller->status === 'pending')
                                    <span class="badge bg-soft-warning text-warning"><i class="feather-clock me-1"></i>Pending</span>
                                @elseif($reseller->status === 'rejected')
                                    <span class="badge bg-soft-danger text-danger"><i class="feather-x-circle me-1"></i>Rejected</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger"><i class="feather-x-circle me-1"></i>Suspended</span>
                                @endif
                            </div>
                            <hr class="border-dashed">
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Subdomain</span>
                                    <a href="https://{{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}" target="_blank" class="fw-semibold text-primary">
                                        {{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}
                                        <i class="feather-external-link ms-1" style="font-size:11px;"></i>
                                    </a>
                                </div>
                                @if($reseller->custom_domain)
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Custom Domain</span>
                                    <span class="fw-semibold text-primary">{{ $reseller->custom_domain }}</span>
                                </div>
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Domain Status</span>
                                    @if($reseller->custom_domain_status === 'active')
                                        <span class="badge bg-soft-success text-success">Active</span>
                                    @elseif($reseller->custom_domain_status === 'failed')
                                        <span class="badge bg-soft-danger text-danger">Failed</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">Pending</span>
                                    @endif
                                </div>
                                @if($reseller->custom_domain_verified_at)
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Verified At</span>
                                    <span class="fw-semibold text-dark">{{ $reseller->custom_domain_verified_at->format('M d, Y H:i') }}</span>
                                </div>
                                @endif
                                @if($reseller->custom_domain_status === 'failed' && $reseller->custom_domain_error)
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Domain Error</span>
                                    <span class="fw-semibold text-danger">{{ $reseller->custom_domain_error }}</span>
                                </div>
                                @endif
                                @endif
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Markup</span>
                                    <span class="fw-semibold text-dark">{{ $reseller->default_markup_percent }}%</span>
                                </div>
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Support Email</span>
                                    <span class="fw-semibold text-dark">{{ $reseller->support_email ?? '—' }}</span>
                                </div>
                                @if($reseller->support_telegram)
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Support Telegram</span>
                                    <span class="fw-semibold text-dark">{{ $reseller->support_telegram }}</span>
                                </div>
                                @endif
                                @if($reseller->support_whatsapp)
                                <div class="d-flex justify-content-between fs-13">
                                    <span class="text-muted">Support WhatsApp</span>
                                    <span class="fw-semibold text-dark">{{ $reseller->support_whatsapp }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="col-xxl-8 col-md-6 mb-4">
                    <div class="row h-100">
                        <div class="col-6 mb-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <div class="d-flex gap-3 align-items-center mb-3">
                                        <div class="avatar-text avatar-md bg-gray-200"><i class="feather-users"></i></div>
                                        <span class="fs-13 text-muted">Total Customers</span>
                                    </div>
                                    <div class="fs-3 fw-bold text-dark">{{ $totalCustomers }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <div class="d-flex gap-3 align-items-center mb-3">
                                        <div class="avatar-text avatar-md bg-gray-200"><i class="feather-briefcase"></i></div>
                                        <span class="fs-13 text-muted">Total Orders</span>
                                    </div>
                                    <div class="fs-3 fw-bold text-dark">{{ $totalOrders }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <div class="d-flex gap-3 align-items-center mb-3">
                                        <div class="avatar-text avatar-md bg-gray-200"><i class="feather-dollar-sign"></i></div>
                                        <span class="fs-13 text-muted">Total Revenue</span>
                                    </div>
                                    <div class="fs-3 fw-bold text-dark">₦{{ number_format($totalRevenue, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <div class="d-flex gap-3 align-items-center mb-3">
                                        <div class="avatar-text avatar-md bg-gray-200"><i class="feather-trending-up"></i></div>
                                        <span class="fs-13 text-muted">Total Profit</span>
                                    </div>
                                    <div class="fs-13 fw-bold text-dark">Login into your panel using your {{ config('app.name', 'ProxWorld') }} account details to see your profits and withdraw them</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card stretch stretch-full">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-2">Service Pricing</h6>
                                            <p class="text-muted mb-0 fs-13">Configure your markup percentages per service</p>
                                        </div>
                                        <a href="{{ route('reseller-panel.services') }}" class="btn btn-sm btn-primary">
                                            <i class="feather-edit me-1"></i> Configure
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Settings Card --}}
                <div class="col-xxl-7 col-lg-9">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Edit Panel Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('reseller-panel.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Panel Name <span class="text-danger">*</span></label>
                                    <input type="text" name="panel_name" class="form-control @error('panel_name') is-invalid @enderror"
                                           value="{{ old('panel_name', $reseller->panel_name) }}" />
                                    @error('panel_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Subdomain</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light" value="{{ $reseller->subdomain }}" disabled />
                                        <span class="input-group-text">.{{ config('proxworld.base_domain') }}</span>
                                    </div>
                                    <div class="form-text text-muted">Subdomain cannot be changed. Contact support if needed.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Default Markup <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="default_markup_percent"
                                               class="form-control @error('default_markup_percent') is-invalid @enderror"
                                               min="1" max="200"
                                               value="{{ old('default_markup_percent', $reseller->default_markup_percent) }}" />
                                        <span class="input-group-text">%</span>
                                        @error('default_markup_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Brand Color</label>
                                        <div class="input-group">
                                            <input type="color" name="primary_color"
                                                   class="form-control form-control-color"
                                                   value="{{ old('primary_color', $reseller->primary_color ?? '#6366f1') }}"
                                                   style="max-width:60px;"
                                                   oninput="document.getElementById('edit-color-hex').value=this.value" />
                                            <input type="text" id="edit-color-hex" class="form-control"
                                                   value="{{ old('primary_color', $reseller->primary_color ?? '#6366f1') }}" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Support Email</label>
                                        <input type="email" name="support_email"
                                               class="form-control @error('support_email') is-invalid @enderror"
                                               value="{{ old('support_email', $reseller->support_email) }}" />
                                        @error('support_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Support Telegram <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="text" name="support_telegram"
                                               class="form-control @error('support_telegram') is-invalid @enderror"
                                               placeholder="@yourpanelsupport or t.me/yourpanel"
                                               value="{{ old('support_telegram', $reseller->support_telegram) }}" />
                                        @error('support_telegram')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-semibold">Support WhatsApp <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="text" name="support_whatsapp"
                                               class="form-control @error('support_whatsapp') is-invalid @enderror"
                                               placeholder="+234 800 000 0000"
                                               value="{{ old('support_whatsapp', $reseller->support_whatsapp) }}" />
                                        @error('support_whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="feather-save me-2"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- DNS Configuration Card - Conditional based on status --}}
                @if($reseller->status === 'active')
                <div class="col-12 mt-4">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Custom Domain Setup</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-success mb-4">
                                <i class="feather-check-circle me-2"></i>
                                <strong>Your panel is approved and active!</strong>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Your Panel URL:</strong><br>
                                        <code class="fw-bold">https://{{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}</code>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Your Server IP for DNS:</strong><br>
                                        <code class="fw-bold text-primary" id="server-ip">{{ $reseller->server_ip ?? 'Contact support' }}</code>
                                        @if($reseller->server_ip)
                                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyToClipboard('{{ $reseller->server_ip }}')">
                                                <i class="feather-copy"></i> Copy IP
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($reseller->custom_domain)
                                <div class="alert alert-{{ $reseller->custom_domain_status === 'active' ? 'success' : ($reseller->custom_domain_status === 'failed' ? 'danger' : 'warning') }} mb-4">
                                    <i class="feather-{{ $reseller->custom_domain_status === 'active' ? 'check-circle' : ($reseller->custom_domain_status === 'failed' ? 'alert-triangle' : 'clock') }} me-2"></i>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Domain:</strong><br>
                                            <code>{{ $reseller->custom_domain }}</code>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Status:</strong><br>
                                            @if($reseller->custom_domain_status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($reseller->custom_domain_status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-warning">Pending Verification</span>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            @if($reseller->custom_domain_verified_at)
                                                <strong>Verified At:</strong><br>
                                                {{ $reseller->custom_domain_verified_at->format('M d, Y H:i') }}
                                            @endif
                                        </div>
                                    </div>
                                    @if($reseller->custom_domain_status === 'active')
                                        <div class="mt-2">
                                            <code class="d-block p-2 bg-light rounded">
                                                <i class="feather-link me-1"></i>
                                                <a href="https://{{ $reseller->custom_domain }}" target="_blank">
                                                    https://{{ $reseller->custom_domain }}
                                                </a>
                                            </code>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="fw-bold mb-3"><i class="feather-server me-2"></i>DNS Record to Add</h6>
                                        <div class="bg-light p-3 rounded">
                                            <div class="mb-2">
                                                <span class="badge bg-primary mb-2">A Record</span>
                                                <div class="small">
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Type:</div>
                                                        <div class="col-8"><code>A</code></div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Name/Host:</div>
                                                        <div class="col-8"><code>@</code> or yourdomain.com</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 fw-bold">Value/Points to:</div>
                                                        <div class="col-8">
                                                            <code class="fw-bold text-primary">{{ $reseller->server_ip ?? 'Contact support' }}</code>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4 fw-bold">TTL:</div>
                                                        <div class="col-8"><code>3600</code> (or Automatic)</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 mb-3">
                                        <h6 class="fw-bold mb-3"><i class="feather-alert-circle me-2"></i>Step-by-Step Instructions</h6>
                                        <ol class="small">
                                            <li class="mb-2">Copy the server IP above (click the copy button)</li>
                                            <li class="mb-2">Log into your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.)</li>
                                            <li class="mb-2">Go to DNS settings for your domain</li>
                                            <li class="mb-2">Add a new A record with:
                                                <ul class="mt-1 mb-1">
                                                    <li>Host/Name: <code>@</code> or leave blank</li>
                                                    <li>Points to: <code class="fw-bold">[PASTE THE IP YOU COPIED]</code></li>
                                                </ul>
                                            </li>
                                            <li class="mb-2">Wait 24-48 hours for DNS propagation worldwide</li>
                                            <li class="mb-2">Enter your domain below and click "Verify & Save"</li>
                                            <li>Once verified, your panel will be accessible via your custom domain</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('reseller-panel.update-domain') }}" method="POST" class="mt-3">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Your Custom Domain</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="feather-link"></i></span>
                                        <input type="text"
                                               name="custom_domain"
                                               class="form-control @error('custom_domain') is-invalid @enderror"
                                               placeholder="panel.yourdomain.com"
                                               value="{{ old('custom_domain', $reseller->custom_domain) }}" />
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather-check me-1"></i> Verify & Save
                                        </button>
                                    </div>
                                    @error('custom_domain')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text text-muted">
                                        Enter your domain (e.g., panel.yourdomain.com). Make sure you've added the A record first and waited for DNS propagation.
                                    </div>
                                </div>
                            </form>

                            @if($reseller->custom_domain_status === 'failed' && $reseller->custom_domain_error)
                                <div class="alert alert-danger mt-3">
                                    <i class="feather-alert-triangle me-2"></i>
                                    <strong>Verification failed:</strong> {{ $reseller->custom_domain_error }}
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-danger" onclick="verifyDomainAgain()">
                                            <i class="feather-refresh-cw me-1"></i> Try Again
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <hr class="my-4">

                            <div class="bg-light p-3 rounded">
                                <h6 class="fw-bold mb-2"><i class="feather-shield me-2"></i>SSL Certificate (Free)</h6>
                                <p class="small mb-2">We automatically issue free SSL certificates for all custom domains. Once your DNS is verified, SSL will be active within 1-2 hours.</p>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                <p class="small text-muted mt-2 mb-0">
                                    <i class="feather-check-circle text-success me-1"></i> SSL is included free for all custom domains
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @elseif($reseller->status === 'pending')
                <div class="col-12 mt-4">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Custom Domain Setup</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-0">
                                <i class="feather-alert-circle me-2"></i>
                                <strong>Your panel is pending approval.</strong>
                                <hr class="my-2">
                                <p class="mb-2">Once approved, you'll receive:</p>
                                <ul class="mb-2">
                                    <li>Your unique server IP address for DNS configuration</li>
                                    <li>Ability to connect your custom domain</li>
                                    <li>Full access to your reseller panel</li>
                                </ul>
                                <div class="mt-3 pt-2">
                                    <i class="feather-info me-1"></i>
                                    <strong>Current status:</strong> <span class="badge bg-warning">Pending Review</span><br>
                                    <small>Approval usually takes 24-48 hours. You'll be notified once approved.</small>
                                </div>
                                <div class="mt-3 pt-2">
                                    <i class="feather-link me-1"></i>
                                    <strong>While waiting:</strong> Your panel is accessible at:<br>
                                    <code class="mt-1 d-inline-block">https://{{ $reseller->subdomain }}.{{ config('proxworld.base_domain') }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @elseif($reseller->status === 'rejected')
                <div class="col-12 mt-4">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Panel Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger mb-0">
                                <i class="feather-x-circle me-2"></i>
                                <strong>Your panel application was rejected.</strong>
                                @if($reseller->rejection_reason)
                                    <hr class="my-2">
                                    <p class="mb-0"><strong>Reason:</strong> {{ $reseller->rejection_reason }}</p>
                                @endif
                                <div class="mt-3">
                                    <a href="{{ route('reseller-panel.create') }}" class="btn btn-primary btn-sm">
                                        Try Again
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Customers Section --}}
            @if($customers && $customers->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Recent Customers</h5>
                            <a href="#" class="btn btn-sm btn-primary">Login to your panel to see all orders</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Total Orders</th>
                                            <th>Total Spent</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->orders_count ?? 0 }}</td>
                                            <td>₦{{ number_format($customer->orders_sum_charge ?? 0, 2) }}</td>
                                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Orders Section --}}
            @if($recentOrders && $recentOrders->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Recent Orders</h5>
                            <a href="#" class="btn btn-sm btn-primary">Login to your panel to see all</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Service</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Profit</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                                            <td>{{ $order->service_name }}</td>
                                            <td>{{ number_format($order->quantity) }}</td>
                                            <td>₦{{ number_format($order->charge, 2) }}</td>
                                            <td>₦{{ number_format($order->reseller_profit, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Transactions Section --}}
            @if($recentTransactions && $recentTransactions->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card stretch">
                        <div class="card-header">
                            <h5 class="card-title">Recent Transactions</h5>
                            <a href="#" class="btn btn-sm btn-primary">Login to your panel to see all</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Customer</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->reference }}</td>
                                            <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ ($transaction->type == 'deposit' || $transaction->type == 'credit') ? 'success' : 'danger' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                            <td>{{ Str::limit($transaction->description, 50) }}</td>
                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @endif

        </div>
    </div>
</main>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Server IP copied to clipboard!');
    });
}

function verifyDomainAgain() {
    fetch('{{ route('reseller-panel.verify-domain') }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.verified) {
            location.reload();
        } else {
            alert('Verification failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error verifying domain. Please try again later.');
    });
}
</script>

@include('components.g-footer')