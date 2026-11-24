# SiMONDES — Aplikasi Monitoring Dana Desa (Halmahera Barat)

Aplikasi web berbasis **Laravel 13 · PHP 8.2+/8.4 · MySQL** untuk memantau alokasi, realisasi, dan sisa anggaran Dana Desa di Kabupaten Halmahera Barat, dibangun untuk DPMD (Dinas Pemberdayaan Masyarakat dan Desa).

Dikembangkan sesuai prototipe `monitoring dana desa.html` dengan peningkatan: autentikasi server, database MySQL, log aktivitas, terminal artisan aman, PWA, dan ekspor Excel/PDF.

---

## ⚡ Fitur

| Fitur | Keterangan |
|---|---|
| Landing page publik | Halaman depan transparansi: statistik, penyerapan per kecamatan, desa teratas, CTA login |
| Login (panel kiri) | Form login berada di panel **sebelah kiri**, rate-limit 5 percobaan, tercatat di log |
| Intip password | **Semua** input password (login, SMTP, ganti password) punya tombol 👁 otomatis |
| Dashboard | Kartu statistik, grafik bar pagu vs realisasi, donut komposisi, tabel monitoring, aktivitas terbaru |
| Data Desa & Pagu | CRUD desa + pagu per tahun anggaran, validasi kode unik & pagu ≥ realisasi |
| Realisasi | CRUD transaksi: bidang, jenis belanja, sumber dana, penerima, no. SPJ, tautan bukti https |
| Laporan & Analisis | Grafik per kecamatan & bidang, rekap, **ekspor Excel & PDF** |
| Rincian per Desa | Buku pengeluaran, kelengkapan dokumen, ekspor Excel & PDF per desa |
| Pengaturan SMTP | Host/port/enkripsi/user/password (terenkripsi) + **kirim email uji** |
| Ganti Password | Kebijakan kuat: 10+ karakter, huruf besar/kecil, angka, simbol |
| Logo & Favicon | Unggah dari pengaturan, **dire-encode server-side** (anti web-shell), tanpa storage:link |
| Log Aktivitas | Audit semua aksi penting + IP address |
| Terminal Artisan | Jalankan `migrate`, `db:seed`, dll dari browser — **daftar putih tanpa shell bebas** |
| PWA | Manifest, service worker, offline fallback, tombol instal |
| Parallax | Animasi transisi berlapis setiap ganti menu + parallax hero |
| Full responsive | Layout mobile penuh (sidebar off-canvas, grid adaptif) |
| Footer | © tahun berjalan — Developed by [Cipta Karya Digital Indonesia](https://ciptakaryadigital.id) |

---

## 🚀 Instalasi

### Cara A — Lokal (dengan terminal)

```bash
composer install
cp .env.example .env
php artisan key:generate
# Edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD (buat database 'simondes' dulu)
php artisan migrate --force && php artisan db:seed --force
php artisan serve   # http://localhost:8000
```

### Cara B — cPanel / hosting TANPA SSH / terminal ⭐

Aplikasi dirancang **tidak butuh `php artisan` sama sekali**:

1. Buat database MySQL di cPanel (mis. `toranggo_simondes`) + user, catat kredensialnya.
2. Set versi PHP **8.2+** di MultiPHP Manager, pastikan ekstensi `pdo_mysql`, `gd`, `mbstring` aktif.
3. Upload seluruh source code, lalu buat berkas `.env` (salin dari `.env.example`):
   ```env
   APP_URL=https://domainanda.id
   APP_ENV=production
   APP_DEBUG=false

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=toranggo_simondes
   DB_USERNAME=toranggo_user
   DB_PASSWORD=******

   SESSION_DRIVER=cookie
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```
4. Jalankan `composer install` (SSH alternatif / fitur Composer cPanel / atau upload folder `vendor` dari komputer lokal — folder vendor hasil `composer install` lokal bisa langsung di-zip dan di-upload).
5. **Buka `https://domainanda.id/install`** → klik **▶ Jalankan Pemasangan**.
   Migrasi database, akun admin, dan data demo dibuat otomatis dari browser.
   Installer terkunci permanen setelah selesai (file `storage/app/installed.lock`).
6. Login — selesai! Untuk perintah artisan berikutnya (migrate baru, cache clear, dll.)
   gunakan menu **Terminal** di dalam aplikasi, tanpa SSH.

> **Alternatif tanpa `/install`:** import `database/simondes-install.sql` lewat **phpMyAdmin** cPanel — semua tabel + data demo + akun admin langsung jadi.

> **Document root** harus mengarah ke folder `public/` (mis. subdomain → `/home/user/simondes/public`).

> Logo/favicon tersimpan di `public/uploads/` — **tidak perlu `storage:link`**.


> Ganti password ini **segera** di menu Pengaturan → Keamanan saat produksi.

### Catatan penting hosting

- **Session = cookie, cache = file, queue = sync** (bawaan) — tidak butuh tabel `sessions`/`cache`/`jobs`. Jika ingin session di database: jalankan `migrate` dari menu Terminal (setelah tabel sessions dibuat lewat `php artisan session:table` saat deploy), lalu ubah `SESSION_DRIVER=database`.
- Folder yang wajib bisa ditulis (writable): `storage/`, `bootstrap/cache/`, `public/uploads/` — biasanya sudah otomatis di cPanel.
- Jika upload logo gagal, cek permission folder `public/uploads` (755 atau 775).

---

## 🔒 Keamanan (Anti PHP2Shell / Web-Shell)

Aplikasi ini dikembangkan dengan pertahanan berlapis terhadap serangan upload web-shell:

1. **Upload gambar dire-encode server-side** (`App\Support\SecureImageUpload`)
   - Validasi MIME asli via `finfo` + `getimagesize` (SVG ditolak karena bisa memuat script).
   - Gambar dibaca via GD lalu **ditulis ulang sebagai PNG murni** — payload PHP yang disisipkan pada metadata/segmen APP diedit hilang total.
   - Nama file acak 40 karakter, tanpa ekstensi eksekusi.
   - Disimpan di `public/uploads/branding` dengan `.htaccess` anti-eksekusi yang dibuat otomatis.
2. **`.htaccess` ganda** (di `public/uploads/*` & `public`) menolak eksekusi semua file script (php/phtml/phar/cgi/pl/py/sh) — lapisan kedua meski file terselundup.
3. **Terminal artisan hanya daftar putih** (`App\Support\ArtisanWhitelist`) — tanpa input teks bebas, dieksekusi via Symfony Process (array args, tanpa shell), perintah destruktif wajib konfirmasi, semua eksekusi masuk log aktivitas.
4. **Content-Security-Policy ketat** (`script-src 'self'`) — semua JS eksternal, tanpa inline script/eval → mitigasi XSS.
5. **Security headers**: X-Frame-Options DENY, nosniff, Referrer-Policy, Permissions-Policy.
6. **CSRF** pada seluruh form, **rate limiting** login (5x/5 menit) + throttle route, **validasi ketat** semua input, **Eloquent** (prepared statements) → anti SQL injection.
7. **Password bcrypt** + kebijakan kompleksitas; password SMTP disimpan terenkripsi (Laravel Crypt).
8. **Installer web** terkunci permanen + kunci `INSTALL_KEY` di `.env` — hanya pemilik server yang dapat memasang.

### Rekomendasi produksi tambahan

```ini
; php.ini — matikan fungsi berbahaya (lapisan OS)
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source,dl
```

```nginx
# nginx — blokir eksekusi PHP di lokasi uploads
location ~* ^/uploads/.*\.(php|phtml|phar)$ { deny all; }
```

- Set `APP_DEBUG=false`, `APP_ENV=production` di `.env` (sudah default di Cara B).
- Aktifkan HTTPS (AutoSSL cPanel) dan `SESSION_SECURE_COOKIE=true`.

---

## 🗂 Struktur Kunci

```
app/
├── Exports/                  # Ekspor Excel (PhpSpreadsheet via maatwebsite)
├── Http/
│   ├── Controllers/          # Installer, Landing, Auth, Dashboard, Village,
│   │                         # Transaction, Report, Detail, Setting, ActivityLog, Terminal
│   └── Middleware/SecurityHeaders.php
├── Models/                   # Village, FundTransaction, Setting, ActivityLog
└── Support/
    ├── SecureImageUpload.php # Upload gambar aman (re-encode GD + .htaccess otomatis)
    ├── ArtisanWhitelist.php  # Daftar putih perintah terminal
    ├── ActivityLogger.php    # Pencatat log aktivitas
    └── DataMaster.php        # Referensi kecamatan/bidang/sumber dana
database/
├── migrations/               # villages, fund_transactions, settings, activity_logs
└── simondes-install.sql      # Dump siap import via phpMyAdmin (tanpa SSH)
public/
├── manifest.webmanifest      # PWA manifest
├── sw.js                     # Service worker
├── offline.html              # Offline fallback
├── uploads/branding/         # Logo & favicon (tanpa storage:link)
└── css/app.css · js/app.js · js/forms.js
resources/views/              # Blade: landing, installer, login, dashboard, modul, PDF
```

## 🧾 Lisensi & Kredit

© Tahun berjalan — DPMD Kabupaten Halmahera Barat — Developed by **[Cipta Karya Digital Indonesia](https://ciptakaryadigital.id)**
# simondes
