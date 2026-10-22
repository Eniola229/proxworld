@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Session Details</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.traffic.index') }}">Traffic Sources</a></li>
                    <li class="breadcrumb-item" title="{{ $trackSession->id }}">{{ substr($trackSession->id, 0, 8) }}</li>
                </ul>
            </div>
            @if($trackSession->user_id)
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.customers.show', $trackSession->user_id) }}" class="btn btn-sm btn-light-brand">
                    <i class="feather-user me-2"></i> View User
                </a>
            </div>
            @endif
        </div>

        <div class="main-content">
            <div class="row">

                <div class="col-xxl-4 col-xl-6">

                    {{-- Session Information --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Session Information</h5></div>
                        <div class="card-body">
                            @php
                                $rows = [
                                    ['Session ID',    '<code class="fs-11" title="' . e($trackSession->id) . '">' . e(substr($trackSession->id, 0, 8)) . '</code>'],
                                    ['Visitor Token', '<code class="fs-11" style="word-break:break-all;">' . e($trackSession->visitor_token) . '</code>'],
                                    ['First Source',  '<span class="badge bg-soft-primary text-primary">' . e($trackSession->first_source) . '</span>'],
                                    ['Medium',        e($trackSession->first_medium ?: '—')],
                                    ['Campaign',      e($trackSession->first_campaign ?: '—')],
                                    ['Total Hits',    number_format($trackSession->hits_count)],
                                    ['First Seen',    $trackSession->created_at->format('M d, Y H:i')],
                                    ['Last Hit',      $trackSession->last_hit_at ? $trackSession->last_hit_at->format('M d, Y H:i') : '—'],
                                    ['Linked To User', $trackSession->linked_at ? $trackSession->linked_at->format('M d, Y H:i') : 'Not registered'],
                                ];
                            @endphp
                            @foreach($rows as [$label, $value])
                                <div class="{{ $loop->last ? '' : 'mb-3 pb-3 border-bottom' }}">
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="fs-12 text-muted text-nowrap">{{ $label }}:</span>
                                        <span class="fs-12 fw-bold text-end">{!! $value !!}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Location & Device --}}
                    <div class="card mt-3">
                        <div class="card-header"><h5 class="card-title">Location & Device</h5></div>
                        <div class="card-body">
                            @php
                                $rows = [
                                    ['IP Address',  '<code class="fs-11" style="word-break:break-all;">' . e($trackSession->ip_address ?: '—') . '</code>'],
                                    ['Country',     e($trackSession->country_name ?: ($trackSession->country_code ?: 'Unknown'))],
                                    ['Device Type', e(ucfirst($trackSession->device_type ?: '—'))],
                                    ['Device',      e($trackSession->device_name ?: '—')],
                                    ['OS',          e($trackSession->os ?: '—')],
                                    ['Browser',     e($trackSession->browser ?: '—')],
                                ];
                            @endphp
                            @foreach($rows as [$label, $value])
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="fs-12 text-muted text-nowrap">{{ $label }}:</span>
                                        <span class="fs-12 fw-bold text-end">{!! $value !!}</span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="fs-11 text-muted" style="word-break:break-all;">{{ $trackSession->user_agent }}</div>
                        </div>
                    </div>

                    {{-- User --}}
                    <div class="card mt-3">
                        <div class="card-header"><h5 class="card-title">User</h5></div>
                        <div class="card-body">
                            @if($trackSession->user)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-text avatar-lg bg-soft-primary text-primary me-3">
                                        {{ strtoupper(mb_substr($trackSession->user->name, 0, 2)) }}
                                    </div>
                                    <div class="text-truncate">
                                        <h6 class="mb-1 text-truncate">{{ $trackSession->user->name }}</h6>
                                        <p class="fs-12 text-muted mb-0 text-truncate" title="{{ $trackSession->user->email }}">{{ $trackSession->user->email }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.customers.show', $trackSession->user_id) }}" class="btn btn-sm btn-primary w-100">
                                    <i class="feather-user me-2"></i> View User
                                </a>
                            @else
                                <div class="text-muted fs-12">This visitor has not registered yet.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Hits --}}
                <div class="col-xxl-8 col-xl-6">
                    <div class="card">
                        <div class="card-header"><h5 class="card-title">Hits ({{ number_format($hits->total()) }})</h5></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Date</th>
                                            <th>Source</th>
                                            <th>Medium / Campaign</th>
                                            <th>Landing URL</th>
                                            <th>IP</th>
                                            <th>Country</th>
                                            <th>Device</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($hits as $hit)
                                            <tr>
                                                <td class="fs-11 text-nowrap">{{ $hit->created_at->format('M d, Y H:i') }}</td>
                                                <td><span class="badge bg-soft-primary text-primary">{{ $hit->source }}</span></td>
                                                <td class="fs-11">{{ $hit->medium ?: '—' }} / {{ $hit->campaign ?: '—' }}</td>
                                                <td>
                                                    <span class="text-truncate d-inline-block fs-11" style="max-width:220px;" title="{{ $hit->landing_url }}">{{ $hit->landing_url }}</span>
                                                    @if($hit->referrer)
                                                        <small class="d-block text-muted fs-11 text-truncate" style="max-width:220px;" title="{{ $hit->referrer }}">from {{ $hit->referrer }}</small>
                                                    @endif
                                                </td>
                                                <td><code class="fs-11">{{ $hit->ip_address ?: '—' }}</code></td>
                                                <td>{{ $hit->country_name ?: ($hit->country_code ?: 'Unknown') }}</td>
                                                <td class="fs-11">{{ $hit->device_name ?: ucfirst($hit->device_type ?? '—') }}<small class="d-block text-muted">{{ $hit->os }} · {{ $hit->browser }}</small></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-3">No hits recorded.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($hits->hasPages())
                        <div class="card-footer">{{ $hits->links() }}</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')