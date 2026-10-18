/* ProxWorld shared JS. Plain ES5, no dependencies. */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  root.className += ' js';

  var header = doc.getElementById('siteHeader');
  var menuBtn = doc.getElementById('menuBtn');
  var links = doc.getElementById('hdrLinks');
  var MOBILE_MAX = 900;

  function isMobile() {
    return (window.innerWidth || root.clientWidth) <= MOBILE_MAX;
  }

  /* ── header: scrolled state ───────────────────────── */
  function onScroll() {
    if (!header) return;
    var y = window.pageYOffset || root.scrollTop || 0;
    if (y > 20) { header.classList.add('scrolled'); } else { header.classList.remove('scrolled'); }
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ── mobile menu ──────────────────────────────────── */
  function closeAllSubs() {
    if (!links) return;
    var open = links.querySelectorAll('.has-sub.open');
    for (var i = 0; i < open.length; i++) {
      open[i].classList.remove('open');
      var t = open[i].querySelector('.sub-toggle');
      if (t) t.setAttribute('aria-expanded', 'false');
    }
  }

  function setMenu(state) {
    if (!header || !menuBtn) return;
    if (state) {
      header.classList.add('menu-open');
      root.classList.add('menu-lock');
    } else {
      header.classList.remove('menu-open');
      root.classList.remove('menu-lock');
      closeAllSubs();
    }
    menuBtn.setAttribute('aria-expanded', state ? 'true' : 'false');
    menuBtn.setAttribute('aria-label', state ? 'Close menu' : 'Open menu');
  }

  if (menuBtn) {
    menuBtn.addEventListener('click', function () {
      setMenu(!header.classList.contains('menu-open'));
    });
  }

  /* ── dropdowns (click / tap / keyboard) ───────────── */
  if (links) {
    var toggles = links.querySelectorAll('.sub-toggle');
    for (var i = 0; i < toggles.length; i++) {
      toggles[i].addEventListener('click', function (e) {
        e.stopPropagation();
        var li = this.parentNode;
        var willOpen = !li.classList.contains('open');
        closeAllSubs();
        if (willOpen) {
          li.classList.add('open');
          this.setAttribute('aria-expanded', 'true');
        }
      });
    }

    // close menu after tapping a real link
    links.addEventListener('click', function (e) {
      var el = e.target;
      while (el && el !== links) {
        if (el.tagName === 'A') { setMenu(false); closeAllSubs(); return; }
        el = el.parentNode;
      }
    });
  }

  doc.addEventListener('click', function (e) {
    if (isMobile() || !links) return;
    if (!links.contains(e.target)) closeAllSubs();
  });

  doc.addEventListener('keydown', function (e) {
    var key = e.key || e.keyCode;
    if (key === 'Escape' || key === 'Esc' || key === 27) {
      if (header && header.classList.contains('menu-open')) { setMenu(false); if (menuBtn) menuBtn.focus(); }
      closeAllSubs();
    }
  });

  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      if (!isMobile()) setMenu(false);
    }, 120);
  });

  /* ── custom cursor: mouse devices only ────────────── */
  var cursor = doc.getElementById('cursor');
  var ring = doc.getElementById('cursorRing');
  var fine = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  if (cursor && ring && fine) {
    root.className += ' has-cursor';
    var mx = 0, my = 0, rx = 0, ry = 0;
    doc.addEventListener('mousemove', function (e) {
      mx = e.clientX; my = e.clientY;
      cursor.style.transform = 'translate(' + mx + 'px,' + my + 'px)';
    });
    var raf = window.requestAnimationFrame || function (f) { return setTimeout(f, 16); };
    (function loop() {
      rx += (mx - rx) * 0.18;
      ry += (my - ry) * 0.18;
      ring.style.transform = 'translate(' + rx + 'px,' + ry + 'px)';
      raf(loop);
    })();
    doc.addEventListener('mouseover', function (e) {
      var t = e.target;
      var hit = false;
      while (t && t !== doc) {
        if (t.tagName === 'A' || t.tagName === 'BUTTON' || t.tagName === 'SUMMARY') { hit = true; break; }
        t = t.parentNode;
      }
      if (hit) { ring.classList.add('hover'); } else { ring.classList.remove('hover'); }
    });
  }

  /* ── reveal on scroll ─────────────────────────────── */
  var reveals = doc.querySelectorAll('.reveal');
  if (reveals.length) {
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        for (var k = 0; k < entries.length; k++) {
          if (entries[k].isIntersecting) {
            entries[k].target.classList.add('visible');
            io.unobserve(entries[k].target);
          }
        }
      }, { threshold: 0.12 });
      for (var r = 0; r < reveals.length; r++) io.observe(reveals[r]);
    } else {
      for (var s = 0; s < reveals.length; s++) reveals[s].classList.add('visible');
    }
  }

  /* ── code tabs: <div data-tabs> with .tab buttons + .tab-panel ── */
  var tabGroups = doc.querySelectorAll('[data-tabs]');
  for (var g = 0; g < tabGroups.length; g++) {
    (function (group) {
      var tabs = group.querySelectorAll('.tab');
      var panels = group.querySelectorAll('.tab-panel');
      function select(idx) {
        for (var n = 0; n < tabs.length; n++) {
          tabs[n].setAttribute('aria-selected', n === idx ? 'true' : 'false');
          if (n === idx) { panels[n].removeAttribute('hidden'); } else { panels[n].setAttribute('hidden', 'hidden'); }
        }
      }
      for (var m = 0; m < tabs.length; m++) {
        (function (idx) {
          tabs[idx].addEventListener('click', function () { select(idx); });
        })(m);
      }
    })(tabGroups[g]);
  }

  /* ── location filter: input#locSearch + .chip[data-name] ── */
  var search = doc.getElementById('locSearch');
  if (search) {
    var chips = doc.querySelectorAll('.loc-region .chip');
    var regions = doc.querySelectorAll('.loc-region');
    var none = doc.getElementById('locNone');
    search.addEventListener('input', function () {
      var q = search.value.toLowerCase().replace(/^\s+|\s+$/g, '');
      var total = 0;
      for (var c = 0; c < chips.length; c++) {
        var match = !q || chips[c].getAttribute('data-name').indexOf(q) !== -1;
        if (match) { chips[c].classList.remove('is-hidden'); total++; } else { chips[c].classList.add('is-hidden'); }
      }
      for (var rg = 0; rg < regions.length; rg++) {
        var visible = regions[rg].querySelectorAll('.chip:not(.is-hidden)').length;
        if (visible) { regions[rg].classList.remove('is-empty'); } else { regions[rg].classList.add('is-empty'); }
      }
      if (none) { if (total === 0) { none.classList.add('show'); } else { none.classList.remove('show'); } }
    });
  }
})();
