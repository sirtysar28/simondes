@extends('layouts.app')
@section('title', 'Pengaturan')
@section('breadcrumb', 'Pengaturan')
@section('page_title', 'Pengaturan & Manajemen')

@section('content')
<section id="pengaturan">
  <h2 style="margin-top:0">Pengaturan &amp; Manajemen Aplikasi</h2>
  <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:-5px 0 19px">Kelola identitas aplikasi, SMTP, keamanan akun, dan data aplikasi.</p>

  @php($activeTab = session('tab', 'umum'))
  <div class="tabbar">
    <a class="tab {{ $activeTab === 'umum' ? 'active' : '' }}" href="#tab-umum" data-tab="umum">◈ Umum &amp; Branding</a>
    <a class="tab {{ $activeTab === 'smtp' ? 'active' : '' }}" href="#tab-smtp" data-tab="smtp">✉ Pengaturan SMTP</a>
    <a class="tab {{ $activeTab === 'keamanan' ? 'active' : '' }}" href="#tab-keamanan" data-tab="keamanan">🔑 Ganti Password</a>
    <a class="tab {{ $activeTab === 'data' ? 'active' : '' }}" href="#tab-data" data-tab="data">⛃ Data &amp; Cadangan</a>
  </div>

  <div class="setting-section">
    {{-- ============ TAB UMUM ============ --}}
    <div class="setting-card tab-pane" id="tab-umum" {{ $activeTab !== 'umum' ? 'style="display:none"' : '' }}>
      <h3>Identitas Aplikasi</h3>
      <p>Nama aplikasi tampil pada sidebar, judul browser, dan PWA.</p>
      <form method="POST" action="{{ route('settings.general') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-grid" style="margin-bottom:18px">
          <div class="form-group"><label for="s-name">Nama Instansi / Aplikasi</label><input id="s-name" name="app_name" value="{{ $appName }}" required maxlength="100"></div>
          <div class="form-group"><label for="s-short">Nama Singkat (maks 40 karakter)</label><input id="s-short" name="app_short" value="{{ $appShort }}" required maxlength="40"></div>
        </div>

        <h3 style="margin:0 0 6px">Logo Instansi</h3>
        <p>Unggah PNG/JPG/WebP maks 2 MB. Gambar otomatis <b>diproses ulang &amp; disanitasi server</b> (dire-encode) sehingga bebas payload berbahaya. Logo tampil di sidebar, halaman depan, dan login.</p>
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:14px">
          <div class="brand-preview">
            @if($logoPath)<img src="{{ asset($logoPath) }}" alt="Logo aktif">@else<b>{{ strtoupper(mb_substr($appShort,0,2)) }}</b>@endif
          </div>
          <div class="toolbar-left">
            <label class="btn primary file-btn">↑ Unggah Logo <input type="file" name="logo" accept="image/png,image/jpeg,image/webp"></label>
            @if($logoPath)
              <label class="btn white" style="gap:7px"><input type="checkbox" name="remove_logo" value="1" style="width:auto"> Hapus logo</label>
            @endif
          </div>
        </div>

        <h3 style="margin:10px 0 6px">Favicon</h3>
        <p>Ikon kecil pada tab browser. PNG/JPG/WebP maks 512 KB, idealnya 512×512 (otomatis di-resize aman saat diproses).</p>
        <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;margin-bottom:18px">
          <div class="brand-preview" style="border-radius:50%">
            @if($faviconPath)<img src="{{ asset($faviconPath) }}" alt="Favicon aktif">@else<b>◈</b>@endif
          </div>
          <div class="toolbar-left">
            <label class="btn primary file-btn">↑ Unggah Favicon <input type="file" name="favicon" accept="image/png,image/jpeg,image/webp"></label>
            @if($faviconPath)
              <label class="btn white" style="gap:7px"><input type="checkbox" name="remove_favicon" value="1" style="width:auto"> Hapus favicon</label>
            @endif
          </div>
        </div>

        <button type="submit" class="btn primary">✓ Simpan Pengaturan Umum</button>
      </form>
    </div>

    {{-- ============ TAB SMTP ============ --}}
    <div class="setting-card tab-pane" id="tab-smtp" {{ $activeTab !== 'smtp' ? 'style="display:none"' : '' }}>
      <h3>Konfigurasi SMTP (Pengaturan Email)</h3>
      <p>Dipakai untuk mengirim email sistem (notifikasi &amp; email uji). Password disimpan terenkripsi di database.</p>
      <form method="POST" action="{{ route('settings.smtp') }}">
        @csrf
        <div class="form-grid">
          <div class="form-group"><label for="smtp-host">SMTP Host</label><input id="smtp-host" name="smtp_host" value="{{ $smtp['host'] }}" placeholder="smtp.example.com" required></div>
          <div class="form-group"><label for="smtp-port">Port</label><input id="smtp-port" name="smtp_port" type="number" min="1" max="65535" value="{{ $smtp['port'] }}" required></div>
          <div class="form-group">
            <label for="smtp-enc">Enkripsi</label>
            <select id="smtp-enc" name="smtp_encryption" class="auto-submit" style="width:100%;border:1px solid #dfe6f2;background:#fbfcff;border-radius:9px;padding:11px">
              <option value="tls" {{ $smtp['encryption'] === 'tls' ? 'selected' : '' }}>TLS (umumnya port 587)</option>
              <option value="ssl" {{ $smtp['encryption'] === 'ssl' ? 'selected' : '' }}>SSL (umumnya port 465)</option>
              <option value="none" {{ $smtp['encryption'] === 'none' ? 'selected' : '' }}>Tanpa enkripsi</option>
            </select>
          </div>
          <div class="form-group"><label for="smtp-user">Username SMTP</label><input id="smtp-user" name="smtp_username" value="{{ $smtp['username'] }}" autocomplete="off"></div>
          <div class="form-group">
            <label for="smtp-pass">Password SMTP {{ $smtp['password_set'] ? '(terisi — biarkan kosong jika tidak diganti)' : '' }}</label>
            <input id="smtp-pass" name="smtp_password" type="password" autocomplete="new-password" placeholder="{{ $smtp['password_set'] ? '••••••••••' : '' }}">
          </div>
          <div class="form-group"><label for="smtp-from">Email Pengirim (From)</label><input id="smtp-from" name="smtp_from_address" type="email" value="{{ $smtp['from_address'] }}" required></div>
          <div class="form-group full"><label for="smtp-from-name">Nama Pengirim</label><input id="smtp-from-name" name="smtp_from_name" value="{{ $smtp['from_name'] }}" required></div>
        </div>
        <div class="modal-actions" style="justify-content:flex-start">
          <button type="submit" class="btn primary">✓ Simpan Konfigurasi SMTP</button>
        </div>
      </form>

      <hr style="border:none;border-top:1px solid var(--line);margin:20px 0">
      <h3>Kirim Email Uji</h3>
      <p>Verifikasi konfigurasi dengan mengirim email percobaan.</p>
      <form method="POST" action="{{ route('settings.smtp.test') }}" class="toolbar-left">
        @csrf
        <input class="field" style="background:#fff" type="email" name="test_to" placeholder="tujuan@example.com" required>
        <button type="submit" class="btn soft">✉ Kirim Email Uji</button>
      </form>
    </div>

    {{-- ============ TAB KEAMANAN ============ --}}
    <div class="setting-card tab-pane" id="tab-keamanan" {{ $activeTab !== 'keamanan' ? 'style="display:none"' : '' }}>
      <h3 id="keamanan">Ganti Password Akun</h3>
      <p>Password minimal 10 karakter, kombinasi huruf kecil, huruf besar, angka, dan simbol.</p>
      <form method="POST" action="{{ route('settings.password') }}" style="max-width:420px">
        @csrf
        <div class="form-group" style="margin-bottom:14px"><label for="pw-current">Password Saat Ini</label><input id="pw-current" name="current_password" type="password" required autocomplete="current-password"></div>
        <div class="form-group" style="margin-bottom:14px"><label for="pw-new">Password Baru</label><input id="pw-new" name="password" type="password" required autocomplete="new-password"></div>
        <div class="form-group" style="margin-bottom:18px"><label for="pw-confirm">Konfirmasi Password Baru</label><input id="pw-confirm" name="password_confirmation" type="password" required autocomplete="new-password"></div>
        <button type="submit" class="btn primary">🔑 Perbarui Password</button>
      </form>
      <div class="notice" style="margin-top:18px">🔒 Setiap password dilindungi hash bcrypt dan setiap percobaan login tercatat pada <a href="{{ route('logs.index') }}">Log Aktivitas</a>.</div>
    </div>

    {{-- ============ TAB DATA ============ --}}
    <div class="setting-card tab-pane" id="tab-data" {{ $activeTab !== 'data' ? 'style="display:none"' : '' }}>
      <h3>Cadangkan Data (JSON)</h3>
      <p>Ekspor seluruh data desa dan transaksi ke berkas JSON, atau pulihkan dari cadangan yang valid.</p>
      <div class="toolbar-left" style="margin-bottom:8px">
        <a class="btn primary" href="{{ route('settings.backup') }}">↓ Unduh Cadangan JSON</a>
      </div>
      <form method="POST" action="{{ route('settings.restore') }}" enctype="multipart/form-data" class="toolbar-left">
        @csrf
        <label class="btn soft file-btn">↑ Impor Cadangan JSON <input type="file" name="restore_file" accept="application/json,.json" required></label>
        <button type="submit" class="btn white">Pulihkan</button>
      </form>

      <hr style="border:none;border-top:1px solid var(--line);margin:20px 0">
      <h3>Reset ke Data Demo</h3>
      <p>Memulihkan contoh data simulasi (24 desa, 2 tahun anggaran). Semua data aktif akan diganti.</p>
      <form method="POST" action="{{ route('settings.reset') }}" data-confirm="Yakin reset? Semua data aktif akan diganti data demo.">
        @csrf
        <button type="submit" class="btn red">↻ Reset ke Data Demo</button>
      </form>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
