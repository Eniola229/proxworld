<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                    <span>Copyright ©</span>
                    <script>document.write(new Date().getFullYear());</script>
                    <span class="ms-1">ProxWorld. All rights reserved.</span>
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end gap-3 mt-3 mt-md-0">
                    <a href="{{ route('faq') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-help-circle me-1" style="font-size: 12px;"></i>
                        FAQ
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('terms-of-use') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-file-text me-1" style="font-size: 12px;"></i>
                        Terms
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('refund-policy') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-rotate-ccw me-1" style="font-size: 12px;"></i>
                        Refund Policy
                    </a>
                    @auth
                    <span class="text-muted">|</span>
                    <a href="{{ route('support.index') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-headphones me-1" style="font-size: 12px;"></i>
                        Support
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- ===== LIVE CHAT (JS component + floating widget). Everything lives in this one file. ===== --}}
@verbatim
<script>
/*
 * SupportChat — one component used by the floating widget AND the /support page.
 * Tabs: Home · Messages · FAQ (link). Views: login → home → messages list → chat.
 * "Send us a message" opens an empty chat straight away; the first message creates the
 * ticket (subject is generated server-side from it). Photos/videos upload via the server
 * to Cloudinary. No page reloads.
 */
(function () {
  if (window.SupportChat) return;

  const MAX_IMG = 10 * 1024 * 1024;
  const MAX_VID = 50 * 1024 * 1024;

  const css = `
.sc{--bg:#111318;--card:#1a1d23;--card2:#242830;--line:#2a2e36;--tx:#f1f3f5;--mut:#8b93a1;--teal:var(--bs-primary,#3454d1);--tealD:color-mix(in srgb,var(--teal) 55%,#000);--tealM:color-mix(in srgb,var(--teal) 30%,#000);
  position:relative;display:flex;flex-direction:column;height:100%;background:var(--bg);color:var(--tx);font-size:14px;line-height:1.45}
.sc *{box-sizing:border-box}
.sc button,.sc textarea{font-family:inherit}
.sc [hidden]{display:none!important}

.sc-x{position:absolute;top:14px;right:14px;z-index:6;width:32px;height:32px;border:0;border-radius:50%;background:rgba(255,255,255,.12);color:#fff;font-size:20px;line-height:1;cursor:pointer}
.sc-x:hover{background:rgba(255,255,255,.2)}

.sc-head{display:flex;align-items:center;gap:10px;padding:16px 56px 14px 14px;border-bottom:1px solid var(--line);flex-shrink:0}
.sc-head.center{padding:16px 56px}
.sc-head.center .sc-title{text-align:center}
.sc-back{display:flex;align-items:center;justify-content:center;width:30px;height:30px;background:none;border:0;color:var(--tx);font-size:20px;cursor:pointer;border-radius:50%;padding:0}
.sc-back:hover{background:rgba(255,255,255,.08)}
.sc-title{flex:1;min-width:0;font-weight:600;font-size:16px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sc-live{font-size:11px;color:var(--mut)}
.sc-live::before{content:'';display:inline-block;width:7px;height:7px;border-radius:50%;background:#4ade80;margin-right:5px}

.sc-main{position:relative;flex:1;min-height:0;display:flex;flex-direction:column}

.sc-home{flex:1;overflow-y:auto}
.sc-hero{padding:24px 22px 64px;background:linear-gradient(180deg,var(--tealD) 0%,var(--tealM) 55%,var(--bg) 100%)}
.sc-avs{display:flex;margin-bottom:60px}
.sc-av{width:42px;height:42px;border-radius:50%;border:2px solid var(--tealD);display:flex;align-items:center;justify-content:center;background:var(--tealM);color:#fff;font-size:18px;margin-right:-8px}
.sc-hero h2{margin:0;font-size:28px;line-height:1.2;font-weight:700;color:#fff}
.sc-cards{padding:0 16px 16px;margin-top:-26px;display:flex;flex-direction:column;gap:10px}
.sc-card{display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%;padding:16px;border-radius:12px;background:var(--card);border:1px solid var(--line);color:var(--tx);text-decoration:none;font-weight:600;font-size:14px;cursor:pointer;text-align:left}
.sc-card:hover{border-color:var(--teal);color:var(--tx)}
.sc-card i{color:var(--teal);font-size:18px}
.sc-card small{display:block;font-weight:400;color:var(--mut);margin-top:2px;font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:250px}

.sc-list{flex:1;overflow-y:auto;padding:0 16px 84px}
.sc-item{display:flex;gap:12px;align-items:center;width:100%;text-align:left;background:none;border:0;border-bottom:1px solid var(--line);padding:14px 4px;cursor:pointer;color:inherit}
.sc-item:hover{background:rgba(255,255,255,.03)}
.sc-ava{flex:0 0 40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--teal),var(--tealD));display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px}
.sc-it{flex:1;min-width:0}
.sc-it-top{display:flex;justify-content:space-between;gap:8px}
.sc-it-subj{font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sc-it-time{color:var(--mut);font-size:12px;flex-shrink:0}
.sc-it-prev{color:var(--mut);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px}
.sc-empty,.sc-loading{text-align:center;color:var(--mut);padding:36px 16px}
.sc-empty a{color:var(--teal)}

.sc-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--teal);color:#fff;border:0;border-radius:10px;padding:12px 18px;font-weight:600;font-size:14px;cursor:pointer;text-decoration:none}
.sc-btn:hover{filter:brightness(1.08);color:#fff}
.sc-btn:disabled{opacity:.5;cursor:default}
.sc-btn.ghost{background:var(--card2);color:var(--tx)}
.sc-float{position:absolute;left:50%;bottom:18px;transform:translateX(-50%);box-shadow:0 6px 20px rgba(0,0,0,.45);white-space:nowrap}

.sc-msgs{flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:12px}
.sc-hello{margin:auto;text-align:center;color:var(--mut);padding:24px 18px}
.sc-hello .sc-ava{margin:0 auto 12px;width:52px;height:52px;font-size:24px}
.sc-hello strong{display:block;color:var(--tx);font-size:16px;margin-bottom:4px}
.sc-m{display:flex;flex-direction:column;max-width:85%;gap:4px}
.sc-m.out{align-self:flex-end;align-items:flex-end}
.sc-m.in{align-self:flex-start;align-items:flex-start}
.sc-bubble{padding:9px 13px;border-radius:14px;white-space:pre-wrap;word-break:break-word}
.sc-m.out .sc-bubble{background:var(--teal);color:#fff;border-bottom-right-radius:4px}
.sc-m.in .sc-bubble{background:var(--card2);color:var(--tx);border-bottom-left-radius:4px}
.sc-media{display:block;max-width:100%;max-height:260px;border-radius:12px;background:#000}
.sc-meta{font-size:11px;color:var(--mut)}

.sc-composer{border-top:1px solid var(--line);padding:10px;flex-shrink:0;max-height:65%;overflow-y:auto;background:var(--bg)}
.sc-row{display:flex;gap:8px;align-items:flex-end}
.sc-row textarea{flex:1;resize:none;border:1px solid var(--line);border-radius:12px;padding:9px 12px;max-height:110px;background:var(--card);color:var(--tx);font-size:14px;outline:none}
.sc-row textarea:focus{border-color:var(--teal)}
.sc-row textarea::placeholder{color:var(--mut)}
.sc-ib{flex:0 0 38px;height:38px;border-radius:50%;border:0;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:18px;padding:0}
.sc-attach{background:var(--card2);color:var(--mut)}
.sc-attach:hover{color:var(--tx)}
.sc-send{background:var(--teal);color:#fff}
.sc-send:disabled{opacity:.5;cursor:default}
.sc-prev{display:flex;align-items:center;gap:10px;background:var(--card);border:1px solid var(--line);border-radius:10px;padding:6px 8px;margin-bottom:8px}
.sc-prev img,.sc-prev .sc-vid{flex:0 0 40px;width:40px;height:40px;border-radius:6px;object-fit:cover;background:var(--card2);display:flex;align-items:center;justify-content:center;color:var(--teal)}
.sc-prev span{flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px}
.sc-prev button{background:none;border:0;color:var(--mut);font-size:20px;line-height:1;cursor:pointer}
.sc-err{color:#f87171;font-size:12px;margin-bottom:6px}

.sc-closed{text-align:center;color:var(--mut);padding:6px}
.sc-rate{padding:6px 4px 2px}
.sc-rate-q{font-weight:600;margin-bottom:6px;text-align:center}
.sc-stars{font-size:30px;line-height:1;cursor:pointer;user-select:none;text-align:center;margin-bottom:8px;color:var(--mut)}
.sc-stars span{padding:0 3px}
.sc-rate textarea{width:100%;resize:none;border:1px solid var(--line);border-radius:10px;padding:8px 12px;background:var(--card);color:var(--tx);margin-bottom:8px;outline:none}
.sc-rate .sc-btn{width:100%}
.sc-rated{text-align:center;padding:4px 0 2px;font-weight:600}
.sc-stars-done{display:block;font-size:22px;color:#f59e0b;letter-spacing:2px;font-weight:400}

.sc-login{margin:auto;text-align:center;padding:32px 24px}
.sc-login i{font-size:42px;color:var(--teal)}
.sc-login h3{font-size:17px;margin:14px 0 6px}
.sc-login p{color:var(--mut);margin-bottom:20px}
.sc-login .sc-btn{width:100%;margin-bottom:8px}

.sc-tabs{display:flex;border-top:1px solid var(--line);background:var(--bg);flex-shrink:0}
.sc-tab{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;padding:10px 0 12px;background:none;border:0;color:var(--mut);text-decoration:none;cursor:pointer;font-size:13px}
.sc-tab i{font-size:22px}
.sc-tab:hover{color:var(--tx)}
.sc-tab.on{color:var(--teal)}
`;
  const styleEl = document.createElement('style');
  styleEl.textContent = css;
  document.head.appendChild(styleEl);

  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const errText = (e, fallback) => {
    if (e && e.status === 413) return 'That file is too large.';
    return (e && e.data && (e.data.errors ? Object.values(e.data.errors)[0][0] : e.data.message)) || fallback;
  };
  // Changes whenever the closed/rating state changes, so the composer area only re-renders when it needs to.
  const sigOf = (t) => (t && t.is_closed ? 1 : 0) + ':' + ((t && t.rating) || 0) + ':' + (t && t.can_rate ? 1 : 0);

  class SupportChat {
    /**
     * cfg: { authed, csrf, loginUrl, registerUrl, userName?, faqUrl?, openTicket?, onClose?,
     *        urls: { index, store, messages, reply, rate } }  (messages/reply/rate contain "__ID__")
     */
    constructor(root, cfg) {
      this.root = root;
      this.cfg = cfg;
      this.view = null;
      this.ticket = null;
      this.lastId = 0;
      this.seen = new Set();
      this.timer = null;
      this.paused = false;
      this.polling = false;
      this.closed = false;
      this.stick = true;
      this.sig = '';

      root.innerHTML = `
        <div class="sc">
          ${cfg.onClose ? '<button type="button" class="sc-x" aria-label="Close">&times;</button>' : ''}
          <div class="sc-head" hidden>
            <button type="button" class="sc-back" aria-label="Back"><i class="feather-arrow-left"></i></button>
            <div class="sc-title">Support</div>
            <span class="sc-live" hidden>Live</span>
          </div>
          <div class="sc-main"></div>
          <nav class="sc-tabs" hidden>
            <button type="button" class="sc-tab" data-tab="home"><i class="feather-home"></i>Home</button>
            <button type="button" class="sc-tab" data-tab="messages"><i class="feather-message-square"></i>Messages</button>
            <a class="sc-tab" href="${esc(cfg.faqUrl || '/faq')}"><i class="feather-help-circle"></i>FAQ</a>
          </nav>
        </div>`;
      this.head = root.querySelector('.sc-head');
      this.main = root.querySelector('.sc-main');
      this.tabs = root.querySelector('.sc-tabs');
      this.titleEl = root.querySelector('.sc-title');
      this.backBtn = root.querySelector('.sc-back');
      this.liveEl = root.querySelector('.sc-live');

      this.backBtn.addEventListener('click', () => (this.ticket ? this.showList() : this.showHome()));
      this.tabs.querySelector('[data-tab=home]').addEventListener('click', () => this.showHome());
      this.tabs.querySelector('[data-tab=messages]').addEventListener('click', () => this.showList());
      const x = root.querySelector('.sc-x');
      if (x) x.addEventListener('click', () => cfg.onClose());
      document.addEventListener('visibilitychange', () => (document.hidden ? this.pause() : this.resume()));
    }

    init() {
      if (!this.cfg.authed) return this.showLogin();
      if (this.cfg.openTicket) return this.showChat({ id: this.cfg.openTicket, subject: 'Ticket' });
      return this.showHome();
    }

    pause() { this.paused = true; }
    resume() { this.paused = false; if (this.view === 'chat') this.poll(); }

    /** title === null hides the header bar; tab === null hides the bottom tabs. */
    chrome({ title = null, back = false, live = false, center = false, tab = null } = {}) {
      this.head.hidden = title === null;
      this.head.classList.toggle('center', center);
      this.titleEl.textContent = title || '';
      this.backBtn.hidden = !back;
      this.liveEl.hidden = !live;
      this.tabs.hidden = !tab;
      this.tabs.querySelectorAll('[data-tab]').forEach((b) => b.classList.toggle('on', b.dataset.tab === tab));
    }

    async api(url, opts = {}) {
      const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': this.cfg.csrf,
      };
      // FormData sets its own multipart boundary; only JSON strings need a content type.
      if (typeof opts.body === 'string') headers['Content-Type'] = 'application/json';

      const res = await fetch(url, { credentials: 'same-origin', ...opts, headers });
      let data = {};
      try { data = await res.json(); } catch (e) { /* empty body */ }
      if (res.status === 401 || res.status === 419) {
        this.showLogin(true);
        throw { handled: true };
      }
      if (!res.ok) throw { status: res.status, data };
      return data;
    }

    /* ---------- views ---------- */

    showLogin(expired) {
      this.stopPolling();
      this.view = 'login';
      this.chrome({ title: 'Support' });
      this.main.innerHTML = `
        <div class="sc-login">
          <i class="feather-lock"></i>
          <h3>${expired ? 'Your session has expired' : 'Log in to chat with support'}</h3>
          <p>You need an account to message our team.</p>
          <a href="${esc(this.cfg.loginUrl)}" class="sc-btn">Log in</a>
          ${this.cfg.registerUrl ? `<a href="${esc(this.cfg.registerUrl)}" class="sc-btn ghost">Create an account</a>` : ''}
        </div>`;
    }

    showHome() {
      this.stopPolling();
      this.view = 'home';
      this.ticket = null;
      this.chrome({ tab: 'home' });
      const name = (this.cfg.userName || '').trim();
      this.main.innerHTML = `
        <div class="sc-home">
          <div class="sc-hero">
            <div class="sc-avs">
              <span class="sc-av"><i class="feather-user"></i></span>
              <span class="sc-av"><i class="feather-headphones"></i></span>
              <span class="sc-av"><i class="feather-smile"></i></span>
            </div>
            <h2>Hi ${name ? esc(name) : 'there'} 👋<br>How can we help?</h2>
          </div>
          <div class="sc-cards">
            <button type="button" class="sc-card sc-go-new"><span>Send us a message</span><i class="feather-send"></i></button>
            <div class="sc-recent"></div>
            <a class="sc-card" href="${esc(this.cfg.faqUrl || '/faq')}"><span>FAQ</span><i class="feather-external-link"></i></a>
          </div>
        </div>`;
      this.main.querySelector('.sc-go-new').onclick = () => this.showNewChat();
      this.loadRecent();
    }

    /** Shows the latest conversation on Home so people can jump back in. */
    async loadRecent() {
      const box = this.main.querySelector('.sc-recent');
      if (!box || !this.cfg.authed) return;
      try {
        const { tickets } = await this.api(this.cfg.urls.index);
        if (this.view !== 'home' || !tickets.length) return;
        const t = tickets[0];
        box.innerHTML = `
          <button type="button" class="sc-card" style="width:100%">
            <span>${esc(t.subject)}<small>${esc(t.preview || 'Open conversation')}</small></span>
            <i class="feather-chevron-right"></i>
          </button>`;
        box.firstElementChild.onclick = () => this.showChat(t);
      } catch (e) { /* the recent card is optional */ }
    }

    async showList() {
      this.stopPolling();
      this.view = 'list';
      this.ticket = null;
      this.chrome({ title: 'Messages', center: true, tab: 'messages' });
      this.main.innerHTML = `
        <div class="sc-list"><div class="sc-loading">Loading your conversations…</div></div>
        <button type="button" class="sc-btn sc-float sc-new">Send us a message <i class="feather-send"></i></button>`;
      this.main.querySelector('.sc-new').onclick = () => this.showNewChat();
      const list = this.main.querySelector('.sc-list');
      try {
        const { tickets } = await this.api(this.cfg.urls.index);
        if (this.view !== 'list') return;
        if (!tickets.length) {
          list.innerHTML = '<div class="sc-empty">No conversations yet. Send us a message and our team will reply here.</div>';
          return;
        }
        list.innerHTML = tickets.map((t, i) => `
          <button type="button" class="sc-item" data-i="${i}">
            <span class="sc-ava"><i class="feather-headphones"></i></span>
            <span class="sc-it">
              <span class="sc-it-top"><span class="sc-it-subj">${esc(t.subject)}</span><span class="sc-it-time">${esc(t.updated_short || '')}</span></span>
              <span class="sc-it-prev">${esc(t.is_closed ? (t.rating ? 'Closed · Thanks for your feedback' : 'Rate your conversation') : (t.preview || ''))}</span>
            </span>
          </button>`).join('');
        list.querySelectorAll('.sc-item').forEach((el) => { el.onclick = () => this.showChat(tickets[+el.dataset.i]); });
      } catch (e) {
        if (e.handled) return;
        list.innerHTML = '<div class="sc-empty">Could not load conversations. <a href="#" class="sc-retry">Try again</a></div>';
        list.querySelector('.sc-retry').onclick = (ev) => { ev.preventDefault(); this.showList(); };
      }
    }

    /** Empty chat — nothing is created until the first message is sent. */
    showNewChat() {
      this.stopPolling();
      this.view = 'chat';
      this.ticket = null;
      this.lastId = 0;
      this.seen = new Set();
      this.closed = false;
      this.sig = '';
      this.chrome({ title: 'New conversation', back: true });
      this.main.innerHTML = `
        <div class="sc-msgs">
          <div class="sc-hello">
            <div class="sc-ava"><i class="feather-headphones"></i></div>
            <strong>Hi${this.cfg.userName ? ' ' + esc(this.cfg.userName) : ''} 👋</strong>
            Tell us what you need. You can attach photos or videos too.
          </div>
        </div>
        <div class="sc-composer"></div>`;
      this.bindMsgs();
      this.renderComposer();
      const ta = this.main.querySelector('.sc-composer textarea');
      if (ta) ta.focus();
    }

    async showChat(ticket) {
      this.stopPolling();
      this.view = 'chat';
      this.ticket = ticket;
      this.lastId = 0;
      this.seen = new Set();
      this.closed = !!ticket.is_closed;
      this.sig = sigOf(ticket);
      this.chrome({ title: ticket.subject || 'Ticket', back: true, live: true });
      this.main.innerHTML = '<div class="sc-msgs"><div class="sc-loading">Loading messages…</div></div><div class="sc-composer"></div>';
      this.bindMsgs();
      this.renderComposer();
      await this.poll();
      if (this.view === 'chat' && this.ticket && this.ticket.id === ticket.id) {
        this.timer = setInterval(() => this.poll(), 3000);
      }
    }

    /* ---------- chat internals ---------- */

    stopPolling() { clearInterval(this.timer); this.timer = null; }

    bindMsgs() {
      this.msgsEl = this.main.querySelector('.sc-msgs');
      this.stick = true;
      this.msgsEl.addEventListener('scroll', () => {
        this.stick = this.msgsEl.scrollHeight - this.msgsEl.scrollTop - this.msgsEl.clientHeight < 80;
      });
      // Images/videos change height after they load; keep the view pinned to the bottom if the user was there.
      this.msgsEl.addEventListener('load', () => { if (this.stick) this.msgsEl.scrollTop = this.msgsEl.scrollHeight; }, true);
      this.msgsEl.addEventListener('loadedmetadata', () => { if (this.stick) this.msgsEl.scrollTop = this.msgsEl.scrollHeight; }, true);
    }

    async poll() {
      if (this.view !== 'chat' || !this.ticket || this.paused || this.polling) return;
      this.polling = true;
      const id = this.ticket.id;
      try {
        const url = this.cfg.urls.messages.replace('__ID__', id) + (this.lastId ? `?after=${encodeURIComponent(this.lastId)}` : '');
        const d = await this.api(url);
        if (this.view !== 'chat' || !this.ticket || this.ticket.id !== id) return;
        if (d.ticket) {
          this.ticket = d.ticket;
          this.titleEl.textContent = d.ticket.subject;
          const sig = sigOf(d.ticket);
          if (sig !== this.sig) {
            this.sig = sig;
            this.closed = !!d.ticket.is_closed;
            this.renderComposer();
          }
        }
        this.addMessages(d.messages || []);
      } catch (e) {
        if (!e.handled) this.msgsEl.querySelector('.sc-loading')?.remove();
      } finally {
        this.polling = false;
      }
    }

    mediaHtml(a) {
      if (!a || !a.url) return '';
      if (a.type === 'video') {
        return `<video class="sc-media" src="${esc(a.url)}" controls preload="metadata" playsinline></video>`;
      }
      return `<a href="${esc(a.url)}" target="_blank" rel="noopener"><img class="sc-media" src="${esc(a.url)}" alt="${esc(a.name || 'Image')}" loading="lazy"></a>`;
    }

    addMessages(list, force = false, track = true) {
      const box = this.msgsEl;
      const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
      const first = !this.lastId;
      let added = false;
      for (const m of list) {
        if (this.seen.has(m.id)) continue;
        this.seen.add(m.id);
        if (track) this.lastId = m.id;
        box.querySelector('.sc-loading')?.remove();
        box.querySelector('.sc-hello')?.remove();
        box.insertAdjacentHTML('beforeend', `
          <div class="sc-m ${m.is_admin ? 'in' : 'out'}">
            ${this.mediaHtml(m.attachment)}
            ${m.message ? `<div class="sc-bubble">${esc(m.message)}</div>` : ''}
            <div class="sc-meta">${m.is_admin ? `<strong>${esc(m.name)}</strong> · ` : ''}${esc(m.time)}</div>
          </div>`);
        added = true;
      }
      if (added && (nearBottom || first || force)) box.scrollTop = box.scrollHeight;
    }

    renderComposer() {
      const c = this.main.querySelector('.sc-composer');
      if (!c) return;
      if (this.closed) { this.renderClosed(c); return; }

      c.innerHTML = `
        <div class="sc-err" hidden></div>
        <div class="sc-prev" hidden></div>
        <div class="sc-row">
          <button type="button" class="sc-ib sc-attach" aria-label="Attach photo or video"><i class="feather-paperclip"></i></button>
          <input type="file" accept="image/*,video/*" hidden>
          <textarea rows="1" maxlength="5000" placeholder="Type your message…"></textarea>
          <button type="button" class="sc-ib sc-send" aria-label="Send"><i class="feather-send"></i></button>
        </div>`;
      const ta = c.querySelector('textarea');
      const btn = c.querySelector('.sc-send');
      const err = c.querySelector('.sc-err');
      const prev = c.querySelector('.sc-prev');
      const fileIn = c.querySelector('input[type=file]');
      let file = null;
      let blobUrl = null;
      let busy = false;

      const showErr = (t) => { err.textContent = t; err.hidden = false; };

      const clearFile = () => {
        if (blobUrl) URL.revokeObjectURL(blobUrl);
        blobUrl = null;
        file = null;
        fileIn.value = '';
        prev.hidden = true;
        prev.innerHTML = '';
      };

      c.querySelector('.sc-attach').addEventListener('click', () => fileIn.click());

      fileIn.addEventListener('change', () => {
        const f = fileIn.files[0];
        if (!f) return;
        err.hidden = true;
        const isImg = f.type.startsWith('image/');
        const isVid = f.type.startsWith('video/');
        if (!isImg && !isVid) { fileIn.value = ''; return showErr('Only photos and videos can be sent.'); }
        if (f.size > (isImg ? MAX_IMG : MAX_VID)) {
          fileIn.value = '';
          return showErr(isImg ? 'Photos can be up to 10 MB.' : 'Videos can be up to 50 MB.');
        }
        clearFile();
        file = f;
        blobUrl = URL.createObjectURL(f);
        prev.innerHTML = `
          ${isImg ? `<img src="${blobUrl}" alt="">` : '<span class="sc-vid"><i class="feather-video"></i></span>'}
          <span>${esc(f.name)}</span>
          <button type="button" aria-label="Remove">&times;</button>`;
        prev.querySelector('button').onclick = clearFile;
        prev.hidden = false;
        file = f;
      });

      const send = async () => {
        const text = ta.value.trim();
        if ((!text && !file) || busy) return;
        busy = true;
        btn.disabled = true;
        err.hidden = true;
        btn.innerHTML = '<i class="feather-loader"></i>';

        const fd = new FormData();
        if (text) fd.append('message', text);
        if (file) fd.append('attachment', file);

        try {
          if (!this.ticket) {
            // First message: creates the ticket. The server builds the subject from the text.
            const d = await this.api(this.cfg.urls.store, { method: 'POST', body: fd });
            this.ticket = d.ticket;
            this.sig = sigOf(d.ticket);
            this.closed = !!d.ticket.is_closed;
            this.titleEl.textContent = d.ticket.subject;
            this.liveEl.hidden = false;
            this.addMessages([d.message], true, true);
            this.timer = setInterval(() => this.poll(), 3000);
          } else {
            const d = await this.api(this.cfg.urls.reply.replace('__ID__', this.ticket.id), { method: 'POST', body: fd });
            this.addMessages([d.message], true, false);
          }
          ta.value = '';
          ta.style.height = 'auto';
          clearFile();
        } catch (e) {
          if (e.handled) return;
          showErr(errText(e, 'Could not send your message. Try again.'));
        } finally {
          busy = false;
          btn.disabled = false;
          btn.innerHTML = '<i class="feather-send"></i>';
          ta.focus();
        }
      };

      ta.addEventListener('input', () => { ta.style.height = 'auto'; ta.style.height = Math.min(ta.scrollHeight, 110) + 'px'; });
      ta.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); } });
      btn.addEventListener('click', send);
    }

    /** Closed ticket: rating form, saved rating, or just the closed note. */
    renderClosed(c) {
      const t = this.ticket || {};
      const note = '<div class="sc-closed"><i class="feather-lock me-1"></i> This conversation is closed. Start a new one if you need more help.</div>';

      if (t.rating) {
        c.innerHTML = note + `<div class="sc-rated">Thanks for your feedback<span class="sc-stars-done">${'★'.repeat(t.rating)}${'☆'.repeat(5 - t.rating)}</span></div>`;
        return;
      }
      if (!t.can_rate || !this.cfg.urls.rate) {
        c.innerHTML = note;
        return;
      }

      c.innerHTML = `
        ${note}
        <div class="sc-rate">
          <div class="sc-rate-q">How did we do?</div>
          <div class="sc-stars"><span data-v="1">☆</span><span data-v="2">☆</span><span data-v="3">☆</span><span data-v="4">☆</span><span data-v="5">☆</span></div>
          <textarea rows="2" maxlength="1000" placeholder="Anything you'd like to add? (optional)"></textarea>
          <div class="sc-err" hidden></div>
          <button type="button" class="sc-btn sc-rate-send" disabled>Submit rating</button>
        </div>`;

      const stars = c.querySelectorAll('.sc-stars span');
      const comment = c.querySelector('.sc-rate textarea');
      const err = c.querySelector('.sc-err');
      const btn = c.querySelector('.sc-rate-send');
      let value = 0;

      const paint = (n) => stars.forEach((s) => {
        const on = Number(s.dataset.v) <= n;
        s.textContent = on ? '★' : '☆';
        s.style.color = on ? '#f59e0b' : 'inherit';
      });

      stars.forEach((s) => {
        s.addEventListener('mouseenter', () => paint(Number(s.dataset.v)));
        s.addEventListener('mouseleave', () => paint(value));
        s.addEventListener('click', () => { value = Number(s.dataset.v); paint(value); btn.disabled = false; });
      });

      btn.addEventListener('click', async () => {
        if (!value || btn.disabled) return;
        btn.disabled = true;
        err.hidden = true;
        try {
          const d = await this.api(this.cfg.urls.rate.replace('__ID__', this.ticket.id), {
            method: 'POST',
            body: JSON.stringify({ rating: value, comment: comment.value.trim() || null }),
          });
          this.ticket = d.ticket;
          this.sig = sigOf(d.ticket);
          this.closed = !!d.ticket.is_closed;
          this.renderComposer();
        } catch (e) {
          if (e.handled) return;
          err.textContent = errText(e, 'Could not submit your rating. Try again.');
          err.hidden = false;
          btn.disabled = false;
        }
      });
    }
  }

  window.SupportChat = SupportChat;
})();

