<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ substr($ticket->id, 0, 8) }} - Support</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/B.png') }}" />
</head>
<body>
    @include('components.g-header')
    @include('components.nav')

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Ticket #{{ substr($ticket->id, 0, 8) }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('support.index') }}">Support</a></li>
                        <li class="breadcrumb-item">View</li>
                    </ul>
                </div>
            </div>

            <div class="main-content">
                <div class="row">
                    
                    <!-- Chat Area (Left) -->
                    <div class="col-lg-8">
                        <div class="card stretch stretch-full">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-0">{{ $ticket->subject }}</h5>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $ticket->status == 'open' ? 'primary' : ($ticket->status == 'in_progress' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="badge bg-success" id="autoRefreshStatus">
                                        <i class="feather-check-circle" style="width: 12px; height: 12px;"></i> Live
                                    </span>
                                </div>
                            </div>
                            <div class="card-body" style="max-height: 600px; overflow-y: auto;" id="chatMessages">
                                
                                <!-- Messages Loop: Shows all replies dynamically -->
                                <div class="d-flex flex-column gap-4" id="messagesList">
                                    @include('support.partials.messages', ['messages' => $messages])
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Box (Right) -->
                    <div class="col-lg-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Reply</h5>
                            </div>
                            <div class="card-body">
                                <!-- Success/Error Alert -->
                                <div id="alertContainer"></div>

                                @if($ticket->status !== 'closed')
                                    <form id="replyForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Your Message</label>
                                            <textarea name="message" id="messageInput" class="form-control" rows="6" required placeholder="Type your reply here..."></textarea>
                                            <small class="text-danger" id="messageError" style="display: none;"></small>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100" id="sendBtn">
                                            <i class="feather-send me-2"></i> Send Reply
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="feather-lock me-2"></i>
                                        This ticket is closed. Our support team will need to reopen it for you to reply.
                                    </div>
                                @endif
                                
                                <div class="alert alert-info mt-3 mb-0">
                                    <small><i class="feather-info me-2"></i> Please note that a copy of every message sent by our support team will also be delivered to your email.</small>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Info Card -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Ticket Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Created</small>
                                    <p class="mb-0">{{ $ticket->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Last Updated</small>
                                    <p class="mb-0">{{ $ticket->updated_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div>
                                    <small class="text-muted">Total Messages</small>
                                    <p class="mb-0" id="messageCount">{{ $messages->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    @include('components.g-footer')

    <style>
        .hover-bg-light:hover { background-color: #f8f9fa; }
        
        /* Smooth scrolling for chat */
        #chatMessages {
            scroll-behavior: smooth;
        }

        /* Pulse animation for live indicator */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        #autoRefreshStatus i {
            animation: pulse 2s ease-in-out infinite;
        }
    </style>

    <script>
        let lastMessageId = '{{ $messages->last()->id ?? "" }}';
        let isUserScrolling = false;
        let refreshInterval;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Scroll to bottom function
        function scrollToBottom(force = false) {
            const container = document.getElementById('chatMessages');
            if (container && (!isUserScrolling || force)) {
                container.scrollTop = container.scrollHeight;
            }
        }

        // Detect if user is scrolling up
        const chatContainer = document.getElementById('chatMessages');
        if (chatContainer) {
            chatContainer.addEventListener('scroll', function() {
                const container = this;
                const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 50;
                isUserScrolling = !isAtBottom;
            });
        }

        // Show alert message
        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'check-circle' : 'alert-circle';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show">
                    <i class="feather-${icon} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 3000);
        }

        // Fetch new messages
        function fetchMessages() {
            fetch('{{ route('support.fetch-messages', $ticket->id) }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
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
                        // playNotificationSound();
                    }
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
        }

        // Handle form submission with AJAX
        const replyForm = document.getElementById('replyForm');
        if (replyForm) {
            replyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const messageInput = document.getElementById('messageInput');
                const sendBtn = document.getElementById('sendBtn');
                const messageError = document.getElementById('messageError');
                const message = messageInput.value.trim();

                // Validate
                if (!message) {
                    messageError.textContent = 'Please enter a message';
                    messageError.style.display = 'block';
                    return;
                }

                // Hide error
                messageError.style.display = 'none';
                
                // Disable button
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending...';
                
                // Send message
                fetch('{{ route('support.reply', $ticket->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: message
                    })
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
                    } else {
                        showAlert('error', data.message || 'Failed to send message');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    showAlert('error', 'An error occurred. Please try again.');
                })
                .finally(() => {
                    // Re-enable button
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="feather-send me-2"></i> Send Reply';
                });
            });
        }

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
</body>
</html>