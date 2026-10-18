<footer id="contact" class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <a href="{{ route('welcome') }}" class="logo-text">Prox<span>World</span></a>
            <p>Your trusted proxy provider worldwide. Safe, fast, and built for results.</p>
            <div class="footer-socials">
                <a href="https://whatsapp.com/channel/0029VbEIyPKFi8xhoHYXrk2i" target="_blank" rel="noopener noreferrer" class="soc-btn" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="https://t.me/proxworldhq" target="_blank" rel="noopener noreferrer" class="soc-btn" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                <a href="https://www.tiktok.com/@ProxWorld3928" target="_blank" rel="noopener noreferrer" class="soc-btn" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.x.com/prox_world" target="_blank" rel="noopener noreferrer" class="soc-btn" aria-label="X"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/proxworldhq" target="_blank" rel="noopener noreferrer" class="soc-btn" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Products</h4>
            <ul>
                @foreach (config('products') as $slug => $p)
                    <li><a href="{{ route('products.show', $slug) }}">{{ $p['name'] }}</a></li>
                @endforeach
                <li><a href="{{ route('pricing') }}">Pricing</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Navigate</h4>
            <ul>
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Sign Up</a></li>
                <li><a href="{{ route('use-cases') }}">Use Cases</a></li>
                <li><a href="{{ route('locations') }}">Locations</a></li>
                <li><a href="{{ route('guides') }}">Setup Guides</a></li>
                <li><a href="{{ route('api.docs') }}">API Docs</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li><a href="{{ route('reseller') }}">Reseller Program</a></li>
                <li><a href="{{ route('about') }}">About Us</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Legal</h4>
            <ul>
                <li><a href="{{ route('faq') }}">FAQ</a></li>
                <li><a href="{{ route('terms-of-use') }}">Terms of Use</a></li>
                <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('refund-policy') }}">Refund Policy</a></li>
                <li><a href="{{ route('acceptable-use-policy') }}">Acceptable Use Policy</a></li>
                <li><a href="{{ route('reseller-agreement') }}">Reseller Agreement</a></li>
                <li><a href="{{ route('cookie-policy') }}">Cookie Policy</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Contact</h4>

            <div class="contact-item">
                <i class="fas fa-headset" aria-hidden="true"></i>
                <a href="#" data-support-open>Support</a>
            </div>

            <div class="contact-item"><i class="fas fa-envelope" aria-hidden="true"></i> <a href="mailto:info@proxworld.shop">info@proxworld.shop</a></div>
            <div class="contact-item"><i class="fas fa-paper-plane" aria-hidden="true"></i> <a href="{{ route('contact') }}">Send a message</a></div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} ProxWorld. All rights reserved.</p>
        <p>ProxWorld is operated by AfricGEM International Company Limited</p>
    </div>
</footer>

{{-- Support pop-up: floating button + panel with two options (email, chat). --}}
<button type="button" class="spw-fab" id="spwFab" aria-label="Open support" aria-expanded="false" aria-controls="spwPanel">
    <i class="fas fa-comment-dots spw-ic-a" aria-hidden="true"></i>
    <i class="fas fa-chevron-down spw-ic-b" aria-hidden="true"></i>
</button>

<div class="spw-panel" id="spwPanel" role="dialog" aria-label="Support" aria-hidden="true">
    <button type="button" class="spw-x" id="spwClose" aria-label="Close">&times;</button>
    <div class="spw-hero">
        <h3>Hi there 👋<br>How can we help?</h3>
    </div>
    <div class="spw-body">
        <a href="mailto:info@proxworld.shop" class="spw-opt">
            <span class="spw-ico"><i class="fas fa-envelope" aria-hidden="true"></i></span>
            <span class="spw-lbl">Email us<small>info@proxworld.shop</small></span>
        </a>
        <a href="{{ auth()->check() ? route('support.index') : route('login') }}" class="spw-opt">
            <span class="spw-ico"><i class="fas fa-comments" aria-hidden="true"></i></span>
            <span class="spw-lbl">Chat with us<small>{{ auth()->check() ? 'Open live chat' : 'Log in to start a chat' }}</small></span>
        </a>
    </div>
