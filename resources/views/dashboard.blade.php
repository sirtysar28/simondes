@extends('layouts.app')
@section('title', 'Dashboard Utama')
@section('breadcrumb', 'Dashboard Utama')
@section('page_title', 'Dashboard Monitoring Dana Desa')

@section('content')
<section>
  <div class="hero">
    <span class="plx" data-depth="0.25" style="width:290px;height:290px;right:-80px;top:-125px"></span>
    <span class="plx" data-depth="0.1" style="width:220px;height:220px;right:50px;top:20px"></span>
    <div class="hero-copy">
      <div class="eyebrow">SISTEM INFORMASI MONITORING KEUANGAN DESA</div>
      <h1>Transparansi Dana Desa,<br>Tata Kelola Berkualitas.</h1>
      <p>Pantau alokasi, realisasi penggunaan, dan sisa anggaran desa secara terpusat, akurat, dan mudah dipahami.</p>
    </div>
    <div class="hero-badge"><span>{{ $year }}</span><small>TAHUN ANGGARAN</small></div>
  </div>

  <div class="toolbar">
    <div class="toolbar-left"><h2>Ringkasan Anggaran Desa</h2></div>
    <div class="toolbar-right">
      @include('partials.filters', ['action' => route('dashboard')])
      <a class="btn primary" href="{{ route('villages.index') }}#modalVillage" data-nav>＋ Tambah Desa</a>
    </div>
  </div>

  <div class="cards">
    <div class="stat">
      <div class="stat-head"><span class="stat-title">Total Pagu Dana Desa</span><div class="stat-icon blue">▣</div></div>
      <div class="stat-value">{{ rp_compact($pagu) }}</div>
      <div class="stat-sub">Alokasi anggaran tahun terpilih</div>
    </div>
    <div class="stat">
      <div class="stat-head"><span class="stat-title">Total Realisasi Penggunaan</span><div class="stat-icon green">↗</div></div>
      <div class="stat-value">{{ rp_compact($real) }}</div>
      <div class="stat-sub"><strong>{{ pct_of($real, $pagu) }}%</strong> dari total pagu anggaran</div>
    </div>
    <div class="stat">
      <div class="stat-head"><span class="stat-title">Total Sisa Dana Desa</span><div class="stat-icon orange">◷</div></div>
      <div class="stat-value">{{ rp_compact($sisa) }}</div>
      <div class="stat-sub">Sisa pagu setelah realisasi tercatat</div>
    </div>
    <div class="stat">
      <div class="stat-head"><span class="stat-title">Desa Terpantau</span><div class="stat-icon purple">⌂</div></div>
      <div class="stat-value">{{ $villages->count() }} Desa</div>
      <div class="stat-sub"><strong>{{ $villages->pluck('district')->unique()->count() }}</strong> kecamatan dalam cakupan filter</div>
    </div>
  </div>

  <div class="layout">
    <div class="panel">
      <div class="panel-top">
        <div>
          <h3>Perbandingan Anggaran per Desa</h3>
          <div class="sub">Pagu dan realisasi penggunaan dana desa</div>
        </div>
        <div class="legend">
          <span><i class="dot" style="background:#3e6bea"></i>Pagu</span>
          <span><i class="dot" style="background:#22bed0"></i>Realisasi</span>
        </div>
      </div>
      @php
        $chartVillages = $villages->take(8);
        $barData = [
            'type' => 'bar',
            'labels' => $chartVillages->map(fn($v) => str_replace('Desa Simulasi ', 'Desa ', $v->name))->all(),
            'series' => [
                ['color' => '#3e6bea', 'values' => $chartVillages->map(fn($v) => $v->budget)->all()],
                ['color' => '#22bed0', 'values' => $chartVillages->map(fn($v) => $v->spent)->all()],
            ],
        ];
      @endphp
      <div class="chart-wrap"><canvas data-chart='{{ json_encode($barData, JSON_UNESCAPED_UNICODE) }}' role="img" aria-label="Grafik perbandingan pagu dan realisasi per desa"></canvas></div>
    </div>

    <div class="panel">
      <div class="panel-top">
        <div>
          <h3>Komposisi Penggunaan Dana</h3>
          <div class="sub">Persentase realisasi dan sisa anggaran</div>
        </div>
      </div>
      <div class="donut-area">
        <canvas data-chart='{{ json_encode(['type' => 'donut', 'value' => pct_of($real, $pagu)]) }}' role="img" aria-label="Diagram komposisi realisasi dan sisa dana"></canvas>
        <div class="donut-leg">
          <span><i class="dot" style="background:#3971e9"></i>Realisasi<b>{{ pct_of($real, $pagu) }}%</b></span>
          <span><i class="dot" style="background:#e7edf8"></i>Sisa Dana<b>{{ pct_of(max(0, $sisa), $pagu) }}%</b></span>
        </div>
      </div>
    </div>
  </div>

  <div class="layout">
    <div class="panel">
      <div class="panel-top">
        <div>
          <h3>Monitoring Anggaran Tiap Desa</h3>
          <div class="sub">Ringkasan penggunaan dan sisa dana berdasarkan transaksi tercatat</div>
        </div>
        <a class="btn soft" href="{{ route('villages.index', ['year' => $year, 'district' => $district]) }}" data-nav>Lihat Semua →</a>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead><tr><th>Desa / Kecamatan</th><th>Pagu Anggaran</th><th>Realisasi</th><th>Sisa Dana</th><th>Penyerapan</th><th>Status</th></tr></thead>
          <tbody>
          @forelse($villages->take(6) as $v)
            <tr>
              <td><div class="v-name">{{ $v->name }}</div><div class="v-code">{{ $v->district }}</div></td>
              <td>{{ rp($v->budget) }}</td>
              <td>{{ rp($v->spent) }}</td>
              <td>{{ rp($v->remaining) }}</td>
              <td>
                <div class="progress"><div style="width:{{ min(100, $v->absorption) }}%"></div></div>
                <div class="progress-info">{{ $v->absorption }}% terserap</div>
              </td>
              <td><span class="tag {{ $v->status[1] }}">{{ $v->status[0] }}</span></td>
            </tr>
          @empty
            <tr><td colspan="6"><div class="empty">Tidak ada data. Ubah filter atau tambahkan desa baru.</div></td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <div class="panel-top">
        <div>
          <h3>Aktivitas Realisasi Terbaru</h3>
          <div class="sub">Pencatatan penggunaan anggaran desa</div>
        </div>
      </div>
      <div class="activity-list">
        @forelse($recent as $t)
          <div class="activity">
            <span class="activity-icon">↗</span>
            <div>
              <strong>{{ $t->village?->name ?? 'Desa dihapus' }}</strong>
              <p>{{ $t->category }} • {{ $t->date?->format('d M Y') }}</p>
            </div>
            <div class="activity-value">{{ rp_compact($t->amount) }}</div>
          </div>
        @empty
          <div class="empty">Belum ada transaksi.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="footnote">ⓘ Nilai realisasi dihitung dari transaksi penggunaan yang dicatat pada sistem ini. Sisa = pagu − realisasi.</div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
