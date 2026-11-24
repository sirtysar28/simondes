/* ============================================================
   SiMONDES — app.js
   Sidebar, transisi parallax, intip password, toast, modal,
   grafik canvas, dan registrasi Service Worker (PWA).
   ============================================================ */
'use strict';

/* ---------- util ---------- */
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

/* ---------- Toast ---------- */
function toast(message) {
  let el = $('#toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'toast'; el.className = 'toast'; document.body.appendChild(el);
  }
  el.textContent = message;
  el.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => el.classList.remove('show'), 3200);
}

/* ---------- Format Rupiah (untuk grafik) ---------- */
function compactRp(n) {
  n = Number(n) || 0;
  if (n >= 1e9) return (n / 1e9).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' M';
  if (n >= 1e6) return (n / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' jt';
  return n.toLocaleString('id-ID');
}

/* ============================================================
   1) PEEK PASSWORD — setiap input password punya tombol mata
   ============================================================ */
function initPeekPassword(root = document) {
  $$('input[type="password"]', root).forEach((input) => {
    if (input.dataset.peekReady) return;
    input.dataset.peekReady = '1';

    const wrap = document.createElement('div');
    wrap.className = 'pw-wrap';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    const eye = document.createElement('button');
    eye.type = 'button';
    eye.className = 'pw-eye';
    eye.setAttribute('aria-label', 'Tampilkan / sembunyikan password');
    eye.innerHTML =
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    eye.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      eye.style.color = show ? '#3264e9' : '';
    });
    wrap.appendChild(eye);
  });
}

/* Pantau elemen baru (misal modal yang dibuka) agar otomatis dapat tombol intip */
function watchPeek() {
  if (!('MutationObserver' in window)) return;
  new MutationObserver((muts) => {
    muts.forEach((m) =>
      m.addedNodes.forEach((n) => {
        if (n.nodeType === 1) initPeekPassword(n.matches?.('form') ? n : document);
      })
    );
  }).observe(document.body, { childList: true, subtree: true });
}

/* ============================================================
   2) MODAL (tambah/edit) — form server-rendered
   ============================================================ */
function initModals() {
  document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-modal]');
    if (opener) {
      const modal = $('#' + opener.dataset.modal);
      if (!modal) return;
      // Isi form edit dari data-payload (JSON)
      const payload = opener.dataset.payload;
      if (payload) {
        try {
          const data = JSON.parse(payload);
          $$('form [name]', modal).forEach((el) => {
            if (data[el.name] !== undefined && data[el.name] !== null) el.value = data[el.name];
          });
        } catch (_) { /* abaikan */ }
      } else {
        const form = $('form', modal);
        if (form) form.reset();
      }
      modal.classList.add('open');
      setTimeout(() => $('input,select,textarea', modal)?.focus(), 60);
    }

    if (e.target.matches('.modal-wrap') || e.target.closest('[data-close-modal]')) {
      e.target.closest('.modal-wrap')?.classList.remove('open');
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') $$('.modal-wrap.open').forEach((m) => m.classList.remove('open'));
  });

  // Auto-submit filter
  $$('.auto-submit').forEach((el) => el.addEventListener('change', () => el.form?.submit()));
}

/* ============================================================
   3) SIDEBAR + DROPDOWN USER
   ============================================================ */
function initChrome() {
  $('#menuBtn')?.addEventListener('click', () => $('#sidebar')?.classList.toggle('open'));
  $('#userBtn')?.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); $('#userDrop')?.classList.toggle('open'); });
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.user-menu')) $('#userDrop')?.classList.remove('open');
  });
}

/* ============================================================
   4) TRANSISI PARALLAX antar halaman/menu
   ============================================================ */
function initParallaxNav() {
  document.body.classList.add('page-enter');
  setTimeout(() => document.body.classList.remove('page-enter'), 900);

  // Saat klik menu: mainkan animasi keluar berlapis (parallax) lalu navigasi
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a[data-nav]');
    if (!link || link.ctrlKey || link.metaKey || link.shiftKey || link.button === 1) return;
    e.preventDefault();
    document.body.classList.add('page-leave');
    const target = link.getAttribute('href');
    setTimeout(() => { window.location.href = target; }, 230);
  });

  // Parallax elemen dekoratif hero mengikuti scroll & gerakan mouse
  const layers = $$('.plx, .hero:before');
  const onScroll = () => {
    const y = window.scrollY;
    $$('.plx').forEach((el) => {
      const depth = parseFloat(el.dataset.depth || '0.18');
      el.style.transform = `translateY(${y * depth}px)`;
    });
  };
  window.addEventListener('scroll', onScroll, { passive: true });

  document.querySelectorAll('.landing-hero, .login-right').forEach((zone) => {
    zone.addEventListener('mousemove', (ev) => {
      const r = zone.getBoundingClientRect();
      const dx = (ev.clientX - r.left) / r.width - 0.5;
      const dy = (ev.clientY - r.top) / r.height - 0.5;
      $$('.plx', zone).forEach((el) => {
        const depth = parseFloat(el.dataset.depth || '0.2') * 40;
        el.style.transform = `translate(${dx * depth}px, ${dy * depth}px)`;
      });
    });
  });
}

/* ============================================================
   5) GRAFIK CANVAS (bar & donut) — data dari atribut JSON
   ============================================================ */
