@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Exchange Rates</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.providers.index') }}">Providers</a></li>
                    <li class="breadcrumb-item">Exchange Rates</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto d-flex gap-2">
                <form method="POST" action="{{ route('admin.exchange-rates.refresh-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="feather-refresh-cw me-1"></i> Refresh All Rates
                    </button>
                </form>
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
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">

                {{-- Provider Currency Summary --}}
                <div class="col-12 mb-3">
                    <h6 class="fw-bold text-dark">Provider Currencies</h6>
                </div>

                @foreach($providers as $provider)
                <div class="col-xxl-3 col-md-6 mb-3">
                    <div class="card stretch stretch-full {{ $provider->is_active ? '' : 'opacity-50' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $provider->name }}</h6>
                                    <span class="badge bg-soft-primary text-primary fs-12">
                                        {{ strtoupper($provider->currency) }}
                                    </span>
                                    @if(strtoupper($provider->currency) === 'NGN')
                                        <span class="badge bg-soft-success text-success ms-1">No conversion needed</span>
                                    @endif
                                </div>
                                <span class="badge {{ $provider->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                                    {{ $provider->is_active ? 'Active' : 'Off' }}
                                </span>
                            </div>

                            @if(strtoupper($provider->currency) !== 'NGN')
                                @php
                                    $cachedRate = $rates->where('from_currency', strtoupper($provider->currency))
                                                       ->where('to_currency', 'NGN')
                                                       ->first();
                                @endphp
                                @if($cachedRate)
                                    <div class="fs-3 fw-bold text-dark mb-1">
                                        ₦{{ number_format($cachedRate->rate, 2) }}
                                        <small class="text-muted fs-12">/ 1 {{ strtoupper($provider->currency) }}</small>
                                    </div>
                                    <div class="text-muted fs-11">
                                        Updated {{ $cachedRate->fetched_at?->diffForHumans() ?? 'never' }}
                                        @if($cachedRate->isStale())
                                            <span class="badge bg-warning text-dark ms-1">Stale</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-warning fw-bold mb-1">Rate not cached yet</div>
                                    <div class="text-muted fs-11">Click refresh to fetch live rate</div>
                                @endif

                                <form method="POST" action="{{ route('admin.exchange-rates.refresh') }}" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="from" value="{{ $provider->currency }}">
                                    <input type="hidden" name="to" value="NGN">
                                    <button type="submit" class="btn btn-sm btn-light w-100">
                                        <i class="feather-refresh-cw me-1"></i> Refresh Rate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- Manual Refresh Form --}}
                <div class="col-12 mt-2 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Manual Rate Lookup</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.exchange-rates.refresh') }}" class="row g-3 align-items-end">
                                @csrf
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">From Currency</label>
                                    <input type="text" name="from" class="form-control text-uppercase"
                                           placeholder="USD" maxlength="3" style="text-transform:uppercase"
                                           value="{{ old('from', 'USD') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">To Currency</label>
                                    <input type="text" name="to" class="form-control text-uppercase"
                                           placeholder="NGN" maxlength="3" style="text-transform:uppercase"
                                           value="{{ old('to', 'NGN') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-search me-1"></i> Fetch Rate
                                    </button>
                                </div>
                                <div class="col-12">
                                    <small class="text-muted">
                                        Uses <strong>ExchangeRate-API</strong> (primary) with
                                        <strong>Floatrates</strong> as fallback. Rates are cached for 30 minutes.
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- All Cached Rates Table --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Cached Exchange Rates</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Rate</th>
                                            <th>Example</th>
                                            <th>Source</th>
                                            <th>Last Updated</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rates as $rate)
                                        <tr>
                                            <td><span class="badge bg-soft-primary text-primary fw-bold">{{ $rate->from_currency }}</span></td>
                                            <td><span class="badge bg-soft-success text-success fw-bold">{{ $rate->to_currency }}</span></td>
                                            <td class="fw-bold">{{ number_format($rate->rate, 4) }}</td>
                                            <td class="text-muted fs-12">
                                                1 {{ $rate->from_currency }} =
                                                @if($rate->to_currency === 'NGN') ₦ @endif
                                                {{ number_format($rate->rate, 2) }} {{ $rate->to_currency }}
                                            </td>
                                            <td><code class="fs-11">{{ $rate->source }}</code></td>
                                            <td class="fs-12 text-muted">
                                                {{ $rate->fetched_at?->format('M d, Y H:i') ?? '—' }}
                                                <br>
                                                <small>{{ $rate->fetched_at?->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($rate->isStale())
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="feather-alert-triangle me-1"></i> Stale
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check me-1"></i> Fresh
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('admin.exchange-rates.refresh') }}">
                                                    @csrf
                                                    <input type="hidden" name="from" value="{{ $rate->from_currency }}">
                                                    <input type="hidden" name="to" value="{{ $rate->to_currency }}">
                                                    <button type="submit" class="btn btn-sm btn-light">
                                                        <i class="feather-refresh-cw"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="feather-dollar-sign fs-3 d-block mb-2"></i>
                                                No rates cached yet. Click "Refresh All Rates" to fetch them.
                                            </td>
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

@include('admin.components.footer')