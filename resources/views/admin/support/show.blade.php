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
                    <li class="breadcrumb-item">{{ \Illuminate\Support\Str::limit($ticket->subject, 60) }}</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success" id="autoRefreshStatus">
                        <i class="feather-check-circle me-1"></i> Live
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
                            <h5 class="card-title">{{ \Illuminate\Support\Str::limit($ticket->subject, 80) }}</h5>
                            <span class="badge {{ $ticket->getStatusBadgeClass() }}" id="ticketStatus">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>

                        <!-- Messages Container (rendered by JS) -->
                        <div class="card-body adm-chat" style="height: 500px; overflow-y: auto;" id="messagesContainer">
                            <div id="messagesList" class="d-flex flex-column gap-3"></div>
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
                                <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}" id="replyForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="message"
                                                  id="messageInput"
                                                  class="form-control"
                                                  rows="4"
                                                  maxlength="5000"
                                                  placeholder="Type your reply here... (Enter to send, Shift+Enter for a new line)"></textarea>

                                        <!-- Attachment preview -->
                                        <div id="attachPreview" class="adm-attach-preview" style="display:none">
                                            <img id="attachThumb" alt="" style="display:none">
                                            <span id="attachIcon" class="adm-attach-icon" style="display:none"><i class="feather-video"></i></span>
                                            <span id="attachName" class="text-truncate"></span>
                                            <button type="button" class="btn-close ms-auto" id="attachRemove" aria-label="Remove"></button>
                                        </div>

                                        <small class="text-danger" id="replyError" style="display:none"></small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <input type="file" id="attachInput" accept="image/*,video/*" hidden>
                                            <button type="button" class="btn btn-sm btn-light-brand" id="attachBtn">
                                                <i class="feather-paperclip me-1"></i> Attach image / video
                                            </button>
                                            <small class="text-muted d-none d-md-inline">
                                                <i class="feather-info me-1"></i> Customer will be notified via email
                                            </small>
                                        </div>
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
                                    <strong class="text-success">@money($ticket->user->balance ?? 0, $ticket->user->preferred_currency ?? 'NGN')</strong>
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

                    <!-- Customer Rating -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title">Customer Rating</h5>
                        </div>
                        <div class="card-body">
                            @if($ticket->isRated())
                                <div class="mb-2">
                                    <span class="text-warning fs-4" style="letter-spacing:2px;">{{ str_repeat('★', $ticket->rating) }}<span class="text-muted">{{ str_repeat('★', 5 - $ticket->rating) }}</span></span>
                                    <strong class="ms-2">{{ $ticket->rating }} / 5</strong>
                                </div>
                                @if($ticket->rating_comment)
                                    <div class="bg-soft-secondary p-3 rounded mb-2" style="white-space:pre-wrap; word-break:break-word;">{{ $ticket->rating_comment }}</div>
                                @endif
                                <p class="fs-11 text-muted mb-0">Rated {{ $ticket->rated_at?->format('M d, Y H:i') }}</p>
                            @elseif($ticket->isClosed())
                                <p class="text-muted mb-0"><i class="feather-clock me-1"></i> Closed. Waiting for the customer to rate.</p>
                            @else
                                <p class="text-muted mb-0">The customer can rate this ticket once it is closed.</p>
                            @endif
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
                                      onsubmit="return confirm('Delete this ticket permanently? Uploaded images and videos will be removed too. This cannot be undone.')">
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