const SiCharts = {
  setup(canvas) {
    const box = canvas.parentElement;
    const ratio = window.devicePixelRatio || 1;
    const w = Math.max(box.getBoundingClientRect().width, 200);
    const h = Math.max(box.getBoundingClientRect().height, 140);
    canvas.width = Math.round(w * ratio);
    canvas.height = Math.round(h * ratio);
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
    return { ctx, w, h };
  },

  rounded(ctx, x, y, w, h, r, fill) {
    if (w <= 0 || h <= 0) return;
    ctx.fillStyle = fill;
    ctx.beginPath();
    ctx.roundRect(x, y, w, h, Math.min(r, w / 2, h / 2));
    ctx.fill();
  },

  bar(canvas, labels, series) {
    const { ctx, w, h } = SiCharts.setup(canvas);
    const left = 46, right = 12, top = 20, bottom = 46;
    const plotW = w - left - right, plotH = h - top - bottom;
    const n = labels.length;
    const max = Math.max(...series.flatMap((s) => s.values), 1) * 1.2;

    ctx.font = '10px system-ui'; ctx.textAlign = 'right'; ctx.fillStyle = '#8e9db4';
    for (let i = 0; i <= 4; i++) {
      const y = top + (plotH * i) / 4;
      ctx.strokeStyle = '#ebeff7';
      ctx.beginPath(); ctx.moveTo(left, y); ctx.lineTo(w - right, y); ctx.stroke();
      ctx.fillText(compactRp(max * (4 - i) / 4), left - 8, y + 3);
    }
    if (!n) {
      ctx.textAlign = 'center'; ctx.fillStyle = '#94a0b4';
      ctx.fillText('Belum ada data untuk grafik', w / 2, h / 2);
      return;
    }
    const slot = plotW / n;
    const groupW = slot * 0.62;
    const bw = Math.min(20, groupW / series.length);

    labels.forEach((label, i) => {
      const cx = left + i * slot + slot / 2;
      series.forEach((s, si) => {
        const v = s.values[i] || 0;
        const bh = (v / max) * plotH;
        const x = cx - (series.length * bw) / 2 + si * bw + 1;
        SiCharts.rounded(ctx, x, top + plotH - bh, bw - 2, bh, 3, s.color);
      });
      ctx.fillStyle = '#8594ad'; ctx.textAlign = 'center'; ctx.font = '10px system-ui';
      ctx.fillText(label.length > 12 ? label.slice(0, 11) + '…' : label, cx, top + plotH + 17, Math.max(30, slot - 3));
    });
  },

  donut(canvas, pctVal) {
    const { ctx, w, h } = SiCharts.setup(canvas);
    const x = w / 2, y = h / 2, rad = Math.min(w, h) * 0.38, thick = Math.min(28, rad * 0.34);
    const share = pctVal > 0 ? Math.max(0, Math.min(1, pctVal / 100)) : 0;

    ctx.beginPath(); ctx.arc(x, y, rad, -Math.PI / 2, Math.PI * 1.5);
    ctx.strokeStyle = '#e7edf8'; ctx.lineWidth = thick; ctx.stroke();
    if (share > 0) {
      ctx.beginPath(); ctx.arc(x, y, rad, -Math.PI / 2, -Math.PI / 2 + Math.PI * 2 * share);
      ctx.strokeStyle = '#3971e9'; ctx.lineWidth = thick; ctx.lineCap = 'round'; ctx.stroke();
    }
    ctx.textAlign = 'center';
    ctx.fillStyle = '#1b2d4e'; ctx.font = 'bold 25px system-ui';
    ctx.fillText(Math.round(share * 100) + '%', x, y + 3);
    ctx.fillStyle = '#8a9ab2'; ctx.font = '11px system-ui';
    ctx.fillText('Terealisasi', x, y + 24);
  },

  renderAll() {
    $$('canvas[data-chart]').forEach((canvas) => {
      try {
        const cfg = JSON.parse(canvas.dataset.chart);
        if (cfg.type === 'bar') SiCharts.bar(canvas, cfg.labels, cfg.series);
        if (cfg.type === 'donut') SiCharts.donut(canvas, cfg.value);
      } catch (_) { /* abaikan */ }
    });
  },
};

/* ============================================================
   6) PWA — registrasi service worker
   ============================================================ */
function initPwa() {
  if (!('serviceWorker' in navigator)) return;
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
  });

  // Tombol instal PWA (jika tersedia)
  let deferredPrompt = null;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    const btn = $('#pwaInstall');
    if (btn) {
      btn.style.display = 'inline-flex';
      btn.addEventListener('click', async () => {
        if (deferredPrompt) {
          deferredPrompt.prompt();
          deferredPrompt = null;
          btn.style.display = 'none';
        }
      });
    }
  });
}

/* ============================================================
   7) Hilangkan spam bot dari form (honeypot opsional) & init
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  initChrome();
  initModals();
  initPeekPassword();
  watchPeek();
  initParallaxNav();
  SiCharts.renderAll();
  initPwa();

  let resizeDebounce;
  window.addEventListener('resize', () => {
    clearTimeout(resizeDebounce);
    resizeDebounce = setTimeout(SiCharts.renderAll, 140);
  });
});
