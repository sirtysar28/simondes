<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#0b1530">
<title>@yield('title', 'Dashboard') — {{ $branding['short'] ?? 'SiMONDES' }}</title>
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" href="{{ $branding['favicon'] ? asset($branding['favicon']) : asset('icons/favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-icon">
        @if(!empty($branding['logo']))<img src="{{ asset($branding['logo']) }}" alt="Logo instansi">@else<span>◈</span>@endif
      </div>
      <div>
        <strong>{{ $branding['short'] ?? 'SiMONDES' }}</strong>
        <small>MONITORING DANA DESA</small>
      </div>
    </div>

    <div class="nav-label">MENU UTAMA</div>
    <a class="navbtn {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
      Dashboard Utama
    </a>
    <a class="navbtn {{ request()->routeIs('villages.*') ? 'active' : '' }}" href="{{ route('villages.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 5h18M3 12h18M3 19h18"/></svg>
      Data Desa &amp; Pagu
    </a>
    <a class="navbtn {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l6-6 4 4 8-8"/><path d="M21 7v6h-6"/></svg>
      Realisasi Penggunaan
    </a>
    <a class="navbtn {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
      Laporan &amp; Analisis
    </a>
    <a class="navbtn {{ request()->routeIs('details.*') ? 'active' : '' }}" href="{{ route('details.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg>
      Rincian per Desa
    </a>

    <div class="nav-label">PENGELOLAAN</div>
    <a class="navbtn {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.7 1.7 0 0 0 .34-1.87 1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.7 1.7 0 0 0 1.87.34h.09a1.7 1.7 0 0 0 1-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55h.09a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.7 1.7 0 0 0-.34 1.87v.09a1.7 1.7 0 0 0 1.55 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.55 1z"/></svg>
      Pengaturan
    </a>
    <a class="navbtn {{ request()->routeIs('logs.*') ? 'active' : '' }}" href="{{ route('logs.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
      Log Aktivitas
    </a>
    <a class="navbtn {{ request()->routeIs('terminal.*') ? 'active' : '' }}" href="{{ route('terminal.index') }}" data-nav>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 17l5-5-5-5M12 19h8"/></svg>
      Terminal Artisan
    </a>

    <div class="side-foot">
      <b>DPMD • KABUPATEN HALMAHERA BARAT</b><br>
      Sistem informasi monitoring dan evaluasi dana desa.
    </div>
  </aside>

  <main class="main">
    <header class="topbar">
      <div style="display:flex;align-items:center;gap:12px">
        <button class="btn white mobile-top" id="menuBtn" aria-label="Menu">☰</button>
        <div>
          <div class="breadcrumbs">DPMD / @yield('breadcrumb', 'Dashboard')</div>
          <div class="page-heading">@yield('page_title', 'Dashboard Monitoring Dana Desa')</div>
        </div>
      </div>
      <div class="top-right">
        <span class="pill">Pembaruan Instan</span>
        <div class="user-menu">
          <a href="{{ route('settings.index') }}" class="avatar" id="userBtn" title="{{ auth()->user()->name }}">
            {{ strtoupper(mb_substr(auth()->user()->name, 0, 2)) }}
          </a>
          <div class="user-drop" id="userDrop">
            <header>
              <b>{{ auth()->user()->name }}</b>
              <span>{{ auth()->user()->email }}</span>
            </header>
            <a href="{{ route('settings.index') }}">⚙ Pengaturan Aplikasi</a>
            <a href="{{ route('settings.index') }}#keamanan">🔑 Ganti Password</a>
            <a href="{{ route('logs.index') }}">🕘 Log Aktivitas Saya</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="danger">⏻ Keluar (Logout)</button>
            </form>
          </div>
        </div>
      </div>
    </header>

    <div class="content">
      @if ($errors->any())
        <div class="validation">
          <b>Periksa kembali isian Anda:</b>
          <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif

      @yield('content')

      <footer class="app-footer">
        <span>© {{ date('Y') }} {{ $branding['name'] ?? 'SiMONDES' }} — DPMD Kabupaten Halmahera Barat. Semua hak dilindungi.</span>
        <span>Developed by <a href="https://ciptakaryadigital.id" target="_blank" rel="noopener noreferrer">Cipta Karya Digital Indonesia</a></span>
      </footer>
    </div>
  </main>
</div>

@yield('modals')

@if (session('toast'))
  <div class="toast show" id="toast" style="opacity:1;transform:none">{{ session('toast') }}</div>
@endif

<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
