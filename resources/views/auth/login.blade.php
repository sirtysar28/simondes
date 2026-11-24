@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="login-wrap">
  {{-- ===== PANEL KIRI: FORM LOGIN ===== --}}
  <section class="login-left">
    <span class="plx" data-depth="0.3" style="position:absolute;width:260px;height:260px;border-radius:50%;border:1px solid #ffffff14;left:-90px;bottom:-60px"></span>
    <div style="position:relative;z-index:1;width:100%">
      <div class="brand" style="padding:0;border:none;margin-bottom:8px">
        <div class="brand-icon">@if(!empty($branding['logo']))<img src="{{ asset($branding['logo']) }}" alt="Logo">@else<span>◈</span>@endif</div>
        <div><strong style="font-size:16px">{{ $branding['short'] ?? 'SiMONDES' }}</strong><small>MONITORING DANA DESA</small></div>
      </div>

      <h1>Selamat Datang 👋</h1>
      <p class="sub">Masuk menggunakan akun resmi DPMD Kabupaten Halmahera Barat untuk mengakses dashboard monitoring dana desa.</p>

      @if ($errors->any())
        <div class="validation" style="background:#3a1a29;border-color:#7a2c3f;color:#ffb9c6;margin-bottom:16px">
          @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
      @endif

      <form class="login-form" method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@halbarkab.go.id">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••••">
        </div>
        <div class="form-group">
          <label for="captcha">Kode Keamanan (5 karakter)</label>
          <div class="captcha-row">
            <img src="{{ route('captcha') }}?t={{ number_format(microtime(true) * 10000, 0, '', '') }}" alt="Kode captcha" width="220" height="64" id="captchaImg" data-captcha-refresh>
            <input id="captcha" type="text" name="captcha" maxlength="5" required autocomplete="off" placeholder="ABCDE" style="text-transform:uppercase;letter-spacing:.35em;font-weight:800">
          </div>
          <div class="captcha-hint">Klik gambar untuk mengganti kode</div>
        </div>
        <div class="remember">
          <label style="display:flex;gap:8px;align-items:center;font-weight:500">
            <input type="checkbox" name="remember" value="1" style="width:auto;accent-color:#3264e9"> Ingat saya
          </label>
          <a href="https://ciptakaryadigital.id" target="_blank" rel="noopener noreferrer">Butuh bantuan?</a>
        </div>
        <button type="submit" class="btn primary" style="width:100%;height:46px;font-size:13.5px">🔓 Masuk Sekarang</button>
      </form>

      <div style="margin-top:26px;font-size:10.5px;color:#6d81ad;line-height:1.8">
        🔒 Koneksi terenkripsi • Percobaan login dibatasi &amp; tercatat pada log aktivitas.<br>
        © {{ date('Y') }} Developed by <a href="https://ciptakaryadigital.id" target="_blank" rel="noopener noreferrer" style="color:#7fb2ff">Cipta Karya Digital Indonesia</a>
      </div>
    </div>
  </section>

  {{-- ===== PANEL KANAN: ILUSTRASI ===== --}}
  <section class="login-right">
    <span class="plx" data-depth="0.25" style="position:absolute;width:320px;height:320px;border-radius:50%;background:#3264e912;top:8%;left:12%"></span>
    <span class="plx" data-depth="0.4" style="position:absolute;width:180px;height:180px;border-radius:50%;background:#20b7dc1c;bottom:14%;right:16%"></span>
    <span class="plx" data-depth="0.15" style="position:absolute;width:520px;height:520px;border-radius:50%;border:1px solid #c9d8f5;bottom:-200px;left:-140px"></span>

    <div class="login-art">
      <div class="art-card">
        <div style="font-size:11px;font-weight:800;color:#8492aa;margin-bottom:14px">REALISASI DANA DESA — TA {{ date('Y') }}</div>
        <div class="mini-bars">
          @foreach([38,62,47,78,55,86,40,69] as $i => $h)
            <i style="height:{{ $h }}%;animation-delay:{{ $i * 0.07 }}s"></i>
          @endforeach
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:12px;font-size:10px;color:#97a4bb">
          <span>8 Kecamatan</span><span style="color:#12a882;font-weight:800">▲ Serapan sehat</span>
        </div>
      </div>
      <div class="art-card" style="display:flex;gap:14px;align-items:center">
        <div class="stat-icon green" style="width:44px;height:44px;font-size:20px">✓</div>
        <div style="flex:1">
          <b style="font-size:13px">Transparansi &amp; Akuntabilitas</b>
          <div style="font-size:11px;color:#8b99b1;margin-top:4px">Setiap rupiah tercatat, setiap aksi terlacak.</div>
        </div>
      </div>
      <p class="login-quote">“Alokasi, realisasi, dan sisa anggaran desa dalam satu genggaman — akurat dan mudah dipahami.”</p>
    </div>
  </section>
</div>
@endsection
