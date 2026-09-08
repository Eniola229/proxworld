@forelse($messages as $msg)
    <div class="d-flex {{ $msg->is_admin ? 'justify-content-start' : 'justify-content-end' }}">
        
        <div class="d-flex flex-column {{ $msg->is_admin ? 'align-items-start' : 'align-items-end' }}">
            
            <!-- Message Bubble -->
            <div class="p-3 rounded {{ $msg->is_admin ? 'bg-light text-dark' : 'bg-primary text-white' }}" style="max-width: 70%;">
                {{ $msg->message }}
            </div>

            <small class="text-muted mb-1">
                @if($msg->is_admin)
                    <strong class="text-primary">{{ $msg->admin->name ?? 'Support' }}</strong>
                @else
                    <strong>You</strong>
                @endif
                • {{ $msg->created_at->format('H:i') }}
            </small>

        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="feather-message-circle" style="font-size: 48px;"></i>
        <p class="mt-3">No messages yet. Start the conversation!</p>
    </div>
@endforelse