@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Traffic Sources</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Traffic Sources</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.tracking-links.index') }}" class="btn btn-sm btn-primary">
                    <i class="feather-link me-2"></i> Tracking Links
                </a>
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
                        ['Sessions',          number_format($stats['sessions']),   'text-dark',    'primary', 'users'],
                        ['Total Hits',        number_format($stats['hits']),       'text-info',    'info',    'mouse-pointer'],
                        ['Registered Users',  number_format($stats['registered']), 'text-success', 'success', 'user-check'],
                        ['Conversion',        $stats['conversion'] . '%',          'text-warning', 'warning', 'trending-up'],
                    ];
                @endphp
                @foreach($cards as [$label, $value, $textClass, $color, $icon])
                <div class="col-xxl-3 col-md-6 col-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold mb-1">{{ $label }}</div>
                                    <div class="fs-4 fw-bold {{ $textClass }}">{{ $value }}</div>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-{{ $color }} text-{{ $color }}">
                                    <i class="feather-{{ $icon }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Top sources / countries -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header"><h5 class="card-title">Top Sources</h5></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead><tr class="border-b"><th>Source</th><th>Sessions</th><th>Hits</th></tr></thead>
                                    <tbody>
                                        @forelse($topSources as $row)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.traffic.index', array_merge(request()->query(), ['source' => $row->source])) }}">
                                                        <span class="badge bg-soft-primary text-primary">{{ $row->source }}</span>
                                                    </a>
                                                </td>
                                                <td>{{ number_format($row->sessions) }}</td>
                                                <td>{{ number_format($row->hits) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted py-3">No data yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header"><h5 class="card-title">Top Countries</h5></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead><tr class="border-b"><th>Country</th><th>Sessions</th></tr></thead>
                                    <tbody>
                                        @forelse($topCountries as $row)
                                            <tr>
                                                <td>
                                                    @if($row->country_code)
                                                        <a href="{{ route('admin.traffic.index', array_merge(request()->query(), ['country' => $row->country_code])) }}" class="text-dark">
                                                            {{ $row->country_name ?: $row->country_code }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($row->total) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="text-center text-muted py-3">No data yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessions Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Sessions</h5>
                </div>

                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('admin.traffic.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Search IP, user name, email, session token..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="source" class="form-select">
                                <option value="">All Sources</option>
                                @foreach($sources as $src)
                                    <option value="{{ $src }}" {{ request('source') == $src ? 'selected' : '' }}>{{ $src }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="country" class="form-select">
                                <option value="">All Countries</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->country_code }}" {{ request('country') == $c->country_code ? 'selected' : '' }}>
                                        {{ $c->country_name ?: $c->country_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="device_type" class="form-select">
                                <option value="">All Devices</option>
                                @foreach(['desktop', 'mobile', 'tablet'] as $d)
                                    <option value="{{ $d }}" {{ request('device_type') == $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="registered" class="form-select">
                                <option value="">Guests + Users</option>
                                <option value="yes" {{ request('registered') == 'yes' ? 'selected' : '' }}>Registered only</option>
                                <option value="no" {{ request('registered') == 'no' ? 'selected' : '' }}>Not registered</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" title="Filter"><i class="feather-filter"></i></button>
                                <a href="{{ route('admin.traffic.index') }}" class="btn btn-secondary" title="Clear"><i class="feather-x"></i></a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="border-b">
                                    <th>Session</th>
                                    <th>First Source</th>
                                    <th>Country</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>User</th>
                                    <th>Hits</th>
                                    <th>First Seen</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sessions as $s)
                                @php
                                    $deviceIcon = ['mobile' => 'smartphone', 'tablet' => 'tablet', 'desktop' => 'monitor'][$s->device_type] ?? 'monitor';
                                @endphp
                                    <tr style="cursor:pointer" onclick="if(!event.target.closest('a,button')) window.location='{{ route('admin.traffic.show', $s->id) }}'">
                                        <td><code class="fs-11">{{ $s->id }} · {{ substr($s->visitor_token, 0, 8) }}</code></td>
                                        <td>
                                            <span class="badge {{ $s->first_source === 'direct' ? 'bg-soft-secondary text-secondary' : 'bg-soft-primary text-primary' }}">{{ $s->first_source }}</span>
                                            @if($s->first_campaign)
                                                <small class="d-block text-muted fs-11">{{ $s->first_campaign }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $s->country_name ?: ($s->country_code ?: 'Unknown') }}</td>
                                        <td><code class="fs-11">{{ $s->ip_address ?: '—' }}</code></td>
                                        <td>
                                            <i class="feather-{{ $deviceIcon }} me-1"></i>{{ $s->device_name ?: ucfirst($s->device_type ?? '—') }}
                                            <small class="d-block text-muted fs-11">{{ $s->os }} · {{ $s->browser }}</small>
                                        </td>
                                        <td>
                                            @if($s->user)
                                                <a href="{{ route('admin.customers.show', $s->user_id) }}" class="text-dark">{{ $s->user->name }}</a>
                                                <small class="d-block text-muted fs-11">{{ $s->user->email }}</small>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">Guest</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($s->hits_count) }}</td>
                                        <td>{{ $s->created_at->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.traffic.show', $s->id) }}" class="btn btn-sm btn-light-brand">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="feather-inbox fs-3 d-block mb-2"></i> No sessions found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($sessions->hasPages())
                <div class="card-footer">{{ $sessions->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</main>

@include('admin.components.footer')