</script>
@endverbatim

{{-- Floating live-chat widget. Included once from g-footer. Hidden on the full support page. --}}
@unless(request()->routeIs('support.index'))
<button type="button" class="sw-fab" id="supportBtn" aria-label="Open support chat">
    <i class="feather-message-circle sw-ic-a"></i>
    <i class="feather-chevron-down sw-ic-b"></i>
</button>

<div class="sw-panel" id="supportPanel" role="dialog" aria-label="Support chat">
    <div id="supportWidgetRoot" style="height:100%"></div>
</div>

<style>
    .sw-fab {
        position: fixed; bottom: 24px; right: 24px; z-index: 1000;
        width: 56px; height: 56px; border-radius: 50%; border: 0;
        display: flex; align-items: center; justify-content: center;
        background: var(--bs-primary, #3454d1); color: #fff;
        box-shadow: 0 6px 18px rgba(0,0,0,.3); cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .sw-fab:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.35); }
    .sw-fab i { font-size: 24px; }
    .sw-fab .sw-ic-b { display: none; }
    .sw-fab.is-open .sw-ic-a { display: none; }
    .sw-fab.is-open .sw-ic-b { display: inline; }
    .sw-panel {
        position: fixed; right: 24px; bottom: 92px; z-index: 1040;
        width: 380px; height: min(660px, calc(100vh - 120px));
        border-radius: 18px; overflow: hidden; background: #111318;
        box-shadow: 0 16px 48px rgba(0,0,0,.4);
        opacity: 0; visibility: hidden; transform: translateY(16px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s;
    }
    .sw-panel.open { opacity: 1; visibility: visible; transform: none; }
    @media (max-width: 575.98px) {
        .sw-fab { bottom: 20px; right: 20px; }
        .sw-fab.is-open { display: none; }
        .sw-panel { right: 0; bottom: 0; left: 0; width: 100%; height: 100%; border-radius: 0; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('supportBtn');
        const panel = document.getElementById('supportPanel');
        let chat = null;

        function open() {
            panel.classList.add('open');
            btn.classList.add('is-open');
            if (!chat) {
                chat = new SupportChat(document.getElementById('supportWidgetRoot'), {
                    authed: @json(auth()->check()),
                    csrf: @json(csrf_token()),
                    loginUrl: @json(route('login')),
                    registerUrl: @json(route('register')),
                    faqUrl: @json(route('faq')),
                    userName: @json(auth()->check() ? (explode(' ', trim(auth()->user()->name))[0] ?? null) : null),
                    onClose: close,
                    urls: {
                        index:    @json(route('support.index')),
                        store:    @json(route('support.store')),
                        messages: @json(route('support.fetch-messages', '__ID__')),
                        reply:    @json(route('support.reply', '__ID__')),
                        rate:     @json(route('support.rate', '__ID__')),
                    },
                });
                chat.init();
            } else {
                chat.resume();
            }
        }

        function close() {
            panel.classList.remove('open');
            btn.classList.remove('is-open');
            if (chat) chat.pause();
        }

        btn.addEventListener('click', () => (panel.classList.contains('open') ? close() : open()));
        window.openSupportWidget = open;
        // Any element (e.g. a nav item) with data-support-open opens the widget.
        document.querySelectorAll('[data-support-open]').forEach((el) =>
            el.addEventListener('click', (e) => { e.preventDefault(); open(); }));
    });
</script>
@endunless


<style>
    .footer {
        padding: 1.5rem 0;
        border-top: 1px solid rgba(0,0,0,0.1);
        background-color: #fff;
        margin-top: auto;
    }

    .footer-link {
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .footer-link:hover {
        color: var(--bs-primary) !important;
        transform: translateY(-1px);
    }

    .footer .copyright {
        line-height: 1.8;
    }

    @media (max-width: 767.98px) {
        .footer .row > div:first-child {
            text-align: center;
        }

        .footer .d-flex {
            justify-content: center !important;
            flex-wrap: wrap;
        }
    }
</style>

<!-- JS Files -->
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
    <!--! END: Vendors JS !-->

    <!--! BEGIN: Apps Init !-->
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
    <!--! END: Apps Init !-->

    <!--! BEGIN: Theme Customizer !-->
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->

    @stack('scripts')
</body>
</html>