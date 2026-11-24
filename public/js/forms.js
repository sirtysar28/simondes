/* Form & modal helpers — aman CSP (tanpa inline script)
   - Konfirmasi hapus via data-confirm
   - Buka modal via location.hash (#idModal)
   - Modal edit: isi field dari data-payload + set action dari data-action */
'use strict';

document.addEventListener('submit', (e) => {
  const form = e.target;
  if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
    e.preventDefault();
  }
});

// Saat tombol [data-modal] diklik: isi form edit bila ada payload + action
document.addEventListener('click', (e) => {
  const opener = e.target.closest('[data-modal]');
  if (!opener) return;
  const modal = document.getElementById(opener.dataset.modal);
  if (!modal) return;

  const payload = opener.dataset.payload;
  if (payload) {
    try {
      const data = JSON.parse(payload);
      modal.querySelectorAll('form [name]').forEach((el) => {
        if (data[el.name] !== undefined && data[el.name] !== null) el.value = data[el.name];
      });
    } catch (_) { /* payload rusak — abaikan */ }
  }

  // form dengan data-dynamic-action mengambil URL dari opener
  const dynForm = modal.querySelector('form[data-dynamic-action]');
  if (dynForm && opener.dataset.action) {
    dynForm.action = opener.dataset.action;
  }
});

document.addEventListener('DOMContentLoaded', () => {
  if (location.hash.length > 1) {
    const modal = document.getElementById(location.hash.slice(1));
    if (modal?.classList.contains('modal-wrap')) {
      modal.classList.add('open');
      setTimeout(() => modal.querySelector('input,select,textarea')?.focus(), 80);
    }
  }
});

// CAPTCHA login: klik gambar untuk mengganti kode
function initCaptcha() {
  const img = $('#captchaImg');
  img?.addEventListener('click', () => {
    img.src = img.src.split('?')[0] + '?t=' + Date.now();
  });
}

// Tab pengaturan (Umum/SMTP/Keamanan/Data)
document.addEventListener('click', (e) => {
  const tab = e.target.closest('.tab[data-tab]');
  if (!tab) return;
  e.preventDefault();
  document.querySelectorAll('.tab[data-tab]').forEach((t) => t.classList.toggle('active', t === tab));
  document.querySelectorAll('.tab-pane').forEach((p) => {
    p.style.display = p.id === 'tab-' + tab.dataset.tab ? '' : 'none';
  });
  history.replaceState(null, '', '#' + tab.getAttribute('href'));
});