<style>
    .adm-chat { background: rgba(128,128,128,.06); }
    .adm-m { display: flex; flex-direction: column; gap: 4px; max-width: 75%; }
    /* Customer = left, grey. Staff (admins) = right, primary colour. */
    .adm-m.cust  { align-self: flex-start; align-items: flex-start; }
    .adm-m.staff { align-self: flex-end;   align-items: flex-end; }
    .adm-bubble { padding: 10px 14px; border-radius: 14px; white-space: pre-wrap; word-break: break-word; }
    .adm-m.cust  .adm-bubble { background: #fff; color: #212529; border: 1px solid #e9ecef; border-bottom-left-radius: 4px; }
    .adm-m.staff .adm-bubble { background: var(--bs-primary, #0d6efd); color: #fff; border-bottom-right-radius: 4px; }
    .adm-meta { font-size: 11px; color: #6c757d; margin-top: 3px; }
    .adm-media { display: block; max-width: 100%; max-height: 320px; border-radius: 12px; background: #000; }
    .adm-attach-preview { display: flex; align-items: center; gap: 10px; margin-top: 8px; padding: 6px 10px; border: 1px solid rgba(128,128,128,.3); border-radius: 8px; }
    .adm-attach-preview img { width: 40px; height: 40px; object-fit: cover; border-radius: 6px; }
    .adm-attach-icon { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; background: rgba(128,128,128,.15); font-size: 18px; }
    .adm-attach-preview #attachName { min-width: 0; max-width: 60%; font-size: 13px; }
</style>

<script>
    (function () {
        const MESSAGES_URL = @json(route('admin.support.fetch-messages', $ticket->id));
        const CSRF = @json(csrf_token());
        const MAX_IMG = 10 * 1024 * 1024;
        const MAX_VID = 50 * 1024 * 1024;
        const list = document.getElementById('messagesList');
        const box = document.getElementById('messagesContainer');
        const countEl = document.getElementById('messageCount');
        const seen = new Set();
        let lastId = null;
        let polling = false;
        let stick = true;

        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        box.addEventListener('scroll', () => { stick = box.scrollHeight - box.scrollTop - box.clientHeight < 80; });
        // Images/videos change height once loaded; stay pinned to the bottom if the admin was there.
        box.addEventListener('load', () => { if (stick) box.scrollTop = box.scrollHeight; }, true);
        box.addEventListener('loadedmetadata', () => { if (stick) box.scrollTop = box.scrollHeight; }, true);

        function mediaHtml(a) {
            if (!a || !a.url) return '';
            if (a.type === 'video') {
                return `<video class="adm-media" src="${esc(a.url)}" controls preload="metadata" playsinline></video>`;
            }
            return `<a href="${esc(a.url)}" target="_blank" rel="noopener"><img class="adm-media" src="${esc(a.url)}" alt="${esc(a.name || 'Image')}" loading="lazy"></a>`;
        }

        function addMessages(msgs, force = false, track = true) {
            const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
            const first = !lastId;
            let added = false;

            msgs.forEach((m) => {
                if (seen.has(m.id)) return;
                seen.add(m.id);
                if (track) lastId = m.id;
                list.insertAdjacentHTML('beforeend', `
                    <div class="adm-m ${m.is_admin ? 'staff' : 'cust'}">
                        ${mediaHtml(m.attachment)}
                        ${m.message ? `<div class="adm-bubble">${esc(m.message)}</div>` : ''}
                        <div class="adm-meta"><strong>${esc(m.name)}</strong> · ${esc(m.time)}</div>
                    </div>`);
                added = true;
            });

            if (added) {
                countEl.textContent = seen.size;
                if (nearBottom || first || force) box.scrollTop = box.scrollHeight;
            }
            if (!seen.size && !list.querySelector('.adm-empty')) {
                list.innerHTML = '<div class="adm-empty text-center text-muted py-5">No messages yet.</div>';
            } else if (seen.size) {
                list.querySelector('.adm-empty')?.remove();
            }
        }

        async function poll() {
            if (polling || document.hidden) return;
            polling = true;
            try {
                const url = MESSAGES_URL + (lastId ? '?after=' + encodeURIComponent(lastId) : '');
                const res = await fetch(url, {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (res.ok) addMessages((await res.json()).messages || []);
            } catch (e) { /* try again next tick */ }
            finally { polling = false; }
        }

        // Initial messages come straight from the server
        addMessages(@json($initial));

        // Reply form (AJAX, multipart so an image/video can go with it)
        const form = document.getElementById('replyForm');
        if (form) {
            const input = document.getElementById('messageInput');
            const btn = document.getElementById('sendBtn');
            const err = document.getElementById('replyError');
            const btnHtml = btn.innerHTML;

            const fileIn = document.getElementById('attachInput');
            const attachBtn = document.getElementById('attachBtn');
            const prev = document.getElementById('attachPreview');
            const thumb = document.getElementById('attachThumb');
            const icon = document.getElementById('attachIcon');
            const nameEl = document.getElementById('attachName');
            let file = null;
            let blobUrl = null;

            const showErr = (t) => { err.textContent = t; err.style.display = 'block'; };

            function clearFile() {
                if (blobUrl) URL.revokeObjectURL(blobUrl);
                blobUrl = null;
                file = null;
                fileIn.value = '';
                prev.style.display = 'none';
                thumb.style.display = 'none';
                icon.style.display = 'none';
            }

            attachBtn.addEventListener('click', () => fileIn.click());
            document.getElementById('attachRemove').addEventListener('click', clearFile);

            fileIn.addEventListener('change', () => {
                const f = fileIn.files[0];
                if (!f) return;
                err.style.display = 'none';
                const isImg = f.type.startsWith('image/');
                const isVid = f.type.startsWith('video/');
                if (!isImg && !isVid) { fileIn.value = ''; return showErr('Only images and videos can be sent.'); }
                if (f.size > (isImg ? MAX_IMG : MAX_VID)) {
                    fileIn.value = '';
                    return showErr(isImg ? 'Images can be up to 10 MB.' : 'Videos can be up to 50 MB.');
                }
                clearFile();
                file = f;
                nameEl.textContent = f.name;
                if (isImg) {
                    blobUrl = URL.createObjectURL(f);
                    thumb.src = blobUrl;
                    thumb.style.display = 'block';
                } else {
                    icon.style.display = 'inline-flex';
                }
                prev.style.display = 'flex';
            });

            async function send() {
                const text = input.value.trim();
                if ((!text && !file) || btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> ' + (file ? 'Uploading...' : 'Sending...');
                err.style.display = 'none';

                const fd = new FormData();
                if (text) fd.append('message', text);
                if (file) fd.append('attachment', file);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: fd,
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw Object.assign({}, data, { _status: res.status });
                    input.value = '';
                    clearFile();
                    addMessages([data.message], true, false);
                } catch (e) {
                    err.textContent = (e && e._status === 413 ? 'That file is too large.' : null)
                        || (e && e.errors ? Object.values(e.errors)[0][0] : e && e.message)
                        || 'Could not send. Try again.';
                    err.style.display = 'block';
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = btnHtml;
                    input.focus();
                }
            }

            form.addEventListener('submit', (e) => { e.preventDefault(); send(); });
            input.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); } });
        }

        setInterval(poll, 3000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
    })();
</script>

@include('admin.components.footer')