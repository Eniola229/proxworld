@extends('layouts.app')

@section('title', 'API documentation — ProxWorld')
@section('description', 'Buy and manage ProxWorld proxies from your own code. REST API reference with copy-paste examples in cURL, Python, Node.js and PHP, plus a live tester.')

@section('content')

<style>
    /* ---- API docs: scoped styles. Change --docs-accent to match your brand colour. ---- */
    .docs { --docs-accent: #6366f1; --docs-line: rgba(127,127,127,.22); --docs-soft: rgba(127,127,127,.08); }
    .docs-layout { display: grid; grid-template-columns: 230px minmax(0, 1fr); gap: 44px; align-items: start; }
    .docs-toc { position: sticky; top: 88px; max-height: calc(100vh - 110px); overflow-y: auto; padding-right: 8px; }
    .docs-toc .grp { font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; opacity: .55; margin: 18px 0 6px; }
    .docs-toc .grp:first-child { margin-top: 0; }
    .docs-toc a { display: block; padding: 6px 12px; border-left: 2px solid var(--docs-line); font-size: 14px; text-decoration: none; color: inherit; opacity: .75; }
    .docs-toc a:hover { opacity: 1; }
    .docs-toc a.active { opacity: 1; border-left-color: var(--docs-accent); color: var(--docs-accent); font-weight: 600; }

    .docs-block { margin-bottom: 56px; scroll-margin-top: 90px; }
    .docs-block > h2 { margin: 0 0 10px; }
    .docs-block h3 { font-size: 16px; margin: 26px 0 10px; }
    .docs-block p { margin: 0 0 14px; }
    .docs code:not(.x) { background: var(--docs-soft); border: 1px solid var(--docs-line); padding: 1px 6px; border-radius: 5px; font-size: .9em; }

    .docs-card { border: 1px solid var(--docs-line); border-radius: 14px; padding: 24px; margin-bottom: 20px; background: var(--docs-soft); }
    .docs-card > :last-child { margin-bottom: 0; }

    .docs-note { border-left: 3px solid var(--docs-accent); background: var(--docs-soft); padding: 12px 16px; border-radius: 0 10px 10px 0; margin: 0 0 18px; font-size: 14px; }
    .docs-note.warn { border-left-color: #f59e0b; }

    .docs-base { display: flex; flex-wrap: wrap; align-items: center; gap: 12px; border: 1px solid var(--docs-accent); border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; }
    .docs-base b { color: var(--docs-accent); font-size: 12px; text-transform: uppercase; letter-spacing: .6px; }
    .docs-base code { background: none !important; border: 0 !important; font-size: 15px !important; word-break: break-all; }

    .ep-line { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
    .ep-method { font-size: 12px; font-weight: 800; letter-spacing: .5px; padding: 4px 10px; border-radius: 6px; }
    .ep-method.get { background: rgba(59,130,246,.16); color: #3b82f6; }
    .ep-method.post { background: rgba(34,197,94,.16); color: #16a34a; }
    .ep-path { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 14px; word-break: break-all; }
    .ep-ability { margin-left: auto; font-size: 12px; opacity: .7; }

    .docs-table { width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 14px; }
    .docs-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .6px; opacity: .6; padding: 8px 12px; border-bottom: 1px solid var(--docs-line); }
    .docs-table td { padding: 10px 12px; border-bottom: 1px solid var(--docs-line); vertical-align: top; }
    .docs-table td:first-child { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 13px; white-space: nowrap; }
    .tag { font-size: 10px; font-weight: 800; letter-spacing: .4px; text-transform: uppercase; padding: 2px 7px; border-radius: 4px; }
    .tag.req { background: rgba(239,68,68,.15); color: #ef4444; }
    .tag.opt { background: rgba(34,197,94,.15); color: #16a34a; }

    .st { font-size: 12px; font-weight: 700; padding: 3px 9px; border-radius: 999px; font-family: inherit !important; }
    .st.pending { background: rgba(245,158,11,.18); color: #d97706; }
    .st.processing { background: rgba(59,130,246,.18); color: #2563eb; }
    .st.completed { background: rgba(34,197,94,.18); color: #16a34a; }
    .st.cancelled { background: rgba(239,68,68,.18); color: #dc2626; }
    .st.refunded { background: rgba(127,127,127,.2); }

    /* key bar + tester */
    .docs-key { position: sticky; top: 76px; z-index: 20; display: flex; flex-wrap: wrap; align-items: center; gap: 10px; border: 1px solid var(--docs-line); border-radius: 12px; padding: 12px 16px; margin-bottom: 36px; backdrop-filter: blur(10px); background: rgba(127,127,127,.12); }
    .docs-key label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; opacity: .7; }
    .docs-key input, .try-field input { flex: 1; min-width: 180px; width: 100%; background: transparent; color: inherit; border: 1px solid var(--docs-line); border-radius: 8px; padding: 9px 12px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 13px; }
    .docs-key input { width: auto; }
    .docs-key input:focus, .try-field input:focus { outline: none; border-color: var(--docs-accent); }
    .key-status { font-size: 12px; opacity: .6; }
    .key-status.saved { color: #16a34a; opacity: 1; }

    .try { margin-top: 22px; padding-top: 18px; border-top: 1px dashed var(--docs-line); }
    .try-btn { background: transparent; color: var(--docs-accent); border: 1px solid var(--docs-accent); border-radius: 8px; padding: 7px 14px; font-size: 12px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; cursor: pointer; }
    .try-btn:hover { background: var(--docs-accent); color: #fff; }
    .try-body { display: none; margin-top: 16px; }
    .try-body.open { display: block; }
    .try-field { margin-bottom: 12px; }
    .try-field label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; opacity: .65; margin-bottom: 5px; }
    .try-send { background: var(--docs-accent); color: #fff; border: 0; border-radius: 8px; padding: 10px 18px; font-size: 14px; font-weight: 700; cursor: pointer; }
    .try-send:disabled { opacity: .5; cursor: not-allowed; }
    .try-out { display: none; margin-top: 14px; border: 1px solid var(--docs-line); border-radius: 10px; padding: 14px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12.5px; white-space: pre-wrap; word-break: break-word; max-height: 360px; overflow: auto; }
    .try-hint { font-size: 12px; opacity: .65; margin: 10px 0 0; }

    @media (max-width: 960px) {
        .docs-layout { grid-template-columns: 1fr; }
        .docs-toc { display: none; }
        .docs-key { top: 64px; }
        .ep-ability { margin-left: 0; width: 100%; }
    }
</style>

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / API documentation</div>
        <h1>ProxWorld API</h1>
        <p class="lede">Buy and manage proxies from your own code, bot or panel. Simple REST, JSON in and out, one API key.</p>
        <div class="hero-cta">
            <a href="{{ route('api.index') }}" class="btn btn-solid btn-large">Get your API key</a>
            <a href="#create-order" class="btn btn-ghost btn-large">Jump to ordering</a>
        </div>
    </div>
</section>

<section class="section docs">
    <div class="container">
        <div class="docs-layout">

            <!-- Side navigation -->
            <nav class="docs-toc" aria-label="API sections">
                <div class="grp">Getting started</div>
                <a href="#introduction">Introduction</a>
                <a href="#authentication">Authentication</a>
                <a href="#errors">Errors</a>
                <div class="grp">Endpoints</div>
                <a href="#services">List services</a>
                <a href="#create-order">Create order</a>
                <a href="#get-order">Get order</a>
                <a href="#list-orders">List orders</a>
                <a href="#balance">Get balance</a>
                <div class="grp">Account</div>
                <a href="{{ route('api.index') }}">My API keys</a>
                <a href="{{ route('support.index') }}">Contact support</a>
            </nav>

            <div class="docs-main">

                <!-- INTRODUCTION -->
                <div class="docs-block" id="introduction">
                    <h2>Introduction</h2>
                    <p>Every request and response is JSON. Send <code>Accept: application/json</code>, and <code>Content-Type: application/json</code> on POST requests.</p>

                    <div class="docs-base"><b>Base URL</b><code>{{ url('/api/v1') }}</code></div>

                    <!-- Live key bar, used by every "Try it" panel -->
                    <div class="docs-key">
                        <label for="global-api-key">Your API key</label>
                        <input type="password" id="global-api-key" placeholder="pxw_your_api_key_here" autocomplete="off">
                        <button type="button" class="try-btn" id="key-visibility-btn" onclick="toggleKeyVisibility()">Show</button>
                        <span class="key-status" id="key-status">Not saved in this browser yet</span>
                    </div>
                    <p style="font-size:14px;opacity:.75;">Paste your key above and every <strong>Try it</strong> panel on this page will use it, so you can test each endpoint live against your own account. The key is stored only in this browser.</p>
                </div>

                <!-- AUTHENTICATION -->
                <div class="docs-block" id="authentication">
                    <h2>Authentication</h2>
                    <p>Send your API key as a Bearer token in the <code>Authorization</code> header of every request.</p>
<pre class="code">Authorization: Bearer pxw_your_api_key_here</pre>

                    <div class="docs-note warn" style="margin-top:18px;">Generate your key from your <a href="{{ route('api.index') }}">API page</a>. The full key is shown only once, right after you create it. Keep it secret, treat it like a password, and never commit it to version control.</div>

                    <div class="tiles c3" style="margin-top:22px;">
                        <div class="tile">
                            <div class="ico"><i class="fas fa-key" aria-hidden="true"></i></div>
                            <h3>Abilities</h3>
                            <p>Keys can be scoped when you create them. <code>orders.read</code> lists services and views your orders. <code>orders.create</code> places new orders. A key with no restrictions has full access.</p>
                        </div>
                        <div class="tile">
                            <div class="ico"><i class="fas fa-gauge-high" aria-hidden="true"></i></div>
                            <h3>Rate limit</h3>
                            <p>60 requests per minute, per API key. Go over it and you receive a <code>429</code> until the window resets.</p>
                        </div>
                        <div class="tile">
                            <div class="ico"><i class="fas fa-wallet" aria-hidden="true"></i></div>
                            <h3>Wallet billing</h3>
                            <p>Orders are paid from your wallet balance, debited the moment you place the order. Top up from your dashboard.</p>
                        </div>
                    </div>
                </div>

                <!-- ERRORS -->
                <div class="docs-block" id="errors">
                    <h2>Errors</h2>
                    <p>Failed requests return a JSON object with a <code>message</code> key describing what went wrong.</p>
<pre class="code">{
  "message": "Insufficient wallet balance."
}</pre>
                    <table class="docs-table">
                        <thead><tr><th>HTTP code</th><th>Meaning</th></tr></thead>
                        <tbody>
                            <tr><td>200 / 201</td><td>Success</td></tr>
                            <tr><td>401</td><td>Missing, invalid or inactive API key</td></tr>
                            <tr><td>402</td><td>Insufficient wallet balance</td></tr>
                            <tr><td>403</td><td>Your key doesn't have the ability this endpoint needs</td></tr>
                            <tr><td>404</td><td>Resource not found, or it belongs to a different account</td></tr>
                            <tr><td>422</td><td>Validation error (quantity out of range, invalid <code>service_id</code>)</td></tr>
                            <tr><td>429</td><td>Rate limit exceeded</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- LIST SERVICES -->
                <div class="docs-block" id="services">
                    <h2>List services</h2>
                    <div class="docs-card">
                        <div class="ep-line">
                            <span class="ep-method get">GET</span>
                            <span class="ep-path">{{ url('/api/v1/services') }}</span>
                            <span class="ep-ability">Ability: <code>orders.read</code></span>
                        </div>
                        <p>Returns every active proxy plan you can order, across all product types (residential, datacenter, ISP, mobile).</p>
                        <p>Service names always include the target country, formatted as <code>{Type} - {Country}</code>, for example <code>Residential Proxy - United States</code>. There is no standalone <code>country</code> field yet, so parse it from the end of the name if you need it separately.</p>

                        <div data-tabs>
                            <div class="tabs" role="tablist">
                                <button type="button" class="tab" role="tab" aria-selected="true">cURL</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Python</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Node.js</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">PHP</button>
                            </div>
                            <div class="tab-panel" role="tabpanel">
<pre class="code">curl "{{ url('/api/v1/services') }}" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code"><span class="c"># pip install requests</span>
import requests

r = requests.get(
    "{{ url('/api/v1/services') }}",
    headers={"Authorization": "Bearer YOUR_API_KEY", "Accept": "application/json"},
)
print(r.json())</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code"><span class="c">// Node 18+ (built-in fetch)</span>
const res = await fetch('{{ url('/api/v1/services') }}', {
  headers: { Authorization: 'Bearer YOUR_API_KEY', Accept: 'application/json' },
});
console.log(await res.json());</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">&lt;?php
$ch = curl_init('{{ url('/api/v1/services') }}');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     =&gt; ['Authorization: Bearer YOUR_API_KEY', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER =&gt; true,
]);
echo curl_exec($ch);
curl_close($ch);</pre>
                            </div>
                        </div>

                        <h3>Response</h3>
<pre class="code">{
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
}</pre>
                        <p style="margin-top:14px;"><code>raw_rate</code> and <code>raw_currency</code> are the provider's own cost. Your actual charge (with markup, in your currency) is calculated when you create an order.</p>

                        <div class="try">
                            <button type="button" class="try-btn" onclick="toggleTry('try-services')">Try it</button>
                            <div class="try-body" id="try-services">
                                <button type="button" class="try-send" onclick="sendTry(this, 'GET', '/services', null, 'out-services')">Send GET request</button>
                                <div class="try-out" id="out-services"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CREATE ORDER -->
                <div class="docs-block" id="create-order">
                    <h2>Create order</h2>
                    <div class="docs-card">
                        <div class="ep-line">
                            <span class="ep-method post">POST</span>
                            <span class="ep-path">{{ url('/api/v1/orders') }}</span>
                            <span class="ep-ability">Ability: <code>orders.create</code></span>
                        </div>
                        <p>Places a new order. Your wallet is debited immediately.</p>

                        <h3>Body parameters</h3>
                        <table class="docs-table">
                            <thead><tr><th>Parameter</th><th>Type</th><th></th><th>Description</th></tr></thead>
                            <tbody>
                                <tr><td>service_id</td><td>integer</td><td><span class="tag req">required</span></td><td>The <code>id</code> of a plan from the services list</td></tr>
                                <tr><td>quantity</td><td>integer</td><td><span class="tag req">required</span></td><td>Number of units (GB, IPs and so on), between 1 and 10,000</td></tr>
                            </tbody>
                        </table>

                        <div data-tabs>
                            <div class="tabs" role="tablist">
                                <button type="button" class="tab" role="tab" aria-selected="true">cURL</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Python</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Node.js</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">PHP</button>
                            </div>
                            <div class="tab-panel" role="tabpanel">
<pre class="code">curl -X POST "{{ url('/api/v1/orders') }}" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"service_id": 1, "quantity": 10}'</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code"><span class="c"># pip install requests</span>
import requests

r = requests.post(
    "{{ url('/api/v1/orders') }}",
    headers={"Authorization": "Bearer YOUR_API_KEY", "Accept": "application/json"},
    json={"service_id": 1, "quantity": 10},
)
print(r.status_code, r.json())</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">const res = await fetch('{{ url('/api/v1/orders') }}', {
  method: 'POST',
  headers: {
    Authorization: 'Bearer YOUR_API_KEY',
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ service_id: 1, quantity: 10 }),
});
console.log(res.status, await res.json());</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">&lt;?php
$ch = curl_init('{{ url('/api/v1/orders') }}');
curl_setopt_array($ch, [
    CURLOPT_POST           =&gt; true,
    CURLOPT_HTTPHEADER     =&gt; [
        'Authorization: Bearer YOUR_API_KEY',
        'Accept: application/json',
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS     =&gt; json_encode(['service_id' =&gt; 1, 'quantity' =&gt; 10]),
    CURLOPT_RETURNTRANSFER =&gt; true,
]);
echo curl_exec($ch);
curl_close($ch);</pre>
                            </div>
                        </div>

                        <h3>Response <span class="st completed">201 Created</span></h3>
<pre class="code">{
  "data": {
    "id": "a1b2c3d4-e5f6-...",
    "service_name": "Residential Proxy - United States",
    "product_type": "residential",
    "quantity": 10,
    "charge": 68.75,
    "currency": "USD",
    "status": "pending",
    "api_order_id": null,
    "proxy_access": null,
    "created_at": "2026-09-08T10:00:00.000000Z",
    "updated_at": "2026-09-08T10:00:00.000000Z"
  }
}</pre>
                        <p style="margin-top:14px;"><code>charge</code> is a JSON number, not a string. <code>api_order_id</code> is the identifier from the upstream provider and stays <code>null</code> until the order starts processing. <code>proxy_access</code> is <code>null</code> until credentials exist; see <a href="#get-order">Get order</a>.</p>

                        <div class="try">
                            <button type="button" class="try-btn" onclick="toggleTry('try-create-order')">Try it</button>
                            <div class="try-body" id="try-create-order">
                                <div class="try-field">
                                    <label for="create-order-service-id">service_id <span class="tag req">required</span></label>
                                    <input type="number" id="create-order-service-id" placeholder="1">
                                </div>
                                <div class="try-field">
                                    <label for="create-order-quantity">quantity <span class="tag req">required</span></label>
                                    <input type="number" id="create-order-quantity" placeholder="10">
                                </div>
                                <button type="button" class="try-send" onclick="sendCreateOrder(this)">Send POST request</button>
                                <p class="try-hint">This places a real order and debits your wallet. Only run it with a key and account you intend to charge.</p>
                                <div class="try-out" id="out-create-order"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GET ORDER -->
                <div class="docs-block" id="get-order">
                    <h2>Get order</h2>
                    <div class="docs-card">
                        <div class="ep-line">
                            <span class="ep-method get">GET</span>
                            <span class="ep-path">{{ url('/api/v1/orders/{order}') }}</span>
                            <span class="ep-ability">Ability: <code>orders.read</code></span>
                        </div>
                        <p>Fetch one of your orders by its <code>id</code>. Poll this endpoint after creating an order until the status is <code>completed</code>.</p>

                        <div data-tabs>
                            <div class="tabs" role="tablist">
                                <button type="button" class="tab" role="tab" aria-selected="true">cURL</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Python</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Node.js</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">PHP</button>
                            </div>
                            <div class="tab-panel" role="tabpanel">
<pre class="code">curl "{{ url('/api/v1/orders') }}/ORDER_ID" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">import requests

r = requests.get(
    "{{ url('/api/v1/orders') }}/ORDER_ID",
    headers={"Authorization": "Bearer YOUR_API_KEY", "Accept": "application/json"},
)
order = r.json()["data"]
if order["status"] == "completed":
    print(order["proxy_access"])</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">const res = await fetch('{{ url('/api/v1/orders') }}/ORDER_ID', {
  headers: { Authorization: 'Bearer YOUR_API_KEY', Accept: 'application/json' },
});
const { data } = await res.json();
if (data.status === 'completed') console.log(data.proxy_access);</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">&lt;?php
$ch = curl_init('{{ url('/api/v1/orders') }}/ORDER_ID');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     =&gt; ['Authorization: Bearer YOUR_API_KEY', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER =&gt; true,
]);
$order = json_decode(curl_exec($ch), true)['data'];
curl_close($ch);
if ($order['status'] === 'completed') { print_r($order['proxy_access']); }</pre>
                            </div>
                        </div>

                        <h3>Response</h3>
<pre class="code">{
  "data": {
    "id": "a1b2c3d4-e5f6-...",
    "service_name": "Residential Proxy - United States",
    "product_type": "residential",
    "quantity": 10,
    "charge": 68.75,
    "currency": "USD",
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
}</pre>
                        <p style="margin-top:14px;"><code>proxy_access</code> is only included once credentials exist. It is omitted entirely (not just <code>null</code>) on orders that haven't reached that point, so check <code>status === "completed"</code> before reading its keys.</p>

                        <h3>Order statuses</h3>
                        <table class="docs-table">
                            <thead><tr><th>Status</th><th>Meaning</th></tr></thead>
                            <tbody>
                                <tr><td><span class="st pending">pending</span></td><td>Order received, not yet provisioned</td></tr>
                                <tr><td><span class="st processing">processing</span></td><td>Being provisioned with the provider</td></tr>
                                <tr><td><span class="st completed">completed</span></td><td>Provisioned, credentials are ready</td></tr>
                                <tr><td><span class="st cancelled">cancelled</span></td><td>Order cancelled</td></tr>
                                <tr><td><span class="st refunded">refunded</span></td><td>Order failed and was refunded to your wallet</td></tr>
                            </tbody>
                        </table>

                        <div class="try">
                            <button type="button" class="try-btn" onclick="toggleTry('try-get-order')">Try it</button>
                            <div class="try-body" id="try-get-order">
                                <div class="try-field">
                                    <label for="get-order-id">order id</label>
                                    <input type="text" id="get-order-id" placeholder="a1b2c3d4-e5f6-...">
                                </div>
                                <button type="button" class="try-send" onclick="sendGetOrder(this)">Send GET request</button>
                                <div class="try-out" id="out-get-order"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LIST ORDERS -->
                <div class="docs-block" id="list-orders">
                    <h2>List orders</h2>
                    <div class="docs-card">
                        <div class="ep-line">
                            <span class="ep-method get">GET</span>
                            <span class="ep-path">{{ url('/api/v1/orders') }}</span>
                            <span class="ep-ability">Ability: <code>orders.read</code></span>
                        </div>
                        <p>Returns the orders you placed through the API (not orders placed from the dashboard), 20 per page. Add <code>?page=2</code> for more.</p>

                        <div data-tabs>
                            <div class="tabs" role="tablist">
                                <button type="button" class="tab" role="tab" aria-selected="true">cURL</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Python</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Node.js</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">PHP</button>
                            </div>
                            <div class="tab-panel" role="tabpanel">
<pre class="code">curl "{{ url('/api/v1/orders') }}?page=1" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">import requests

r = requests.get(
    "{{ url('/api/v1/orders') }}",
    params={"page": 1},
    headers={"Authorization": "Bearer YOUR_API_KEY", "Accept": "application/json"},
)
print(r.json()["meta"])</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">const res = await fetch('{{ url('/api/v1/orders') }}?page=1', {
  headers: { Authorization: 'Bearer YOUR_API_KEY', Accept: 'application/json' },
});
const json = await res.json();
console.log(json.meta);</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">&lt;?php
$ch = curl_init('{{ url('/api/v1/orders') }}?page=1');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     =&gt; ['Authorization: Bearer YOUR_API_KEY', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER =&gt; true,
]);
echo curl_exec($ch);
curl_close($ch);</pre>
                            </div>
                        </div>

                        <h3>Response</h3>
<pre class="code">{
  "data": [
    {
      "id": "a1b2c3d4-...",
      "service_name": "Residential Proxy - United States",
      "product_type": "residential",
      "quantity": 10,
      "charge": 68.75,
      "currency": "USD",
      "status": "completed",
      "api_order_id": "prov_9f8e7d6c",
      "created_at": "2026-09-08T10:00:00.000000Z",
      "updated_at": "2026-09-08T10:04:32.000000Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 1 }
}</pre>

                        <div class="try">
                            <button type="button" class="try-btn" onclick="toggleTry('try-list-orders')">Try it</button>
                            <div class="try-body" id="try-list-orders">
                                <div class="try-field">
                                    <label for="list-orders-page">page (optional)</label>
                                    <input type="number" id="list-orders-page" placeholder="1">
                                </div>
                                <button type="button" class="try-send" onclick="sendListOrders(this)">Send GET request</button>
                                <div class="try-out" id="out-list-orders"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BALANCE -->
                <div class="docs-block" id="balance">
                    <h2>Get balance</h2>
                    <div class="docs-card">
                        <div class="ep-line">
                            <span class="ep-method get">GET</span>
                            <span class="ep-path">{{ url('/api/v1/balance') }}</span>
                        </div>
                        <p>Returns your current wallet balance and currency. Check it before placing large orders to avoid a <code>402</code>.</p>

                        <div data-tabs>
                            <div class="tabs" role="tablist">
                                <button type="button" class="tab" role="tab" aria-selected="true">cURL</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Python</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">Node.js</button>
                                <button type="button" class="tab" role="tab" aria-selected="false">PHP</button>
                            </div>
                            <div class="tab-panel" role="tabpanel">
<pre class="code">curl "{{ url('/api/v1/balance') }}" \
  -H "Authorization: Bearer YOUR_API_KEY" \
  -H "Accept: application/json"</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">import requests

r = requests.get(
    "{{ url('/api/v1/balance') }}",
    headers={"Authorization": "Bearer YOUR_API_KEY", "Accept": "application/json"},
)
print(r.json())</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">const res = await fetch('{{ url('/api/v1/balance') }}', {
  headers: { Authorization: 'Bearer YOUR_API_KEY', Accept: 'application/json' },
});
console.log(await res.json());</pre>
                            </div>
                            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">&lt;?php
$ch = curl_init('{{ url('/api/v1/balance') }}');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER     =&gt; ['Authorization: Bearer YOUR_API_KEY', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER =&gt; true,
]);
echo curl_exec($ch);
curl_close($ch);</pre>
                            </div>
                        </div>

                        <h3>Response</h3>
<pre class="code">{
  "balance": 5000.00,
  "currency": "USD"
}</pre>

                        <div class="try">
                            <button type="button" class="try-btn" onclick="toggleTry('try-balance')">Try it</button>
                            <div class="try-body" id="try-balance">
                                <button type="button" class="try-send" onclick="sendTry(this, 'GET', '/balance', null, 'out-balance')">Send GET request</button>
                                <div class="try-out" id="out-balance"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="docs-block">
                    <div class="section-head" style="text-align:left;">
                        <h2>Need a hand?</h2>
                        <p>Stuck on an integration or need a higher rate limit? Our team can help.</p>
                    </div>
                    <div class="hero-cta" style="justify-content:flex-start;">
                        <a href="{{ route('support.index') }}" class="btn btn-solid btn-large">Contact support</a>
                        <a href="{{ route('faq') }}" class="btn btn-ghost btn-large">Read the FAQ</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    (function () {
        var API_BASE = "{{ url('/api/v1') }}";
        var KEY_STORAGE = 'pxw_docs_api_key';
        var keyInput = document.getElementById('global-api-key');
        var keyStatus = document.getElementById('key-status');

        function store(fn) { try { return fn(); } catch (e) { return null; } }

        var saved = store(function () { return localStorage.getItem(KEY_STORAGE); });
        if (saved) {
            keyInput.value = saved;
            keyStatus.textContent = 'Loaded from this browser';
            keyStatus.classList.add('saved');
        }

        keyInput.addEventListener('input', function () {
            var val = keyInput.value.trim();
            if (val) {
                store(function () { localStorage.setItem(KEY_STORAGE, val); });
                keyStatus.textContent = 'Saved in this browser';
                keyStatus.classList.add('saved');
            } else {
                store(function () { localStorage.removeItem(KEY_STORAGE); });
                keyStatus.textContent = 'Not saved in this browser yet';
                keyStatus.classList.remove('saved');
            }
        });

        window.toggleKeyVisibility = function () {
            var btn = document.getElementById('key-visibility-btn');
            var hidden = keyInput.type === 'password';
            keyInput.type = hidden ? 'text' : 'password';
            btn.textContent = hidden ? 'Hide' : 'Show';
        };

        window.toggleTry = function (id) {
            document.getElementById(id).classList.toggle('open');
        };

        window.sendTry = async function (btn, method, path, body, outId) {
            var key = keyInput.value.trim();
            var out = document.getElementById(outId);
            out.style.display = 'block';

            if (!key) {
                out.textContent = 'Enter your API key in the bar at the top of the page first.';
                return;
            }

            var label = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Sending…';
            out.textContent = 'Loading…';

            try {
                var opts = { method: method, headers: { 'Authorization': 'Bearer ' + key, 'Accept': 'application/json' } };
                if (body !== null && body !== undefined) {
                    opts.headers['Content-Type'] = 'application/json';
                    opts.body = JSON.stringify(body);
                }
                var res = await fetch(API_BASE + path, opts);
                var text = await res.text();
                var pretty;
                try { pretty = JSON.stringify(JSON.parse(text), null, 2); } catch (e) { pretty = text || '(empty response body)'; }
                out.textContent = 'Status: ' + res.status + ' ' + res.statusText + '\n\n' + pretty;
            } catch (err) {
                out.textContent = 'Request failed: ' + err.message + '\n\nThis is usually a CORS or network issue. Check your browser console for details.';
            } finally {
                btn.disabled = false;
                btn.textContent = label;
            }
        };

        window.sendCreateOrder = function (btn) {
            var serviceId = document.getElementById('create-order-service-id').value.trim();
            var quantity = document.getElementById('create-order-quantity').value.trim();
            var out = document.getElementById('out-create-order');

            if (!serviceId || !quantity) {
                out.style.display = 'block';
                out.textContent = 'Fill in both service_id and quantity first.';
                return;
            }
            window.sendTry(btn, 'POST', '/orders', { service_id: Number(serviceId), quantity: Number(quantity) }, 'out-create-order');
        };

        window.sendGetOrder = function (btn) {
            var id = document.getElementById('get-order-id').value.trim();
            var out = document.getElementById('out-get-order');

            if (!id) {
                out.style.display = 'block';
                out.textContent = 'Enter an order id first.';
                return;
            }
            window.sendTry(btn, 'GET', '/orders/' + encodeURIComponent(id), null, 'out-get-order');
        };

        window.sendListOrders = function (btn) {
            var page = document.getElementById('list-orders-page').value.trim();
            var path = page ? '/orders?page=' + encodeURIComponent(page) : '/orders';
            window.sendTry(btn, 'GET', path, null, 'out-list-orders');
        };

        // Highlight the current section in the side nav
        var links = Array.prototype.slice.call(document.querySelectorAll('.docs-toc a[href^="#"]'));
        var map = {};
        links.forEach(function (a) { map[a.getAttribute('href').slice(1)] = a; });

        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting && map[e.target.id]) {
                        links.forEach(function (l) { l.classList.remove('active'); });
                        map[e.target.id].classList.add('active');
                    }
                });
            }, { rootMargin: '-15% 0px -70% 0px' });

            Object.keys(map).forEach(function (id) {
                var el = document.getElementById(id);
                if (el) io.observe(el);
            });
        }
    })();
</script>

@endsection