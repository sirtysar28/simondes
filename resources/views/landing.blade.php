@extends('layouts.guest')
@section('title', 'Beranda')

@section('content')
<main style="flex:1">
  {{-- HERO dengan parallax orbs --}}
  <section class="landing-hero">
    <span class="plx" data-depth="0.35" style="position:absolute;width:340px;height:340px;border-radius:50%;border:1px solid #ffffff26;left:-90px;top:-120px"></span>
    <span class="plx" data-depth="0.22" style="position:absolute;width:220px;height:220px;border-radius:50%;border:1px solid #ffffff1f;right:8%;top:6%"></span>
    <span class="plx" data-depth="0.5" style="position:absolute;width:150px;height:150px;border-radius:50%;background:#20b7dc17;left:14%;bottom:8%"></span>
    <span class="plx" data-depth="0.14" style="position:absolute;width:90px;height:90px;border-radius:50%;background:#3264e922;right:18%;bottom:22%"></span>

    <div style="position:relative;z-index:1">
      <div class="eyebrow" style="font-size:11px;letter-spacing:1.6px;color:#8fd4f2;font-weight:800">DPMD • KABUPATEN HALMAHERA BARAT</div>
      <h1>Transparansi Dana Desa,<br><span>Tata Kelola Berkualitas.</span></h1>
      <p>{{ $branding['name'] ?? 'SiMONDES' }} memantau alokasi, realisasi penggunaan, dan sisa anggaran desa di seluruh kecamatan Halmahera Barat secara terpusat, akurat, dan mudah dipahami — Tahun Anggaran {{ $year }}.</p>
      @guest
        <a class="btn primary" href="{{ route('login') }}" style="height:48px;padding:0 30px;font-size:13.5px">Masuk ke Dashboard →</a>
        <div style="margin-top:14px;font-size:11px;color:#7e94c4">Butuh akses? Hubungi admin DPMD.</div>
      @else
        <a class="btn primary" href="{{ route('dashboard') }}" style="height:48px;padding:0 30px;font-size:13.5px">Buka Dashboard →</a>
      @endguest
    </div>

    <div class="hero-stats" style="position:relative;z-index:1">
      <div class="hs"><b>{{ rp_compact($pagu) }}</b><span>TOTAL PAGU DANA DESA</span></div>
      <div class="hs"><b>{{ rp_compact($real) }}</b><span>REALISASI PENGGUNAAN</span></div>
      <div class="hs"><b>{{ $totalDesa }} Desa</b><span>DESA TERPANTAU</span></div>
      <div class="hs"><b>{{ $totalKecamatan }} Kec.</b><span>KECAMATAN HALBAR</span></div>
    </div>
  </section>

  {{-- FITUR --}}
  <section class="landing-section" id="fitur">
    <h2>Satu Aplikasi, Seluruh Siklus Dana Desa</h2>
    <p>Dirancang mengikuti kebutuhan monitoring DPMD — dari alokasi pagu hingga laporan realisasi per desa yang siap diekspor.</p>
    <div class="feat-grid">
      <div class="feat"><div class="fi" style="background:#ecf2ff;color:#3264e9">▣</div><h3>Pagu &amp; Alokasi Terpusat</h3><p>Kelola pagu Dana Desa (DD) dan ADD setiap desa per tahun anggaran dengan validasi ganda agar anggaran tidak melebihi pagu.</p></div>
      <div class="feat"><div class="fi" style="background:#e9faf4;color:#0eaf87">↗</div><h3>Pencatatan Realisasi</h3><p>Catat setiap penggunaan dana lengkap dengan bidang kegiatan, jenis belanja, sumber dana, penerima, dan nomor bukti SPJ.</p></div>
      <div class="feat"><div class="fi" style="background:#fff6e9;color:#f3a13e">▥</div><h3>Laporan Excel &amp; PDF</h3><p>Rekapitulasi penyerapan per kecamatan dan desa siap unduh dalam format Excel maupun PDF untuk pelaporan resmi.</p></div>
      <div class="feat"><div class="fi" style="background:#f2eeff;color:#8561d9">⌂</div><h3>Rincian per Desa</h3><p>Telusuri buku pengeluaran tiap desa, kelengkapan dokumen bukti, hingga cetak laporan siap tanda tangan.</p></div>
      <div class="feat"><div class="fi" style="background:#eaf6ff;color:#3675b6">🕘</div><h3>Log Aktivitas</h3><p>Setiap perubahan data tercatat lengkap: siapa, kapan, dari alamat IP apa — mendukung akuntabilitas pengelola.</p></div>
      <div class="feat"><div class="fi" style="background:#fcebef;color:#de5669">📱</div><h3>PWA &amp; Mobile Ready</h3><p>Instal seperti aplikasi native di HP, tampil penuh di layar kecil, dan tetap dapat diakses saat koneksi terputus.</p></div>
    </div>
  </section>

  {{-- TRANSPARANSI: penyerapan per kecamatan --}}
  <section class="landing-section" id="transparansi" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
    <h2>Penyerapan per Kecamatan — TA {{ $year }}</h2>
    <p>Ringkasan publik pagu vs realisasi berdasarkan data yang tercatat pada sistem ({{ number_format($countTx) }} transaksi, serapan {{ $absorption }}%).</p>
    <div class="district-list">
      @forelse($byDistrict as $d)
        <div class="district-row">
          <b>{{ $d['district'] }}</b>
          <div>
            <div class="progress"><div style="width:{{ min(100, pct_of($d['real'], $d['pagu'])) }}%"></div></div>
            <div class="progress-info">{{ rp_compact($d['real']) }} dari {{ rp_compact($d['pagu']) }} pagu</div>
          </div>
          <div class="pct">{{ pct_of($d['real'], $d['pagu']) }}%</div>
        </div>
      @empty
        <div class="empty">Belum ada data TA {{ $year }} yang dipublikasikan.</div>
      @endforelse
    </div>

    <div class="panel" style="max-width:900px;margin:34px auto 0">
      <div class="panel-top"><h3>Desa dengan Realisasi Tertinggi</h3></div>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>Desa</th><th>Kecamatan</th><th>Pagu</th><th>Realisasi</th><th>Serapan</th></tr></thead>
          <tbody>
          @forelse($top as $v)
            <tr>
              <td><div class="v-name">{{ $v->name }}</div></td>
              <td>{{ $v->district }}</td>
              <td>{{ rp($v->budget) }}</td>
              <td>{{ rp($v->spent) }}</td>
              <td><span class="tag {{ $v->status[1] }}">{{ $v->status[0] }} • {{ $v->absorption }}%</span></td>
            </tr>
          @empty
            <tr><td colspan="5"><div class="empty">Belum ada data.</div></td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <section class="cta-band">
    <h2>Siap memantau dana desa secara digital?</h2>
    <p>Masuk untuk mengelola data, mencatat realisasi, dan mengunduh laporan resmi dalam sekali klik.</p>
    <a class="btn white" href="{{ route('login') }}" style="height:46px;padding:0 28px;font-size:13px;color:#1c3faf">Login Administrator →</a>
  </section>
</main>
@endsection