</div>

<style>
    .spw-fab {
        position: fixed; right: 24px; bottom: 24px; z-index: 1000;
        width: 56px; height: 56px; border-radius: 50%; border: 0;
        display: flex; align-items: center; justify-content: center;
        background: var(--accent, #3454d1); color: #fff; font-size: 22px;
        box-shadow: 0 6px 18px rgba(0,0,0,.3); cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .spw-fab:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.35); }
    .spw-fab .spw-ic-b { display: none; }
    .spw-fab.is-open .spw-ic-a { display: none; }
    .spw-fab.is-open .spw-ic-b { display: inline; }

    .spw-panel {
        position: fixed; right: 24px; bottom: 92px; z-index: 1040;
        width: 340px; max-width: calc(100vw - 32px);
        background: #111318; color: #f1f3f5; border-radius: 18px; overflow: hidden;
        box-shadow: 0 16px 48px rgba(0,0,0,.4);
        opacity: 0; visibility: hidden; transform: translateY(16px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s;
    }
    .spw-panel.open { opacity: 1; visibility: visible; transform: none; }
    .spw-x {
        position: absolute; top: 12px; right: 12px; z-index: 2;
        width: 30px; height: 30px; border: 0; border-radius: 50%;
        background: rgba(255,255,255,.14); color: #fff; font-size: 20px; line-height: 1; cursor: pointer;
    }
    .spw-x:hover { background: rgba(255,255,255,.24); }
    .spw-hero {
        padding: 26px 22px 44px;
        background: linear-gradient(180deg, var(--accent, #3454d1) 0%, #111318 140%);
    }
    .spw-hero h3 { margin: 0; color: #fff; font-size: 24px; line-height: 1.25; font-weight: 700; }
    .spw-body { display: flex; flex-direction: column; gap: 10px; padding: 0 16px 16px; margin-top: -22px; }
    .spw-opt {
        display: flex; align-items: center; gap: 14px; padding: 14px;
        background: #1a1d23; border: 1px solid #2a2e36; border-radius: 12px;
        color: #f1f3f5; text-decoration: none; transition: border-color .2s, transform .2s;
    }
    .spw-opt:hover { border-color: var(--accent, #3454d1); transform: translateY(-1px); color: #fff; }
    .spw-ico {
        flex: 0 0 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: var(--accent, #3454d1); color: #fff; font-size: 18px;
    }
    .spw-lbl { font-weight: 600; font-size: 15px; line-height: 1.3; }
    .spw-lbl small { display: block; margin-top: 2px; font-weight: 400; font-size: 12.5px; color: #8b93a1; }

    @media (max-width: 575.98px) {
        .spw-fab { right: 16px; bottom: 16px; }
        .spw-panel { right: 16px; left: 16px; width: auto; bottom: 84px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .spw-panel, .spw-fab { transition: none; }
    }
</style>

<script>
    (function () {
        const fab = document.getElementById('spwFab');
        const panel = document.getElementById('spwPanel');
        const closeBtn = document.getElementById('spwClose');
        if (!fab || !panel) return;

        const setOpen = (open) => {
            panel.classList.toggle('open', open);
            fab.classList.toggle('is-open', open);
            fab.setAttribute('aria-expanded', open ? 'true' : 'false');
            panel.setAttribute('aria-hidden', open ? 'false' : 'true');
        };

        fab.addEventListener('click', (e) => { e.stopPropagation(); setOpen(!panel.classList.contains('open')); });
        closeBtn.addEventListener('click', () => setOpen(false));
        document.addEventListener('click', (e) => {
            if (panel.classList.contains('open') && !panel.contains(e.target) && !fab.contains(e.target)) setOpen(false);
        });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });

        // Any link/button with data-support-open (e.g. the footer "Support" link) opens the pop-up.
        document.querySelectorAll('[data-support-open]').forEach((el) =>
            el.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); setOpen(true); }));
        window.openSupportPopup = () => setOpen(true);
    })();
</script>