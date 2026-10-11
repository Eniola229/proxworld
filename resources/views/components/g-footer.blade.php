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
 * Views: login prompt → ticket list → new ticket → live chat. No page reloads.
 * Closed tickets show a rating form (or the saved rating) in place of the composer.
 */
(function () {
  if (window.SupportChat) return;

  const css = `
.sc{display:flex;flex-direction:column;height:100%;background:var(--bs-body-bg,#fff);color:var(--bs-body-color,#212529);font-size:14px}
.sc-head{display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--bs-primary,#0d6efd);color:#fff;flex-shrink:0}
.sc-head button{background:none;border:0;color:#fff;font-size:22px;line-height:1;padding:0;cursor:pointer}
.sc-head button[hidden]{display:none}
.sc-title{flex:1;font-weight:600;font-size:15px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sc-live{font-size:11px;opacity:.9}
.sc-live::before{content:'';display:inline-block;width:7px;height:7px;border-radius:50%;background:#4ade80;margin-right:5px}
.sc-body{flex:1;min-height:0;display:flex;flex-direction:column}
.sc-pad{padding:14px;flex-shrink:0}
.sc-list{flex:1;overflow-y:auto}
.sc-item{display:block;width:100%;text-align:left;background:none;border:0;border-bottom:1px solid rgba(128,128,128,.2);padding:12px 16px;cursor:pointer;color:inherit}
.sc-item:hover{background:rgba(128,128,128,.08)}
.sc-item-top{display:flex;justify-content:space-between;gap:8px;align-items:center;margin-bottom:2px}
.sc-item-subj{font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sc-item small{color:#6c757d}
.sc-empty,.sc-loading{text-align:center;color:#6c757d;padding:32px 16px}
.sc-form{padding:16px;overflow-y:auto}
.sc-msgs{flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:12px;background:rgba(128,128,128,.06)}
.sc-m{display:flex;flex-direction:column;max-width:85%}
.sc-m.out{align-self:flex-end;align-items:flex-end}
.sc-m.in{align-self:flex-start;align-items:flex-start}
.sc-bubble{padding:9px 13px;border-radius:14px;white-space:pre-wrap;word-break:break-word}
.sc-m.out .sc-bubble{background:var(--bs-primary,#0d6efd);color:#fff;border-bottom-right-radius:4px}
.sc-m.in .sc-bubble{background:#fff;color:#212529;border:1px solid #e9ecef;border-bottom-left-radius:4px}
.sc-meta{font-size:11px;color:#6c757d;margin-top:3px}
.sc-composer{border-top:1px solid rgba(128,128,128,.2);padding:10px;flex-shrink:0;max-height:60%;overflow-y:auto}
.sc-row{display:flex;gap:8px;align-items:flex-end}
.sc-row textarea{flex:1;resize:none;border:1px solid #ced4da;border-radius:10px;padding:8px 12px;max-height:110px;background:transparent;color:inherit}
.sc-err{color:#dc3545;font-size:12px;margin-bottom:6px}
.sc-closed{text-align:center;color:#6c757d;padding:6px}
.sc-login{margin:auto;text-align:center;padding:32px 24px}
.sc-login i{font-size:42px;color:var(--bs-primary,#0d6efd)}
.sc-rate{padding:6px 4px 2px}
.sc-rate-q{font-weight:600;margin-bottom:6px;text-align:center}
.sc-stars{font-size:30px;line-height:1;cursor:pointer;user-select:none;text-align:center;margin-bottom:8px}
.sc-stars span{padding:0 3px}
.sc-rate textarea{width:100%;resize:none;border:1px solid #ced4da;border-radius:10px;padding:8px 12px;background:transparent;color:inherit;margin-bottom:8px}
.sc-rated{text-align:center;padding:4px 0 2px;font-weight:600}
.sc-stars-done{display:block;font-size:22px;color:#f59e0b;letter-spacing:2px;font-weight:400}
`;
  const styleEl = document.createElement('style');
  styleEl.textContent = css;
  document.head.appendChild(styleEl);

  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const STATUS = { open: ['warning', 'Open'], in_progress: ['info', 'In progress'], closed: ['success', 'Closed'] };
  const badge = (s) => { const [c, l] = STATUS[s] || ['secondary', s]; return `<span class="badge bg-${c}">${esc(l)}</span>`; };
  const errText = (e, fallback) => (e && e.data && (e.data.errors ? Object.values(e.data.errors)[0][0] : e.data.message)) || fallback;
  // Changes whenever the closed/rating state changes, so the composer area only re-renders when it needs to.
  const sigOf = (t) => (t && t.is_closed ? 1 : 0) + ':' + ((t && t.rating) || 0) + ':' + (t && t.can_rate ? 1 : 0);

  class SupportChat {
    /**
     * cfg: { authed, csrf, loginUrl, registerUrl, openTicket?, onClose?,
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
      this.sig = '';

      root.innerHTML = `
        <div class="sc">
          <div class="sc-head">
            <button type="button" class="sc-back" hidden aria-label="Back"><i class="feather-arrow-left"></i></button>
            <div class="sc-title">Support</div>
            <span class="sc-live" hidden>Live</span>
            ${cfg.onClose ? '<button type="button" class="sc-close" aria-label="Close">&times;</button>' : ''}
          </div>
          <div class="sc-body"></div>
        </div>`;
      this.body = root.querySelector('.sc-body');
      this.titleEl = root.querySelector('.sc-title');
      this.backBtn = root.querySelector('.sc-back');
      this.liveEl = root.querySelector('.sc-live');
      this.backBtn.addEventListener('click', () => this.showList());
      const x = root.querySelector('.sc-close');
      if (x) x.addEventListener('click', () => cfg.onClose());
      document.addEventListener('visibilitychange', () => (document.hidden ? this.pause() : this.resume()));
    }

    init() {
      if (!this.cfg.authed) return this.showLogin();
      if (this.cfg.openTicket) return this.showChat({ id: this.cfg.openTicket, subject: 'Ticket' });
      return this.showList();
    }

    pause() { this.paused = true; }
    resume() { this.paused = false; if (this.view === 'chat') this.poll(); }

    setHeader(title, back, live) {
      this.titleEl.textContent = title;
      this.backBtn.hidden = !back;
      this.liveEl.hidden = !live;
    }

    async api(url, opts = {}) {
      const res = await fetch(url, {
        credentials: 'same-origin',
        ...opts,
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': this.cfg.csrf,
          ...(opts.body ? { 'Content-Type': 'application/json' } : {}),
        },
      });
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
      this.setHeader('Support', false, false);
      this.body.innerHTML = `
        <div class="sc-login">
          <i class="feather-lock"></i>
          <h6 class="mt-3 mb-2">${expired ? 'Your session has expired' : 'Log in to chat with support'}</h6>
          <p class="text-muted mb-4">You need an account to open tickets and chat with our team.</p>
          <a href="${esc(this.cfg.loginUrl)}" class="btn btn-primary w-100 mb-2">Log in</a>
          ${this.cfg.registerUrl ? `<a href="${esc(this.cfg.registerUrl)}" class="btn btn-light w-100">Create an account</a>` : ''}
        </div>`;
    }

    async showList() {
      this.stopPolling();
      this.view = 'list';
      this.ticket = null;
      this.setHeader('Support', false, false);
      this.body.innerHTML = `
        <div class="sc-pad"><button type="button" class="btn btn-primary w-100 sc-new"><i class="feather-plus me-1"></i> New ticket</button></div>
        <div class="sc-list"><div class="sc-loading">Loading your tickets…</div></div>`;
      this.body.querySelector('.sc-new').onclick = () => this.showNew();
      const list = this.body.querySelector('.sc-list');
      try {
        const { tickets } = await this.api(this.cfg.urls.index);
        if (this.view !== 'list') return;
        if (!tickets.length) {
          list.innerHTML = '<div class="sc-empty">No tickets yet. Create one and our team will reply here.</div>';
          return;
        }
        list.innerHTML = tickets.map((t, i) => `
          <button type="button" class="sc-item" data-i="${i}">
            <div class="sc-item-top"><span class="sc-item-subj">${esc(t.subject)}</span>${badge(t.status)}</div>
            <small>#${esc(t.short_id)} · Updated ${esc(t.updated)}</small>
          </button>`).join('');
        list.querySelectorAll('.sc-item').forEach((el) => { el.onclick = () => this.showChat(tickets[+el.dataset.i]); });
      } catch (e) {
        if (e.handled) return;
        list.innerHTML = '<div class="sc-empty">Could not load tickets. <a href="#" class="sc-retry">Try again</a></div>';
        list.querySelector('.sc-retry').onclick = (ev) => { ev.preventDefault(); this.showList(); };
      }
    }

    showNew() {
      this.stopPolling();
      this.view = 'new';
      this.setHeader('New ticket', true, false);
      this.body.innerHTML = `
        <form class="sc-form">
          <div class="mb-3"><label class="form-label">Subject</label>
            <input type="text" name="subject" class="form-control" maxlength="255" required placeholder="e.g. What can we help you with?"></div>
          <div class="mb-3"><label class="form-label">Message</label>
            <textarea name="message" class="form-control" rows="6" maxlength="5000" required placeholder="Give us the details and we'll sort it out"></textarea></div>
          <div class="sc-err" hidden></div>
          <button type="submit" class="btn btn-primary w-100">Submit ticket</button>
        </form>`;
      const form = this.body.querySelector('form');
      const err = form.querySelector('.sc-err');
      const btn = form.querySelector('button[type=submit]');
      form.onsubmit = async (ev) => {
        ev.preventDefault();
        err.hidden = true;
        btn.disabled = true;
        btn.textContent = 'Submitting…';
        try {
          const { ticket } = await this.api(this.cfg.urls.store, {
            method: 'POST',
            body: JSON.stringify({ subject: form.subject.value.trim(), message: form.message.value.trim() }),
          });
          this.showChat(ticket);
        } catch (e) {
          if (e.handled) return;
          err.textContent = errText(e, 'Could not create the ticket. Try again.');
          err.hidden = false;
          btn.disabled = false;
          btn.textContent = 'Submit ticket';
        }
      };
    }

    async showChat(ticket) {
      this.stopPolling();
      this.view = 'chat';
      this.ticket = ticket;
      this.lastId = 0;
      this.seen = new Set();
      this.closed = !!ticket.is_closed;
      this.sig = sigOf(ticket);
      this.setHeader(ticket.subject || 'Ticket', true, true);
      this.body.innerHTML = '<div class="sc-msgs"><div class="sc-loading">Loading messages…</div></div><div class="sc-composer"></div>';
      this.msgsEl = this.body.querySelector('.sc-msgs');
      this.renderComposer();
      await this.poll();
      if (this.view === 'chat' && this.ticket.id === ticket.id) {
        this.timer = setInterval(() => this.poll(), 3000);
      }
    }

    /* ---------- chat internals ---------- */

    stopPolling() { clearInterval(this.timer); this.timer = null; }

    async poll() {
      if (this.view !== 'chat' || this.paused || this.polling) return;
      this.polling = true;
      const id = this.ticket.id;
      try {
        const url = this.cfg.urls.messages.replace('__ID__', id) + (this.lastId ? `?after=${encodeURIComponent(this.lastId)}` : '');
        const d = await this.api(url);
        if (this.view !== 'chat' || this.ticket.id !== id) return;
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

    addMessages(list, force = false, track = true) {
      const box = this.msgsEl;
      const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
      const first = !this.lastId;
      box.querySelector('.sc-loading')?.remove();
      let added = false;
      for (const m of list) {
        if (this.seen.has(m.id)) continue;
        this.seen.add(m.id);
        if (track) this.lastId = m.id;
        box.insertAdjacentHTML('beforeend', `
          <div class="sc-m ${m.is_admin ? 'in' : 'out'}">
            <div class="sc-bubble">${esc(m.message)}</div>
            <div class="sc-meta">${m.is_admin ? `<strong>${esc(m.name)}</strong> · ` : ''}${esc(m.time)}</div>
          </div>`);
        added = true;
      }
      if (added && (nearBottom || first || force)) box.scrollTop = box.scrollHeight;
    }

    renderComposer() {
      const c = this.body.querySelector('.sc-composer');
      if (!c) return;
      if (this.closed) { this.renderClosed(c); return; }
      c.innerHTML = `
        <div class="sc-err" hidden></div>
        <div class="sc-row">
          <textarea rows="1" maxlength="5000" placeholder="Type your message…"></textarea>
          <button type="button" class="btn btn-primary sc-send" aria-label="Send"><i class="feather-send"></i></button>
        </div>`;
      const ta = c.querySelector('textarea');
      const btn = c.querySelector('.sc-send');
      const err = c.querySelector('.sc-err');
      const send = async () => {
        const text = ta.value.trim();
        if (!text || btn.disabled) return;
        btn.disabled = true;
        err.hidden = true;
        try {
          const d = await this.api(this.cfg.urls.reply.replace('__ID__', this.ticket.id), {
            method: 'POST',
            body: JSON.stringify({ message: text }),
          });
          ta.value = '';
          ta.style.height = 'auto';
          this.addMessages([d.message], true, false);
        } catch (e) {
          if (e.handled) return;
          err.textContent = errText(e, 'Could not send your message. Try again.');
          err.hidden = false;
        } finally {
          btn.disabled = false;
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
      const note = '<div class="sc-closed"><i class="feather-lock me-1"></i> This ticket is closed. Open a new ticket if you need more help.</div>';

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
          <button type="button" class="btn btn-primary w-100 sc-rate-send" disabled>Submit rating</button>
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
    <i class="feather-headphones"></i>
    <span>Support</span>
</button>

<div class="sw-panel" id="supportPanel" role="dialog" aria-label="Support chat">
    <div id="supportWidgetRoot" style="height:100%"></div>
</div>

<style>
    .sw-fab {
        position: fixed; bottom: 30px; right: 30px; z-index: 1000;
        display: flex; align-items: center; gap: 8px;
        background: var(--bs-primary, #0d6efd); color: #fff; border: 0;
        padding: 12px 20px; border-radius: 50px; font-size: 14px; font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,.15); cursor: pointer; transition: transform .2s ease, box-shadow .2s ease;
    }
    .sw-fab:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.2); }
    .sw-fab i { font-size: 20px; }
    .sw-panel {
        position: fixed; right: 30px; bottom: 90px; z-index: 1040;
        width: 380px; height: min(600px, calc(100vh - 120px));
        border-radius: 14px; overflow: hidden; background: var(--bs-body-bg, #fff);
        box-shadow: 0 12px 40px rgba(0,0,0,.25);
        opacity: 0; visibility: hidden; transform: translateY(16px);
        transition: opacity .2s ease, transform .2s ease, visibility .2s;
    }
    .sw-panel.open { opacity: 1; visibility: visible; transform: none; }
    @media (max-width: 575.98px) {
        .sw-fab { bottom: 20px; right: 20px; padding: 12px 14px; }
        .sw-fab span { display: none; }
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
            if (!chat) {
                chat = new SupportChat(document.getElementById('supportWidgetRoot'), {
                    authed: @json(auth()->check()),
                    csrf: @json(csrf_token()),
                    loginUrl: @json(route('login')),
                    registerUrl: @json(route('register')),
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