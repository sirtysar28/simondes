<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pemasangan — SiMONDES</title>
<style>
body{margin:0;min-height:100vh;background:linear-gradient(115deg,#0c1733,#1d3e93);display:grid;place-items:center;padding:24px;font-family:Inter,system-ui,-apple-system,'Segoe UI',sans-serif;color:#172443}
.card{background:#fff;border-radius:18px;box-shadow:0 30px 80px rgba(5,20,54,.4);max-width:640px;width:100%;padding:34px;max-height:92vh;overflow:auto}
.ic{width:56px;height:56px;border-radius:16px;display:grid;place-items:center;font-size:26px;color:#fff;background:linear-gradient(145deg,#46cde6,#3567eb);margin-bottom:18px}
h1{font-size:21px;margin:0 0 6px}
.sub{color:#8090a9;font-size:12.5px;line-height:1.7;margin:0 0 22px}
table{width:100%;border-collapse:collapse;margin-bottom:22px}
th,td{text-align:left;font-size:12px;padding:9px 10px;border-bottom:1px solid #eef2f8}
th{color:#8492aa;font-size:10px;text-transform:uppercase;letter-spacing:.5px}
.ok{color:#0d9c74;font-weight:800}.bad{color:#d13c53;font-weight:800}
.key{background:#eef4ff;border:1px dashed #9db8f2;border-radius:10px;padding:13px 15px;font-family:ui-monospace,Menlo,monospace;font-size:12px;word-break:break-all;margin:0 0 14px;color:#274a9e}
label{font-size:11px;font-weight:750;color:#63738d;display:block;margin-bottom:6px}
input[type=text]{width:100%;border:1px solid #dfe6f2;background:#fbfcff;border-radius:9px;padding:12px;outline:none;font-family:ui-monospace,Menlo,monospace;font-size:12px}
input[type=text]:focus{border-color:#5176e6}
.btn{border:none;border-radius:10px;height:44px;padding:0 22px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:750;cursor:pointer;background:#3366e8;color:#fff}
.btn:hover{background:#2556d3}
.err{background:#fff0f2;border:1px solid #ffd9df;color:#c0384b;border-radius:10px;padding:11px 14px;font-size:12px;margin-bottom:16px}
.log{background:#0a1120;color:#c6d6f2;border-radius:12px;padding:18px;font-family:ui-monospace,Menlo,monospace;font-size:12px;line-height:1.8;white-space:pre-wrap;word-break:break-word;margin:0 0 18px;max-height:340px;overflow:auto}
.log .g{color:#39d5a0}.log .r{color:#ff7b8a}
a{color:#3366e8}
</style>
</head>
<body>
<div class="card">
  <div class="ic">◈</div>

  @if($mode === 'already')
    <h1>Aplikasi Sudah Terpasang ✅</h1>
    <p class="sub">Installer dinonaktifkan demi keamanan (file <code>storage/app/installed.lock</code> aktif). Untuk pemeliharaan artisan, gunakan menu <b>Terminal</b> di dalam aplikasi setelah login.</p>
    <a class="btn" href="/">Ke Halaman Utama →</a>

  @elseif($mode === 'result')
    <h1>{{ $success ? 'Pemasangan Selesai 🎉' : 'Pemasangan Gagal' }}</h1>
    <p class="sub">{{ $success ? 'Tabel database, akun admin, dan data demo sudah dibuat. Silakan login dengan kredensial di bawah.' : 'Periksa log berikut, perbaiki konfigurasi .env, lalu ulangi.' }}</p>
    <div class="log">{!! implode("\n", array_map(fn($l) => str_starts_with($l,'[OK]') ? '<span class="g">'.$l.'</span>' : (str_starts_with($l,'[GAGAL]') ? '<span class="r">'.$l.'</span>' : $l), $log)) !!}</div>
    @if($success)
      <table>
        <tr><th>Login</th><td>admin@simondes.id</td></tr>
        <tr><th>Password</th><td>Admin#2026</td></tr>
      </table>
      <p class="sub">⚠ Segera ganti password melalui Pengaturan → Keamanan.</p>
      <a class="btn" href="/login">Masuk Sekarang →</a>
    @else
      <a class="btn" href="/install">← Kembali</a>
    @endif

  @else
    <h1>Pemasangan SiMONDES</h1>
    <p class="sub">Mode khusus hosting <b>tanpa SSH/terminal</b> (cPanel): cukup <code>composer install</code>, atur <code>.env</code>, lalu jalankan halaman ini — migrasi database, akun admin, dan data demo dibuat otomatis.</p>

    @if ($errors->any())
      <div class="err">@foreach ($errors->all() as $e){{ $e }}@endforeach</div>
    @endif

    <table>
      <tr><th>Pemeriksaan</th><th>Status</th><th>Keterangan</th></tr>
      @foreach($checks as [$label, $ok, $note])
        <tr>
          <td>{{ $label }}</td>
          <td class="{{ $ok ? 'ok' : 'bad' }}">{{ $ok ? '✓ OK' : '✗ Gagal' }}</td>
          <td style="color:#8090a9">{{ $note }}</td>
        </tr>
      @endforeach
    </table>

    <p class="sub">Masukkan <b>kunci instalasi</b> di bawah — kunci ini juga tersimpan di berkas <code>.env</code> Anda (<code>INSTALL_KEY=</code>…), sehingga hanya pemilik server yang dapat memasang:</p>
    <div class="key">{{ $installKey }}</div>

    <form method="POST" action="/install">
      @csrf
      <label for="install_key">Kunci Instalasi</label>
      <input id="install_key" type="text" name="install_key" value="{{ $installKey }}" required>
      <div style="margin-top:18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <button type="submit" class="btn">▶ Jalankan Pemasangan</button>
        <span style="font-size:11px;color:#97a4bb">± 5 detik. Jangan muat ulang saat berjalan.</span>
      </div>
    </form>
  @endif
</div>
</body>
</html>
