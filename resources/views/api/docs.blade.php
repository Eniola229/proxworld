<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProxWorld API Documentation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0f1117; color: #e2e8f0; line-height: 1.7; }

        .sidebar { position: fixed; left: 0; top: 0; width: 260px; height: 100vh; background: #1a1d2e; border-right: 1px solid #2d3748; overflow-y: auto; padding: 24px 0; z-index: 100; }
        .sidebar-logo { padding: 0 24px 24px; border-bottom: 1px solid #2d3748; margin-bottom: 16px; }
        .sidebar-logo h2 { color: #fff; font-size: 18px; font-weight: 700; }
        .sidebar-logo span { color: #6366f1; }
        .sidebar-logo small { color: #718096; font-size: 11px; display: block; margin-top: 2px; }
        .nav-section { padding: 8px 24px 4px; color: #4a5568; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .nav-link { display: block; padding: 8px 24px; color: #a0aec0; text-decoration: none; font-size: 13px; transition: all 0.15s; }
        .nav-link:hover, .nav-link.active { color: #fff; background: #2d3748; border-right: 2px solid #6366f1; }

        .main { margin-left: 260px; padding: 48px; max-width: 900px; }

        h1 { font-size: 32px; font-weight: 800; color: #fff; margin-bottom: 12px; }
        h2 { font-size: 22px; font-weight: 700; color: #fff; margin: 48px 0 16px; padding-bottom: 8px; border-bottom: 1px solid #2d3748; }
        h3 { font-size: 16px; font-weight: 600; color: #e2e8f0; margin: 24px 0 10px; }
        p { color: #a0aec0; margin-bottom: 16px; font-size: 14px; }

        .badge { display: inline-block; padding: 2px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-right: 8px; }
        .badge-post { background: #1a3a2a; color: #48bb78; }
        .badge-get  { background: #1a2a4a; color: #63b3ed; }

        .endpoint-card { background: #1a1d2e; border: 1px solid #2d3748; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
        .endpoint-url  { background: #0f1117; border: 1px solid #2d3748; border-radius: 6px; padding: 12px 16px; font-family: monospace; font-size: 13px; color: #6366f1; margin-bottom: 16px; word-break: break-all; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 16px; }
        th { background: #2d3748; color: #e2e8f0; padding: 10px 14px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 10px 14px; border-bottom: 1px solid #2d3748; color: #a0aec0; vertical-align: top; }
        td:first-child { color: #f6ad55; font-family: monospace; font-size: 12px; }
        .required { color: #fc8181; font-size: 10px; font-weight: 700; background: #2d1515; padding: 1px 5px; border-radius: 3px; }
        .optional  { color: #68d391; font-size: 10px; font-weight: 700; background: #1a2d1a; padding: 1px 5px; border-radius: 3px; }

        pre { margin: 0; }
        code.hljs { border-radius: 8px; padding: 16px; font-size: 12px; }

        .alert-info { background: #1a2740; border: 1px solid #2b4c7e; border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #90cdf4; font-size: 13px; }
        .alert-warn { background: #2d2008; border: 1px solid #6b4c00; border-radius: 8px; padding: 16px; margin-bottom: 24px; color: #fbd38d; font-size: 13px; }

        .base-url-box { background: #1a1d2e; border: 1px solid #6366f1; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
        .base-url-box span { color: #6366f1; font-weight: 700; font-size: 12px; text-transform: uppercase; }
        .base-url-box code { color: #e2e8f0; font-size: 14px; font-family: monospace; }

        /* --- Try it panel --- */
        .key-bar { position: sticky; top: 0; z-index: 50; background: #14161f; border: 1px solid #2d3748; border-radius: 10px; padding: 14px 18px; margin-bottom: 32px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .key-bar label { color: #a0aec0; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        .key-bar input { flex: 1; min-width: 200px; background: #0f1117; border: 1px solid #2d3748; border-radius: 6px; padding: 8px 12px; color: #e2e8f0; font-family: monospace; font-size: 13px; }
        .key-bar input:focus { outline: none; border-color: #6366f1; }
        .key-status { font-size: 11px; color: #4a5568; }
        .key-status.saved { color: #48bb78; }

        .tryit { margin-top: 16px; border-top: 1px dashed #2d3748; padding-top: 16px; }
        .tryit-toggle { background: #232842; color: #a5b4fc; border: 1px solid #3730a3; border-radius: 6px; padding: 7px 14px; font-size: 12px; font-weight: 700; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px; }
        .tryit-toggle:hover { background: #2d3358; }
        .tryit-body { display: none; margin-top: 16px; }
        .tryit-body.open { display: block; }
        .tryit-field { margin-bottom: 12px; }
        .tryit-field label { display: block; font-size: 11px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; font-weight: 700; }
        .tryit-field input { width: 100%; background: #0f1117; border: 1px solid #2d3748; border-radius: 6px; padding: 9px 12px; color: #e2e8f0; font-family: monospace; font-size: 13px; }
        .tryit-field input:focus { outline: none; border-color: #6366f1; }
        .tryit-send { background: #6366f1; color: #fff; border: none; border-radius: 6px; padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .tryit-send:hover { background: #4f52d6; }
        .tryit-send:disabled { background: #3a3d52; cursor: not-allowed; }
        .tryit-output { display: none; margin-top: 14px; background: #0f1117; border: 1px solid #2d3748; border-radius: 6px; padding: 14px; font-family: monospace; font-size: 12px; color: #cbd5e0; white-space: pre-wrap; word-break: break-word; max-height: 340px; overflow-y: auto; }
        .tryit-status-line { font-size: 11px; color: #4a5568; margin-top: 8px; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 24px; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <h2>ProxWorld <span>API</span></h2>
        <small>v1 Documentation</small>
    </div>
    <div class="nav-section">Getting Started</div>
    <a href="#introduction" class="nav-link">Introduction</a>
    <a href="#authentication" class="nav-link">Authentication</a>
    <a href="#errors" class="nav-link">Error Handling</a>
    <div class="nav-section">Endpoints</div>
    <a href="#services" class="nav-link">List Services</a>
    <a href="#create-order" class="nav-link">Create Order</a>
    <a href="#get-order" class="nav-link">Get Order</a>
    <a href="#list-orders" class="nav-link">List Orders</a>
    <a href="#balance" class="nav-link">Get Balance</a>
    <div class="nav-section">Account</div>
    <a href="{{ route('api.index') }}" class="nav-link">← My API Keys</a>
</aside>

<main class="main">

    <h1 id="introduction">ProxWorld API</h1>
    <p>Buy and manage proxies programmatically — integrate ProxWorld directly into your application, bot, or panel with a simple REST API.</p>

    <div class="base-url-box">
        <span>Base URL</span>
        <code>{{ url('/api/v1') }}</code>
    </div>

    <div class="alert-info">
        <strong>📌 All requests and responses are JSON.</strong> Set <code>Accept: application/json</code> and, for POST requests, <code>Content-Type: application/json</code>.
    </div>

    <!-- LIVE API KEY BAR (used by every "Try it" panel below) -->
    <div class="key-bar">
        <label for="global-api-key">Your API Key (used to test the endpoints on this page)</label>
        <input type="password" id="global-api-key" placeholder="pxw_your_api_key_here" autocomplete="off">
        <button type="button" class="tryit-toggle" onclick="toggleKeyVisibility()" id="key-visibility-btn">Show</button>
        <span class="key-status" id="key-status">Not saved in this browser yet</span>
    </div>

    <!-- AUTHENTICATION -->
    <h2 id="authentication">Authentication</h2>
    <p>Every request requires your API key sent as a Bearer token in the <code>Authorization</code> header:</p>
    <pre><code class="language-bash">Authorization: Bearer pxw_your_api_key_here</code></pre>

    <div class="endpoint-card">
        <h3>Getting Your API Key</h3>
        <p>Generate your API key from your <a href="{{ route('api.index') }}" style="color: #6366f1;">account dashboard</a>. The full key is only ever shown once, right after you create it — keep it secret and treat it like a password.</p>
        <div class="alert-warn">⚠️ Never share your API key publicly or commit it to version control.</div>
        <p style="margin-bottom:0;">💡 Paste your key into the <strong>Your API Key</strong> bar above and it'll be used automatically by every "Try it" panel on this page, so you can test each endpoint live against your own account.</p>
    </div>

    <div class="endpoint-card">
        <h3>Abilities</h3>
        <p>Each key can be scoped to specific abilities when you create it. A key with no restrictions set has full access.</p>
        <table>
            <thead><tr><th>Ability</th><th>Grants</th></tr></thead>
            <tbody>
                <tr><td>orders.read</td><td>List services, view/list your own orders</td></tr>
                <tr><td>orders.create</td><td>Place new orders</td></tr>
            </tbody>
        </table>
    </div>

    <div class="endpoint-card">
        <h3>Rate Limits</h3>
        <p>60 requests per minute, per API key.</p>
    </div>

    <!-- ERROR HANDLING -->
    <h2 id="errors">Error Handling</h2>
    <p>Errors return a JSON object with a <code>message</code> key describing what went wrong.</p>
    <pre><code class="language-json">{
    "message": "Insufficient wallet balance."
}</code></pre>

    <table>
        <thead><tr><th>HTTP Code</th><th>Meaning</th></tr></thead>
        <tbody>
            <tr><td>200 / 201</td><td>Success</td></tr>
            <tr><td>401</td><td>Missing, invalid, or inactive API key</td></tr>
            <tr><td>402</td><td>Insufficient wallet balance</td></tr>
            <tr><td>403</td><td>Your API key doesn't have the required ability for this endpoint</td></tr>
            <tr><td>404</td><td>Resource not found (or belongs to a different account)</td></tr>
            <tr><td>422</td><td>Validation error (e.g. quantity out of range, invalid service_id)</td></tr>
            <tr><td>429</td><td>Rate limit exceeded</td></tr>
        </tbody>
    </table>

    <!-- SERVICES -->
    <h2 id="services">List Services</h2>
    <div class="endpoint-card">
        <span class="badge badge-get">GET</span><strong>Ability: orders.read</strong>
        <p style="margin-top: 12px;">Returns every active proxy plan you can order, across all product types (residential, datacenter, ISP, mobile).</p>
        <div class="endpoint-url">GET {{ url('/api/v1/services') }}</div>

        <p><strong>Service names always include the target country</strong>, formatted as <code>{Type} - {Country}</code> — e.g. <code>Residential Proxy - United States</code>, <code>Datacenter Proxy - Germany</code>. Parse the country from the tail of the name if you need it as a separate field; there's currently no standalone <code>country</code> key in this response.</p>

        <h3>Response</h3>
        <pre><code class="language-json">{
    "data": [
        {
            "id": 1,
            "name": "Residential Proxy - 60 Days - United States",
            "type": "residential",
            "unit": "GB",
            "raw_rate": "5.500000",
            "raw_currency": "USD"
        },
        {
            "id": 2,
            "name": "Datacenter Proxy - 30 Days - Germany",
            "type": "datacenter",
            "unit": "IP",
            "raw_rate": "1.200000",
            "raw_currency": "USD"
        }
    ]
}</code></pre>
        <p><code>raw_rate</code>/<code>raw_currency</code> are the provider's own cost — your actual charge (with markup, converted to your currency) is calculated when you create an order.</p>

        <div class="tryit">
            <button type="button" class="tryit-toggle" onclick="toggleTryIt('tryit-services')">Try it</button>
            <div class="tryit-body" id="tryit-services">
                <button type="button" class="tryit-send" onclick="sendTryIt(this, 'GET', '/services', null, 'output-services')">Send GET request</button>
                <div class="tryit-output" id="output-services"></div>
            </div>
        </div>
    </div>

    <!-- CREATE ORDER -->
    <h2 id="create-order">Create Order</h2>
    <div class="endpoint-card">
        <span class="badge badge-post">POST</span><strong>Ability: orders.create</strong>
        <p style="margin-top: 12px;">Places a new order. Your wallet is debited immediately at the time of purchase.</p>
        <div class="endpoint-url">POST {{ url('/api/v1/orders') }}</div>

        <h3>Body Parameters</h3>
        <table>
            <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>service_id</td><td>integer</td><td><span class="required">required</span></td><td>The <code>id</code> of a plan from the services list</td></tr>
                <tr><td>quantity</td><td>integer</td><td><span class="required">required</span></td><td>Number of units (e.g. GB, IPs) — between 1 and 10,000</td></tr>
            </tbody>
        </table>

        <h3>Response <span style="color:#48bb78;font-size:12px;">201 Created</span></h3>
        <pre><code class="language-json">{
    "data": {
        "id": "a1b2c3d4-e5f6-...",
        "service_name": "Residential Proxy - United States",
        "product_type": "residential",
        "quantity": 10,
        "charge": 68.75,
        "currency": "NGN",
        "status": "pending",
        "api_order_id": null,
        "proxy_access": null,
        "created_at": "2026-09-08T10:00:00.000000Z",
        "updated_at": "2026-09-08T10:00:00.000000Z"
    }
}</code></pre>
        <p><code>charge</code> is a JSON number, not a string. <code>api_order_id</code> is the identifier assigned by the upstream provider once the order starts processing — it's <code>null</code> until then. <code>proxy_access</code> is <code>null</code> until credentials exist; see the field notes under <a href="#get-order" style="color:#6366f1;">Get Order</a>.</p>

        <div class="tryit">
            <button type="button" class="tryit-toggle" onclick="toggleTryIt('tryit-create-order')">Try it</button>
            <div class="tryit-body" id="tryit-create-order">
                <div class="tryit-field">
                    <label for="create-order-service-id">service_id</label>
                    <input type="number" id="create-order-service-id" placeholder="1">
                </div>
                <div class="tryit-field">
                    <label for="create-order-quantity">quantity</label>
                    <input type="number" id="create-order-quantity" placeholder="10">
                </div>
                <button type="button" class="tryit-send" onclick="sendCreateOrder(this)">Send POST request</button>
                <p class="tryit-status-line">⚠️ This places a real order and debits your wallet — only run it with a key/account you intend to actually charge.</p>
                <div class="tryit-output" id="output-create-order"></div>
            </div>
        </div>
    </div>

    <!-- GET ORDER -->
    <h2 id="get-order">Get Order</h2>
    <div class="endpoint-card">
        <span class="badge badge-get">GET</span><strong>Ability: orders.read</strong>
        <div class="endpoint-url">GET {{ url('/api/v1/orders/{order}') }}</div>

        <h3>Response</h3>
        <pre><code class="language-json">{
    "data": {
        "id": "a1b2c3d4-e5f6-...",
        "service_name": "Residential Proxy - United States",
        "product_type": "residential",
        "quantity": 10,
        "charge": 68.75,
        "currency": "NGN",
        "status": "completed",
        "api_order_id": "prov_9f8e7d6c",
        "proxy_access": {
            "host": "gate.example.com",
            "port": 12345,
            "username": "user_ab12cd",
            "password": "••••••••"
        },
        "created_at": "2026-09-08T10:00:00.000000Z",
        "updated_at": "2026-09-08T10:04:32.000000Z"
    }
}</code></pre>
        <p><code>proxy_access</code> is only included once credentials actually exist for the order — it's omitted (not just <code>null</code>) on orders that haven't reached that point yet, so check for its presence with <code>status === "completed"</code> or before reading its keys.</p>

        <h3>Order Statuses</h3>
        <table>
            <thead><tr><th>Status</th><th>Meaning</th></tr></thead>
            <tbody>
                <tr><td>pending</td><td>Order received, not yet provisioned</td></tr>
                <tr><td>processing</td><td>Being provisioned with the provider</td></tr>
                <tr><td>completed</td><td>Provisioned — credentials are ready</td></tr>
                <tr><td>cancelled</td><td>Order cancelled</td></tr>
                <tr><td>refunded</td><td>Order failed and was refunded to your wallet</td></tr>
            </tbody>
        </table>

        <div class="tryit">
            <button type="button" class="tryit-toggle" onclick="toggleTryIt('tryit-get-order')">Try it</button>
            <div class="tryit-body" id="tryit-get-order">
                <div class="tryit-field">
                    <label for="get-order-id">order id</label>
                    <input type="text" id="get-order-id" placeholder="a1b2c3d4-e5f6-...">
                </div>
                <button type="button" class="tryit-send" onclick="sendGetOrder(this)">Send GET request</button>
                <div class="tryit-output" id="output-get-order"></div>
            </div>
        </div>
    </div>

    <!-- LIST ORDERS -->
    <h2 id="list-orders">List Orders</h2>
    <div class="endpoint-card">
        <span class="badge badge-get">GET</span><strong>Ability: orders.read</strong>
        <p style="margin-top: 12px;">Returns your orders placed through this API (not orders placed via the dashboard), paginated 20 per page.</p>
        <div class="endpoint-url">GET {{ url('/api/v1/orders') }}</div>

        <h3>Response</h3>
        <pre><code class="language-json">{
    "data": [
        {
            "id": "a1b2c3d4-...",
            "service_name": "Residential Proxy - United States",
            "product_type": "residential",
            "quantity": 10,
            "charge": 68.75,
            "currency": "NGN",
            "status": "completed",
            "api_order_id": "prov_9f8e7d6c",
            "created_at": "2026-09-08T10:00:00.000000Z",
            "updated_at": "2026-09-08T10:04:32.000000Z"
        }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 1 }
}</code></pre>

        <div class="tryit">
            <button type="button" class="tryit-toggle" onclick="toggleTryIt('tryit-list-orders')">Try it</button>
            <div class="tryit-body" id="tryit-list-orders">
                <div class="tryit-field">
                    <label for="list-orders-page">page (optional)</label>
                    <input type="number" id="list-orders-page" placeholder="1">
                </div>
                <button type="button" class="tryit-send" onclick="sendListOrders(this)">Send GET request</button>
                <div class="tryit-output" id="output-list-orders"></div>
            </div>
        </div>
    </div>

    <!-- BALANCE -->
    <h2 id="balance">Get Balance</h2>
    <div class="endpoint-card">
        <span class="badge badge-get">GET</span>
        <div class="endpoint-url">GET {{ url('/api/v1/balance') }}</div>

        <h3>Response</h3>
        <pre><code class="language-json">{
    "balance": 5000.00,
    "currency": "NGN"
}</code></pre>

        <div class="tryit">
            <button type="button" class="tryit-toggle" onclick="toggleTryIt('tryit-balance')">Try it</button>
            <div class="tryit-body" id="tryit-balance">
                <button type="button" class="tryit-send" onclick="sendTryIt(this, 'GET', '/balance', null, 'output-balance')">Send GET request</button>
                <div class="tryit-output" id="output-balance"></div>
            </div>
        </div>
    </div>

    <p style="margin-top: 48px; padding-top: 24px; border-top: 1px solid #2d3748; color: #4a5568; font-size: 12px; text-align: center;">
        ProxWorld API v1 · <a href="{{ route('support.index') }}" style="color: #6366f1;">Contact Support</a>
    </p>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>

<script>
    const PXW_API_BASE = "{{ url('/api/v1') }}";
    const PXW_KEY_STORAGE = 'pxw_docs_api_key';

    // --- API key bar: persist across visits in this browser only ---
    const keyInput = document.getElementById('global-api-key');
    const keyStatus = document.getElementById('key-status');

    window.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem(PXW_KEY_STORAGE);
        if (saved) {
            keyInput.value = saved;
            keyStatus.textContent = 'Loaded from this browser';
            keyStatus.classList.add('saved');
        }
    });

    keyInput.addEventListener('input', () => {
        const val = keyInput.value.trim();
        if (val) {
            localStorage.setItem(PXW_KEY_STORAGE, val);
            keyStatus.textContent = 'Saved in this browser';
            keyStatus.classList.add('saved');
        } else {
            localStorage.removeItem(PXW_KEY_STORAGE);
            keyStatus.textContent = 'Not saved in this browser yet';
            keyStatus.classList.remove('saved');
        }
    });

    function toggleKeyVisibility() {
        const btn = document.getElementById('key-visibility-btn');
        if (keyInput.type === 'password') {
            keyInput.type = 'text';
            btn.textContent = 'Hide';
        } else {
            keyInput.type = 'password';
            btn.textContent = 'Show';
        }
    }

    function toggleTryIt(id) {
        document.getElementById(id).classList.toggle('open');
    }

    function getApiKey() {
        return keyInput.value.trim();
    }

    async function sendTryIt(btn, method, path, body, outputId) {
        const key = getApiKey();
        const out = document.getElementById(outputId);
        out.style.display = 'block';

        if (!key) {
            out.textContent = 'Enter your API key in the bar at the top of the page first.';
            return;
        }

        const origLabel = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Sending…';
        out.textContent = 'Loading…';

        try {
            const opts = {
                method,
                headers: {
                    'Authorization': 'Bearer ' + key,
                    'Accept': 'application/json',
                },
            };
            if (body !== null && body !== undefined) {
                opts.headers['Content-Type'] = 'application/json';
                opts.body = JSON.stringify(body);
            }

            const res = await fetch(PXW_API_BASE + path, opts);
            const text = await res.text();
            let pretty;
            try {
                pretty = JSON.stringify(JSON.parse(text), null, 2);
            } catch (e) {
                pretty = text || '(empty response body)';
            }
            out.textContent = `Status: ${res.status} ${res.statusText}\n\n${pretty}`;
        } catch (err) {
            out.textContent = 'Request failed: ' + err.message + '\n\nThis is usually a CORS or network issue — check your browser console for details.';
        } finally {
            btn.disabled = false;
            btn.textContent = origLabel;
        }
    }

    function sendCreateOrder(btn) {
        const serviceId = document.getElementById('create-order-service-id').value.trim();
        const quantity = document.getElementById('create-order-quantity').value.trim();
        const out = document.getElementById('output-create-order');

        if (!serviceId || !quantity) {
            out.style.display = 'block';
            out.textContent = 'Fill in both service_id and quantity first.';
            return;
        }

        sendTryIt(btn, 'POST', '/orders', {
            service_id: Number(serviceId),
            quantity: Number(quantity),
        }, 'output-create-order');
    }

    function sendGetOrder(btn) {
        const orderId = document.getElementById('get-order-id').value.trim();
        const out = document.getElementById('output-get-order');

        if (!orderId) {
            out.style.display = 'block';
            out.textContent = 'Enter an order id first.';
            return;
        }

        sendTryIt(btn, 'GET', '/orders/' + encodeURIComponent(orderId), null, 'output-get-order');
    }

    function sendListOrders(btn) {
        const page = document.getElementById('list-orders-page').value.trim();
        const path = page ? `/orders?page=${encodeURIComponent(page)}` : '/orders';
        sendTryIt(btn, 'GET', path, null, 'output-list-orders');
    }
</script>

</body>
</html>