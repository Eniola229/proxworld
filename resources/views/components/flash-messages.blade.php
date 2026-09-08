{{-- Global flash message toasts. Handles two flash conventions used across
     the app: session('alert') => ['type' => ..., 'message' => ...], and
     plain session('success') / session('error') / session('warning') / session('info').
     Either one, from any controller, will now actually render. --}}

@php
    $flashes = [];

    if (session('alert')) {
        $flashes[] = [
            'type' => session('alert')['type'] ?? 'info',
            'message' => session('alert')['message'] ?? '',
        ];
    }

    foreach (['success', 'error', 'warning', 'info'] as $key) {
        if (session($key)) {
            $flashes[] = ['type' => $key, 'message' => session($key)];
        }
    }

    $styles = [
        'success' => ['icon' => 'feather-check-circle', 'badge' => 'bg-success'],
        'error'   => ['icon' => 'feather-x-circle',     'badge' => 'bg-danger'],
        'warning' => ['icon' => 'feather-alert-triangle','badge' => 'bg-warning'],
        'info'    => ['icon' => 'feather-info',         'badge' => 'bg-info'],
    ];
@endphp

@if(count($flashes) || $errors->any())
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 1080;">

        @foreach($flashes as $flash)
            @php $style = $styles[$flash['type']] ?? $styles['info']; @endphp
            <div class="toast show bg-white shadow-lg border-0 mb-2" role="alert" data-bs-autohide="true" data-bs-delay="6000">
                <div class="toast-header">
                    <span class="avatar-text avatar-xs {{ $style['badge'] }} text-white rounded-circle me-2">
                        <i class="{{ $style['icon'] }}"></i>
                    </span>
                    <strong class="me-auto text-uppercase">{{ ucfirst($flash['type']) }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">{{ $flash['message'] }}</div>
            </div>
        @endforeach

        @if($errors->any())
            <div class="toast show bg-white shadow-lg border-0 mb-2" role="alert" data-bs-autohide="true" data-bs-delay="8000">
                <div class="toast-header">
                    <span class="avatar-text avatar-xs bg-danger text-white rounded-circle me-2">
                        <i class="feather-x-circle"></i>
                    </span>
                    <strong class="me-auto text-uppercase">Error</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

    </div>
@endif