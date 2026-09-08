@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Ticket #{{ substr($ticket->id, 0, 8) }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.support.index') }}">Support</a></li>
                    <li class="breadcrumb-item">{{ $ticket->subject }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success" id="autoRefreshStatus">
                        <i class="feather-check-circle me-1"></i> Auto-refresh: ON
                    </span>
                    <a href="{{ route('admin.customers.show', $ticket->user_id) }}" 
                       class="btn btn-sm btn-light-brand">
                        <i class="feather-user me-2"></i> View Customer
                    </a>
                </div>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show">
                <i class="feather-{{ session('alert')['type'] == 'success' ? 'check-circle' : 'alert-circle' }} me-2"></i>
                {{ session('alert')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                
                <!-- Chat Area -->
                <div class="col-xxl-8 col-xl-7">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">{{ $ticket->subject }}</h5>
                            <span class="badge {{ $ticket->getStatusBadgeClass() }}" id="ticketStatus">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                        
                        <!-- Messages Container -->
                        <div class="card-body" style="max-height: 500px; overflow-y: auto;" id="messagesContainer">
                            <div id="messagesList">
                                @include('admin.support.partials.messages', ['messages' => $messages])
                            </div>
                        </div>

                        <!-- Reply Form -->
                        @if(auth('admin')->user()->canManageSupport())
                        <div class="card-footer">
                            @if($ticket->isClosed())
                                <div class="alert alert-warning mb-0">
                                    <i class="feather-lock me-2"></i>
                                    This ticket is closed. 
                                    <form method="POST" action="{{ route('admin.support.reopen', $ticket->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning ms-2">
                                            <i class="feather-rotate-cw me-1"></i> Reopen Ticket
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}" id="replyForm">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="message" 
                                                  id="messageInput"
                                                  class="form-control" 
                                                  rows="4" 
                                                  placeholder="Type your reply here..."
                                                  required></textarea>
                                        @error('message')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="feather-info me-1"></i> Customer will be notified via email
                                        </small>
                                        <button type="submit" class="btn btn-primary" id="sendBtn">
                                            <i class="feather-send me-2"></i> Send Reply
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                        @endif

                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xxl-4 col-xl-5">
                    
                    <!-- Customer Info -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary me-3">
                                    {{ substr($ticket->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $ticket->user->name }}</h6>
                                    <p class="fs-12 text-muted mb-0">{{ $ticket->user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Balance:</span>
                                    <strong class="text-success">₦{{ number_format($ticket->user->balance ?? 0, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Member Since:</span>
                                    <strong>{{ $ticket->user->created_at->format('M d, Y') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket Details -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Ticket Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Ticket ID</small>
                                <code>#{{ substr($ticket->id, 0, 8) }}</code>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Status</small>
                                <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Created</small>
                                <strong>{{ $ticket->created_at->format('M d, Y H:i') }}</strong>
                                <p class="fs-11 text-muted mb-0">{{ $ticket->created_at->diffForHumans() }}</p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Last Updated</small>
                                <strong>{{ $ticket->updated_at->format('M d, Y H:i') }}</strong>
                                <p class="fs-11 text-muted mb-0">{{ $ticket->updated_at->diffForHumans() }}</p>
                            </div>

                            <div>
                                <small class="text-muted d-block mb-1">Total Messages</small>
                                <strong id="messageCount">{{ $messages->count() }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(auth('admin')->user()->canManageSupport())
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            
                            <!-- Update Status -->
                            <form method="POST" action="{{ route('admin.support.status', $ticket->id) }}" class="mb-3">
                                @csrf
                                <label class="form-label fw-bold">Change Status</label>
                                <div class="input-group">
                                    <select name="status" class="form-select" required>
                                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>

                            <!-- Quick Close -->
                            @if(!$ticket->isClosed())
                                <form method="POST" 
                                      action="{{ route('admin.support.close', $ticket->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to close this ticket?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 mb-2">
                                        <i class="feather-check-circle me-2"></i> Close Ticket
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.support.reopen', $ticket->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100 mb-2">
                                        <i class="feather-rotate-cw me-2"></i> Reopen Ticket
                                    </button>
                                </form>
                            @endif

                            <!-- Delete (Super Admin Only) -->
                            @if(auth('admin')->user()->isSuperAdmin())
                                <form method="POST" 
                                      action="{{ route('admin.support.destroy', $ticket->id) }}" 
                                      onsubmit="return confirm('Delete this ticket permanently? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="feather-trash-2 me-2"></i> Delete Ticket
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                    @endif

                </div>

            </div>
        </div>

    </div>
</main>

<!-- Auto scroll and refresh script -->
<script>
    let lastMessageId = '{{ $messages->last()->id ?? "" }}';
    let isUserScrolling = false;
    let refreshInterval;

    // Scroll to bottom function
    function scrollToBottom(force = false) {
        const container = document.getElementById('messagesContainer');
        if (container && (!isUserScrolling || force)) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Detect if user is scrolling up
    document.getElementById('messagesContainer')?.addEventListener('scroll', function() {
        const container = this;
        const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 50;
        isUserScrolling = !isAtBottom;
    });

    // Fetch new messages
    function fetchMessages() {
        fetch('{{ route('admin.support.fetch-messages', $ticket->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Check if there are new messages
                    if (data.lastMessageId !== lastMessageId) {
                        // Update messages
                        document.getElementById('messagesList').innerHTML = data.html;
                        lastMessageId = data.lastMessageId;
                        
                        // Update message count
                        document.getElementById('messageCount').textContent = data.messageCount;
                        
                        // Scroll to bottom if not manually scrolling
                        scrollToBottom();
                        
                        // Play notification sound (optional)
                        playNotificationSound();
                    }
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    // Play notification sound (optional)
    function playNotificationSound() {
        // You can add a subtle notification sound here
        // const audio = new Audio('/sounds/notification.mp3');
        // audio.play().catch(e => console.log('Audio play failed'));
    }

    // Handle form submission with AJAX
    document.getElementById('replyForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const sendBtn = document.getElementById('sendBtn');
        const messageInput = document.getElementById('messageInput');
        
        // Disable button
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="feather-loader me-2 spinner-border spinner-border-sm"></i> Sending...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear input
                messageInput.value = '';

                // Show success message
                showAlert('success', 'Your message has been sent!');
                
                // Fetch new messages immediately
                fetchMessages();
                
                // Force scroll to bottom
                setTimeout(() => scrollToBottom(true), 100);
            }
        })
        .catch(error => console.error('Error sending message:', error))
        .finally(() => {
            // Re-enable button
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="feather-send me-2"></i> Send Reply';
        });
    });

    // Start auto-refresh on page load
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom(true);
        
        // Refresh every 5 seconds
        refreshInterval = setInterval(fetchMessages, 5000);
    });

    // Stop refresh when page is hidden (save resources)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(fetchMessages, 5000);
            fetchMessages(); // Fetch immediately when tab becomes active
        }
    });
</script>

@include('admin.components.footer')