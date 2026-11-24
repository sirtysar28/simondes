@extends('layouts.app')
@section('title', 'Laporan & Analisis')
@section('breadcrumb', 'Laporan & Analisis')
@section('page_title', 'Laporan Keuangan Desa')

@section('content')
<section id="section-laporan">
  <div class="toolbar">
    <div>
      <h2>Laporan &amp; Analisis Keuangan Desa</h2>
      <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:6px 0 0">Ringkasan kinerja penyerapan anggaran menurut kecamatan dan desa — TA {{ $year }}.</p>
    </div>
    <div class="toolbar-right">
      @include('partials.filters', ['action' => route('reports.index')])
      <a class="btn white" href="{{ route('reports.export.excel', ['year' => $year, 'district' => $district]) }}">↓ Excel</a>
      <a class="btn primary" href="{{ route('reports.export.pdf', ['year' => $year, 'district' => $district]) }}">↓ PDF</a>
    </div>
  </div>

  <div class="analytics-row" style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
    <div class="panel">
      <div class="panel-top">
        <div><h3>Penyerapan Menurut Kecamatan</h3><div class="sub">Perbandingan total pagu dengan realisasi</div></div>
      </div>
      @php
        $districtChart = [
            'type' => 'bar',
            'labels' => $districtChart->map(fn($d) => $d['district'])->all(),
            'series' => [
                ['color' => '#3e6bea', 'values' => $districtChart->map(fn($d) => $d['pagu'])->all()],
                ['color' => '#22bed0', 'values' => $districtChart->map(fn($d) => $d['real'])->all()],
            ],
        ];
      @endphp
      <div class="chart-wrap"><canvas data-chart='{{ json_encode($districtChart, JSON_UNESCAPED_UNICODE) }}'></canvas></div>
    </div>
    <div class="panel">
      <div class="panel-top">
        <div><h3>Realisasi Berdasarkan Bidang</h3><div class="sub">Distribusi transaksi yang sudah dicatat</div></div>
      </div>
      @php
        $categoryChart = [
            'type' => 'bar',
            'labels' => $categoryChart->map(fn($c) => \Illuminate\Support\Str::of($c['category'])->replace('Penyelenggaraan ', '')->replace('Penanggulangan ', '')->replace(' Kemasyarakatan', ''))->all(),
            'series' => [['color' => '#8561d9', 'values' => $categoryChart->map(fn($c) => $c['total'])->all()]],
        ];
      @endphp
      <div class="chart-wrap"><canvas data-chart='{{ json_encode($categoryChart, JSON_UNESCAPED_UNICODE) }}'></canvas></div>
    </div>
  </div>

  <div class="panel" style="margin-top:18px">
    <div class="panel-top">
      <h3>Rekapitulasi Anggaran Desa — TA {{ $year }}</h3>
      <div class="sub">Total pagu {{ rp($pagu) }} • realisasi {{ rp($real) }} ({{ pct_of($real, $pagu) }}%)</div>
    </div>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Desa</th><th>Kecamatan</th><th>Pagu</th><th>Realisasi</th><th>Sisa</th><th>Persentase</th><th>Evaluasi</th></tr></thead>
        <tbody>
        @forelse($villages as $v)
          <tr>
            <td><b>{{ $v->name }}</b></td>
            <td>{{ $v->district }}</td>
            <td>{{ rp($v->budget) }}</td>
            <td>{{ rp($v->spent) }}</td>
            <td>{{ rp($v->remaining) }}</td>
            <td>{{ $v->absorption }}%</td>
            <td><span class="tag {{ $v->status[1] }}">{{ $v->status[0] }}</span></td>
          </tr>
        @empty
          <tr><td colspan="7"><div class="empty">Tidak ada data pada filter ini.</div></td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
