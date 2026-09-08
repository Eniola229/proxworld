<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProxWorld API Tester</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:      #0a0c14;
            --bg2:     #0f1117;
            --bg3:     #161922;
            --border:  rgba(255,255,255,0.07);
            --accent:  #6366f1;
            --green:   #10b981;
            --red:     #ef4444;
            --yellow:  #f59e0b;
            --text:    #e2e8f0;
            --muted:   #64748b;
            --soft:    #94a3b8;
        }

        body {
            font-family: 'Syne', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
            grid-template-rows: 56px 1fr;
        }

        /* ── TOPBAR ── */
        .topbar {
            grid-column: 1 / -1;
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }
        .topbar-logo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.4rem;
            letter-spacing: 3px;
            color: #fff;
        }
        .topbar-logo span { color: var(--accent); }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .topbar-badge {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
            color: #a5b4fc;
        }
        .status-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 8px rgba(16,185,129,0.6);
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ── SIDEBAR ── */
        .sidebar {
            background: var(--bg2);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 16px 0;
        }
        .sidebar-section {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 20px 6px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            cursor: pointer;
            transition: all 0.15s;
            border-left: 2px solid transparent;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--soft);
        }
        .nav-item:hover { background: rgba(255,255,255,0.03); color: var(--text); }
        .nav-item.active { background: rgba(99,102,241,0.08); color: #fff; border-left-color: var(--accent); }
        .nav-item .method-tag {
            font-family: 'DM Mono', monospace;
            font-size: 0.55rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 6px;
            border-radius: 3px;
            background: rgba(99,102,241,0.2);
            color: #a5b4fc;
            flex-shrink: 0;
        }

        /* ── MAIN ── */
        .main {
            display: grid;
            grid-template-rows: 1fr;
            overflow: hidden;
        }
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* ── REQUEST PANEL ── */
        .request-panel {
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .panel-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 1.5rem;
            letter-spacing: 2px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .panel-title .badge-method {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 1.5px;
            padding: 4px 10px;
            border-radius: 4px;
            background: rgba(99,102,241,0.2);
            border: 1px solid rgba(99,102,241,0.3);
            color: #a5b4fc;
        }
        .panel-desc {
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.6;
            padding: 12px 14px;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 6px;
            border-left: 3px solid var(--accent);
        }

        .field-group { display: flex; flex-direction: column; gap: 6px; }
        .field-label {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--soft);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .req-badge {
            font-size: 0.5rem;
            padding: 1px 5px;
            border-radius: 3px;
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .opt-badge {
            font-size: 0.5rem;
            padding: 1px 5px;
            border-radius: 3px;
            background: rgba(16,185,129,0.12);
            border: 1px solid rgba(16,185,129,0.25);
            color: #6ee7b7;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input, select, textarea {
            font-family: 'DM Mono', monospace;
            font-size: 0.8rem;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            padding: 10px 14px;
            width: 100%;
            outline: none;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: rgba(99,102,241,0.5);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }
        input::placeholder { color: var(--muted); }
        select option { background: var(--bg3); }

        .url-bar {
            display: flex;
            align-items: center;
            gap: 0;
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }
        .url-method {
            font-family: 'DM Mono', monospace;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 10px 14px;
            background: rgba(99,102,241,0.15);
            color: #a5b4fc;
            border-right: 1px solid var(--border);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .url-input {
            border: none;
            border-radius: 0;
            background: transparent;
            flex: 1;
            font-size: 0.75rem;
        }
        .url-input:focus { box-shadow: none; }

        .send-btn {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 11px 28px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
        }
        .send-btn:hover { background: #5558e3; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.35); }
        .send-btn:active { transform: translateY(0); }
        .send-btn.loading { opacity: 0.7; cursor: not-allowed; }

        .spinner {
            width: 14px; height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── RESPONSE PANEL ── */
        .response-panel {
            overflow-y: auto;
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .response-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .response-title {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--muted);
        }
        .response-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-chip {
            font-family: 'DM Mono', monospace;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .status-chip.ok    { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #6ee7b7; }
        .status-chip.err   { background: rgba(239,68,68,0.15);  border: 1px solid rgba(239,68,68,0.3);  color: #fca5a5; }
        .status-chip.idle  { background: rgba(100,116,139,0.15); border: 1px solid rgba(100,116,139,0.3); color: var(--soft); }
        .time-chip {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            color: var(--muted);
        }

        .response-box {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            flex: 1;
            min-height: 300px;
        }
        .response-box-header {
            padding: 8px 14px;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .response-box-header span {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
        }
        .copy-btn {
            font-family: 'DM Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 1px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 4px;
            color: var(--soft);
            padding: 3px 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .copy-btn:hover { border-color: var(--accent); color: #a5b4fc; }

        #response-output {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            line-height: 1.8;
            padding: 16px;
            white-space: pre-wrap;
            word-break: break-all;
            min-height: 280px;
            color: var(--soft);
        }

        /* JSON syntax highlight */
        .json-key     { color: #a5b4fc; }
        .json-string  { color: #6ee7b7; }
        .json-number  { color: #fcd34d; }
        .json-boolean { color: #f9a8d4; }
        .json-null    { color: #94a3b8; }

        /* Empty state */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 60px 20px;
            color: var(--muted);
            text-align: center;
        }
        .empty-state i { font-size: 2rem; opacity: 0.3; }
        .empty-state p { font-size: 0.8rem; line-height: 1.6; max-width: 200px; }

        /* History -->
        .history-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .history-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 0.75rem;
        }
        .history-item:hover { border-color: rgba(99,102,241,0.3); background: rgba(99,102,241,0.05); }
        .history-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.15); }

        /* Responsive */
        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; grid-template-rows: 56px auto 1fr; }
            .sidebar { border-right: none; border-bottom: 1px solid var(--border); overflow-x: auto; white-space: nowrap; display: flex; padding: 8px; flex-wrap: nowrap; }
            .sidebar-section { display: none; }
            .nav-item { display: inline-flex; border-left: none; border-bottom: 2px solid transparent; padding: 8px 14px; }
            .nav-item.active { border-bottom-color: var(--accent); border-left-color: transparent; }
            .content { grid-template-columns: 1fr; }
            .request-panel { border-right: none; border-bottom: 1px solid var(--border); }
        } 
    </style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
    <div class="topbar-logo">Prox<span>World</span> <span style="font-family:'DM Mono',monospace;font-size:0.7rem;color:var(--muted);letter-spacing:2px;">API TESTER</span></div>
    <div class="topbar-right">
        <div class="status-dot"></div>
        <span style="font-family:'DM Mono',monospace;font-size:0.65rem;color:var(--muted);letter-spacing:1px;">LIVE</span>
        <div class="topbar-badge">v2.0</div>
         <a href="/api/docs" class="btn btn-solid" style="display:inline-flex;align-items:center;gap:0.6rem; color: white;">
           READ DOCS&nbsp;<i class="fas fa-key"></i>
        </a>

    </div>
</header>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-section">Endpoints</div>

    <div class="nav-item active" onclick="loadAction('services', this)">
        <span class="method-tag">POST</span> Services List
    </div>
    <div class="nav-item" onclick="loadAction('balance', this)">
        <span class="method-tag">POST</span> Get Balance
    </div>
    <div class="nav-item" onclick="loadAction('add', this)">
        <span class="method-tag">POST</span> Add Order
    </div>
    <div class="nav-item" onclick="loadAction('status', this)">
        <span class="method-tag">POST</span> Order Status
    </div>
    <div class="nav-item" onclick="loadAction('multi_status', this)">
        <span class="method-tag">POST</span> Multiple Status
    </div>
    <div class="nav-item" onclick="loadAction('refill', this)">
        <span class="method-tag">POST</span> Create Refill
    </div>
    <div class="nav-item" onclick="loadAction('refill_status', this)">
        <span class="method-tag">POST</span> Refill Status
    </div>
    <div class="nav-item" onclick="loadAction('cancel', this)">
        <span class="method-tag">POST</span> Cancel Orders
    </div>

    <div class="sidebar-section" style="margin-top:16px;">History</div>
    <div id="history-nav" style="padding: 0 10px;"></div>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="content">

        <!-- REQUEST -->
        <div class="request-panel">

            <div class="field-group">
                <div class="panel-title" id="action-title">
                    <span class="badge-method">POST</span>
                    Services List
                </div>
                <p class="panel-desc" id="action-desc">
                    Returns all available services. No API key required for this endpoint.
                </p>
            </div>

            <!-- URL Bar -->
            <div class="field-group">
                <label class="field-label">Endpoint</label>
                <div class="url-bar">
                    <div class="url-method">POST</div>
                    <input class="url-input" type="text" id="api-url" value="/api/v2" readonly>
                </div>
            </div>

            <!-- API Key -->
            <div class="field-group" id="field-key">
                <label class="field-label">
                    API Key <span class="opt-badge">optional for services</span>
                </label>
                <input type="text" id="input-key" placeholder="bst_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
            </div>

            <!-- Dynamic Fields -->
            <div id="dynamic-fields"></div>

            <!-- Send -->
            <button class="send-btn" id="send-btn" onclick="sendRequest()">
                <div class="spinner" id="spinner"></div>
                <span id="send-text">Send Request</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" id="send-icon"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>

        </div>

        <!-- RESPONSE -->
        <div class="response-panel">

            <div class="response-header">
                <div class="response-title">Response</div>
                <div class="response-meta">
                    <div class="status-chip idle" id="status-chip">— Waiting</div>
                    <div class="time-chip" id="time-chip"></div>
                </div>
            </div>

            <div class="response-box">
                <div class="response-box-header">
                    <span>JSON Output</span>
                    <button class="copy-btn" onclick="copyResponse()">Copy</button>
                </div>
                <div id="response-output">
                    <div class="empty-state">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                        <p>Hit <strong>Send Request</strong> to see the response here.</p>
                    </div>
                </div>
            </div>

            <!-- Request preview -->
            <div>
                <div style="font-family:'DM Mono',monospace;font-size:0.6rem;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Request Payload</div>
                <div class="response-box">
                    <div id="request-preview" style="font-family:'DM Mono',monospace;font-size:0.72rem;line-height:1.8;padding:14px;color:var(--muted);min-height:60px;">
                        { "action": "services" }
                    </div>
                </div>
            </div>

        </div>

    </div>
</main>

<script>
    // ── CONFIG ──
    // Change this to your actual API URL when testing live
    const BASE_URL = window.location.origin + '/api/v2';

    // ── ACTION DEFINITIONS ──
    const actions = {
        services: {
            title: 'Services List',
            desc: 'Returns all available services. No API key required for this endpoint.',
            fields: [],
            keyRequired: false,
        },
        balance: {
            title: 'Get Balance',
            desc: 'Returns your current wallet balance in NGN.',
            fields: [],
            keyRequired: true,
        },
        add: {
            title: 'Add Order',
            desc: 'Place a new order. Balance is deducted immediately and refunded automatically if the order fails.',
            fields: [
                { id: 'service', label: 'Service ID', placeholder: 'e.g. 1', type: 'number', required: true },
                { id: 'link',    label: 'Link',       placeholder: 'https://instagram.com/yourprofile', type: 'url', required: true },
                { id: 'quantity',label: 'Quantity',   placeholder: 'e.g. 1000', type: 'number', required: true },
            ],
            keyRequired: true,
        },
        status: {
            title: 'Order Status',
            desc: 'Check the live status of a single order by its ID.',
            fields: [
                { id: 'order', label: 'Order ID', placeholder: 'a1b2c3d4-e5f6-...', type: 'text', required: true },
            ],
            keyRequired: true,
        },
        multi_status: {
            title: 'Multiple Order Status',
            desc: 'Check status of multiple orders in one request. Pass comma-separated order IDs.',
            action: 'status',
            fields: [
                { id: 'orders', label: 'Order IDs (comma-separated)', placeholder: 'id1,id2,id3', type: 'text', required: true },
            ],
            keyRequired: true,
        },
        refill: {
            title: 'Create Refill',
            desc: 'Request a refill for a completed order that dropped below the delivered amount.',
            fields: [
                { id: 'order', label: 'Order ID', placeholder: 'a1b2c3d4-e5f6-...', type: 'text', required: true },
            ],
            keyRequired: true,
        },
        refill_status: {
            title: 'Refill Status',
            desc: 'Check the status of a previously created refill.',
            fields: [
                { id: 'refill', label: 'Refill ID', placeholder: 'e.g. 123456', type: 'text', required: true },
            ],
            keyRequired: true,
        },
        cancel: {
            title: 'Cancel Orders',
            desc: 'Cancel one or more pending or processing orders.',
            fields: [
                { id: 'orders', label: 'Order IDs (comma-separated)', placeholder: 'id1,id2,id3', type: 'text', required: true },
            ],
            keyRequired: true,
        },
    };

    let currentAction = 'services';
    let requestHistory = [];

    // ── LOAD ACTION ──
    function loadAction(key, el) {
        currentAction = key;

        // Sidebar active state
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        if (el) el.classList.add('active');

        const cfg = actions[key];
        document.getElementById('action-title').innerHTML = `<span class="badge-method">POST</span> ${cfg.title}`;
        document.getElementById('action-desc').textContent = cfg.desc;

        // Key field label
        const keyField = document.getElementById('field-key');
        keyField.querySelector('.field-label').innerHTML = `API Key <span class="${cfg.keyRequired ? 'req-badge' : 'opt-badge'}">${cfg.keyRequired ? 'required' : 'optional'}</span>`;

        // Dynamic fields
        const container = document.getElementById('dynamic-fields');
        container.innerHTML = '';
        cfg.fields.forEach(f => {
            const wrap = document.createElement('div');
            wrap.className = 'field-group';
            wrap.innerHTML = `
                <label class="field-label">${f.label} <span class="${f.required ? 'req-badge' : 'opt-badge'}">${f.required ? 'required' : 'optional'}</span></label>
                <input type="${f.type}" id="input-${f.id}" placeholder="${f.placeholder}">
            `;
            container.appendChild(wrap);
        });

        updatePreview();
        resetResponse();
    }

    // ── UPDATE PREVIEW ──
    function updatePreview() {
        const cfg = actions[currentAction];
        const payload = { action: cfg.action || currentAction };
        const key = document.getElementById('input-key').value.trim();
        if (key) payload.key = key;
        cfg.fields.forEach(f => {
            const val = document.getElementById('input-' + f.id)?.value.trim();
            if (val) payload[f.id] = val;
        });
        document.getElementById('request-preview').textContent = JSON.stringify(payload, null, 2);
    }

    document.addEventListener('input', updatePreview);

    // ── SEND REQUEST ──
async function sendRequest() {
    const cfg = actions[currentAction];
    const key = document.getElementById('input-key').value.trim();

    if (cfg.keyRequired && !key) {
        showError('API key is required for this endpoint.');
        return;
    }

    // Build payload
    const payload = new FormData();
    payload.append('action', cfg.action || currentAction);
    if (key) payload.append('key', key);
    cfg.fields.forEach(f => {
        const val = document.getElementById('input-' + f.id)?.value.trim();
        if (val) payload.append(f.id, val);
    });

    // Loading state
    setLoading(true);
    const startTime = Date.now();

    try {
        const res = await fetch(BASE_URL, { method: 'POST', body: payload });
        const elapsed = Date.now() - startTime;
        
        // Check if response is JSON
        const contentType = res.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            const json = await res.json();
            showResponse(json, res.status, elapsed);
            addHistory(cfg.title, res.status, elapsed);
        } else {
            // If not JSON, show the text response
            const text = await res.text();
            console.error('Non-JSON response:', text);
            showError(`Server returned HTML instead of JSON. Status: ${res.status}. Check if API endpoint is correct.`);
            addHistory(cfg.title, res.status, elapsed);
        }
    } catch (err) {
        const elapsed = Date.now() - startTime;
        showError(err.message || 'Network error — check the API URL and CORS settings.');
        addHistory(cfg.title, 0, elapsed);
    } finally {
        setLoading(false);
    }
}
    // ── SHOW RESPONSE ──
    function showResponse(data, status, ms) {
        const chip = document.getElementById('status-chip');
        chip.textContent = `${status} ${statusText(status)}`;
        chip.className = 'status-chip ' + (status >= 200 && status < 300 ? 'ok' : 'err');
        document.getElementById('time-chip').textContent = `${ms}ms`;

        const el = document.getElementById('response-output');
        el.innerHTML = syntaxHighlight(JSON.stringify(data, null, 2));
    }

    function showError(msg) {
        const chip = document.getElementById('status-chip');
        chip.textContent = 'Error';
        chip.className = 'status-chip err';
        document.getElementById('time-chip').textContent = '';
        document.getElementById('response-output').innerHTML = `<span style="color:#fca5a5;">${escHtml(msg)}</span>`;
    }

    function resetResponse() {
        document.getElementById('status-chip').textContent = '— Waiting';
        document.getElementById('status-chip').className = 'status-chip idle';
        document.getElementById('time-chip').textContent = '';
        document.getElementById('response-output').innerHTML = `
            <div class="empty-state">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                <p>Hit <strong>Send Request</strong> to see the response here.</p>
            </div>`;
    }

    // ── LOADING ──
    function setLoading(on) {
        const btn = document.getElementById('send-btn');
        document.getElementById('spinner').style.display = on ? 'block' : 'none';
        document.getElementById('send-icon').style.display = on ? 'none' : 'block';
        document.getElementById('send-text').textContent = on ? 'Sending...' : 'Send Request';
        btn.classList.toggle('loading', on);
        btn.disabled = on;
    }

    // ── HISTORY ──
    function addHistory(title, status, ms) {
        requestHistory.unshift({ title, status, ms, time: new Date().toLocaleTimeString() });
        if (requestHistory.length > 8) requestHistory.pop();
        renderHistory();
    }

    function renderHistory() {
        const nav = document.getElementById('history-nav');
        nav.innerHTML = requestHistory.map((h, i) => `
            <div class="history-item">
                <div class="history-dot" style="background:${h.status >= 200 && h.status < 300 ? 'var(--green)' : h.status === 0 ? 'var(--muted)' : 'var(--red)'};"></div>
                <span style="flex:1;font-family:'DM Mono',monospace;font-size:0.65rem;color:var(--soft);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${h.title}</span>
                <span style="font-family:'DM Mono',monospace;font-size:0.58rem;color:var(--muted);">${h.ms}ms</span>
            </div>
        `).join('');
    }

    // ── COPY ──
    function copyResponse() {
        const text = document.getElementById('response-output').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.querySelector('.copy-btn');
            btn.textContent = 'Copied!';
            setTimeout(() => btn.textContent = 'Copy', 1500);
        });
    }

    // ── JSON HIGHLIGHT ──
    function syntaxHighlight(json) {
        json = escHtml(json);
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, match => {
            if (/^"/.test(match)) {
                if (/:$/.test(match)) return `<span class="json-key">${match}</span>`;
                return `<span class="json-string">${match}</span>`;
            }
            if (/true|false/.test(match)) return `<span class="json-boolean">${match}</span>`;
            if (/null/.test(match))       return `<span class="json-null">${match}</span>`;
            return `<span class="json-number">${match}</span>`;
        });
    }

    function escHtml(str) {
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function statusText(s) {
        const map = { 200:'OK', 400:'Bad Request', 401:'Unauthorized', 402:'Payment Required', 404:'Not Found', 422:'Unprocessable', 500:'Server Error' };
        return map[s] || '';
    }

    // ── INIT ──
    loadAction('services', document.querySelector('.nav-item'));
</script>
</body>
</html>