@forelse($messages as $message)
    <div class="d-flex mb-4 {{ $message->is_admin ? 'justify-content-end' : '' }}">
        @if(!$message->is_admin)
            <div class="avatar-text avatar-md bg-soft-primary text-primary me-3">
                {{ substr($message->user->name ?? 'U', 0, 2) }}
            </div>
        @endif
        
        <div style="max-width: 70%;">
            <div class="card {{ $message->is_admin ? 'bg-primary text-white' : 'bg-light' }} mb-1">
                <div class="card-body p-3">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $message->message }}</p>
                </div>
            </div>
            <small class="{{ $message->is_admin ? 'text-end' : '' }} d-block text-muted">
                {{ $message->is_admin ? 'Admin' : ($message->user->name ?? 'Customer') }} 
                • {{ $message->created_at->format('M d, Y H:i') }}
            </small>
        </div>

        @if($message->is_admin)
            <div class="avatar-text avatar-md bg-soft-danger text-danger ms-3">
                AD
            </div>
        @endif
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="feather-message-circle fs-3 d-block mb-2"></i>
        <p>No messages yet</p>
    </div>
@endforelse