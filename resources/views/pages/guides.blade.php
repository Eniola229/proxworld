@extends('layouts.app')

@section('title', 'Setup guides — ProxWorld')
@section('description', 'Connect ProxWorld proxies to cURL, Python, Node.js, PHP, browsers and scraping tools. Copy-paste examples.')

@section('content')

<section class="page-hero">
    <div class="glow"></div>
    <div class="container">
        <div class="crumbs"><a href="{{ url('/') }}">Home</a> / Setup guides</div>
        <h1>Connect in a minute</h1>
        <p class="lede">After you order, your dashboard shows the host, port, username and password. Put those into any of the examples below.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Copy-paste examples</h2>
            <p>Replace the capital-letter values with the details from your dashboard.</p>
        </div>

        <div data-tabs>
            <div class="tabs" role="tablist">
                <button type="button" class="tab" role="tab" aria-selected="true">cURL</button>
                <button type="button" class="tab" role="tab" aria-selected="false">Python</button>
                <button type="button" class="tab" role="tab" aria-selected="false">Node.js</button>
                <button type="button" class="tab" role="tab" aria-selected="false">PHP</button>
            </div>

            <div class="tab-panel" role="tabpanel">
<pre class="code"><span class="c"># Check which IP the site sees</span>
curl -x "http://USERNAME:PASSWORD@PROXY_HOST:PORT" https://api.ipify.org

<span class="c"># SOCKS5</span>
curl --socks5-hostname "PROXY_HOST:PORT" --proxy-user "USERNAME:PASSWORD" https://api.ipify.org</pre>
            </div>

            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code"><span class="c"># pip install requests</span>
import requests

proxy = "http://USERNAME:PASSWORD@PROXY_HOST:PORT"
proxies = {"http": proxy, "https": proxy}

r = requests.get("https://api.ipify.org", proxies=proxies, timeout=30)
print(r.text)</pre>
            </div>

            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code"><span class="c">// npm install axios https-proxy-agent</span>
const axios = require('axios');
const { HttpsProxyAgent } = require('https-proxy-agent');

const agent = new HttpsProxyAgent('http://USERNAME:PASSWORD@PROXY_HOST:PORT');

axios.get('https://api.ipify.org', { httpsAgent: agent, proxy: false, timeout: 30000 })
  .then(res =&gt; console.log(res.data))
  .catch(err =&gt; console.error(err.message));</pre>
            </div>

            <div class="tab-panel" role="tabpanel" hidden>
<pre class="code">&lt;?php
$ch = curl_init('https://api.ipify.org');
curl_setopt_array($ch, [
    CURLOPT_PROXY          =&gt; 'PROXY_HOST:PORT',
    CURLOPT_PROXYUSERPWD   =&gt; 'USERNAME:PASSWORD',
    CURLOPT_PROXYTYPE      =&gt; CURLPROXY_HTTP,   <span class="c">// CURLPROXY_SOCKS5 for SOCKS5</span>
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_TIMEOUT        =&gt; 30,
]);
echo curl_exec($ch);
curl_close($ch);</pre>
            </div>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="container">
        <div class="section-head">
            <h2>Browsers and tools</h2>
            <p>If a tool has a proxy setting, it works with ProxWorld. Enter the same four values: host, port, username and password.</p>
        </div>
        <div class="tiles c3">
            <div class="tile">
                <div class="ico"><i class="fas fa-window-maximize" aria-hidden="true"></i></div>
                <h3>Browsers and extensions</h3>
                <p>Add a manual HTTP or SOCKS5 proxy in your browser or in a proxy-manager extension, then enter your credentials when asked.</p>
            </div>
            <div class="tile">
                <div class="ico"><i class="fas fa-robot" aria-hidden="true"></i></div>
                <h3>Automation frameworks</h3>
                <p>Puppeteer, Playwright and Selenium all accept a proxy server option at launch. Pass the host and port, then supply the username and password.</p>
            </div>
            <div class="tile">
                <div class="ico"><i class="fas fa-spider" aria-hidden="true"></i></div>
                <h3>Scraping libraries</h3>
                <p>Scrapy, Requests, Axios and similar libraries take a proxy URL in the format shown above.</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <h2>Want to automate ordering?</h2>
            <p>The API lets you buy and manage proxies from your own code.</p>
        </div>
        <div class="hero-cta">
            <a href="{{ route('api.docs') }}" class="btn btn-solid btn-large">Read the API docs</a>
            <a href="{{ route('faq') }}" class="btn btn-ghost btn-large">Read the FAQ</a>
        </div>
    </div>
</section>

@endsection
