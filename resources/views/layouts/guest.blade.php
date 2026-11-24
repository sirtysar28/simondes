<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#0b1530">
<title>@yield('title', 'Monitoring Dana Desa') — {{ $branding['short'] ?? 'SiMONDES' }}</title>
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" href="{{ $branding['favicon'] ? asset($branding['favicon']) : asset('icons/favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="guest @if(request()->routeIs('login'))no-nav @endif">
@unless(request()->routeIs('login'))
<header class="guest-nav">
  <a class="gbrand" href="{{ route('landing') }}" data-nav>
    <div class="brand-icon">@if(!empty($branding['logo']))<img src="{{ asset($branding['logo']) }}" alt="Logo">@else<span>◈</span>@endif</div>
    <div><b>{{ $branding['short'] ?? 'SiMONDES' }}</b><small>MONITORING DANA DESA</small></div>
  </a>
  <nav>
    @auth
      <a href="{{ route('dashboard') }}">Dashboard</a>
    @else
      <a href="{{ route('landing') }}#fitur">Fitur</a>
      <a href="{{ route('landing') }}#transparansi">Transparansi</a>
      <button class="btn primary" id="pwaInstall" style="display:none">⬇ Instal Aplikasi</button>
      <a class="cta" href="{{ route('login') }}">Masuk Aplikasi →</a>
    @endauth
  </nav>
</header>
@endunless

@yield('content')

<footer class="guest-footer">
  <span>© {{ date('Y') }} {{ $branding['name'] ?? 'SiMONDES' }} — DPMD Kabupaten Halmahera Barat</span>
  <span>Developed by <a href="https://ciptakaryadigital.id" target="_blank" rel="noopener noreferrer">Cipta Karya Digital Indonesia</a></span>
</footer>

@if (session('toast'))<div class="toast show" id="toast" style="opacity:1;transform:none">{{ session('toast') }}</div>@endif
@if ($errors->any())
  @foreach ($errors->all() as $error)
    <div class="toast show" style="opacity:1;transform:none;background:#b3273f">{{ $error }}</div>
  @endforeach
@endif
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
