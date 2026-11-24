@extends('layouts.app')
@section('title', 'Rincian per Desa')
@section('breadcrumb', 'Rincian per Desa')
@section('page_title', 'Rincian Keuangan Desa')

@section('content')
<section id="section-rincian">
  <div class="toolbar">
    <div>
      <h2>Rincian Penggunaan Dana per Desa</h2>
      <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:6px 0 0">Telusuri setiap pengeluaran, jenis belanja, penerima, bukti, serta sisa anggaran per desa.</p>
    </div>
    <div class="toolbar-right">
      @include('partials.filters', ['action' => route('details.index'), 'extraFilters' => null])
    </div>
  </div>

  <div class="toolbar">
    <form method="GET" action="{{ route('details.index') }}" class="toolbar-left" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <input type="hidden" name="year" value="{{ $year }}">
      <input type="hidden" name="district" value="{{ $district }}">
      <select class="field auto-submit" name="village" style="min-width:240px" aria-label="Pilih desa">
        @foreach($villages as $v)
          <option value="{{ $v->id }}" {{ $selected?->id === $v->id ? 'selected' : '' }}>{{ $v->name }} • {{ $v->district }}</option>
        @endforeach
      </select>
      @if($selected)
        <a class="btn white" href="{{ route('details.export.excel', [$selected, 'year' => $year, 'district' => $district]) }}">↓ Excel Desa</a>
        <a class="btn primary" href="{{ route('details.export.pdf', [$selected, 'year' => $year, 'district' => $district]) }}">↓ PDF Desa</a>
      @endif
    </form>
    <div class="toolbar-right">
      <a class="btn soft" href="{{ route('transactions.index', ['year' => $year]) }}#modalTx" data-nav>＋ Pengeluaran</a>
    </div>
  </div>

  <div class="cards">
    <div class="stat"><div class="stat-title">Pagu Desa</div><div class="stat-value">{{ rp($selected->budget ?? 0) }}</div></div>
    <div class="stat"><div class="stat-title">Total Pengeluaran</div><div class="stat-value">{{ rp($selected->spent ?? 0) }}</div></div>
    <div class="stat"><div class="stat-title">Sisa Anggaran</div><div class="stat-value">{{ rp($selected->remaining ?? 0) }}</div></div>
    <div class="stat"><div class="stat-title">Jumlah Transaksi</div><div class="stat-value">{{ $transactions->count() }} Transaksi</div></div>
  </div>

  <div class="layout">
    <div class="panel">
      <h3>Pengeluaran Berdasarkan Bidang</h3>
      @php
        $catChart = [
            'type' => 'bar',
            'labels' => $byCategory->map(fn($c) => \Illuminate\Support\Str::of($c['category'])->replace('Penyelenggaraan ', '')->replace('Penanggulangan ', '')->replace(' Kemasyarakatan', '')->limit(14))->all(),
            'series' => [['color' => '#22bfd1', 'values' => $byCategory->map(fn($c) => $c['total'])->all()]],
        ];
      @endphp
      <div class="chart-wrap"><canvas data-chart='{{ json_encode($catChart, JSON_UNESCAPED_UNICODE) }}'></canvas></div>
    </div>
    <div class="panel">
      <h3>Kelengkapan Dokumen</h3>
      <div class="activity-list">
        <div class="activity"><span class="activity-icon">▣</span><div><strong>Transaksi tercatat</strong><p>{{ $transactions->count() }} transaksi terdaftar</p></div></div>
        <div class="activity"><span class="activity-icon" style="background:#e9faf4;color:#0d9c74">✓</span><div><strong>Dokumen lengkap</strong><p>{{ $completeDocs }} transaksi memiliki nomor bukti dan tautan dokumen</p></div></div>
        <div class="activity"><span class="activity-icon" style="background:#fff6e9;color:#c9841d">!</span><div><strong>Perlu dilengkapi</strong><p>{{ $missingDocs }} transaksi belum memiliki bukti lengkap</p></div></div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-top">
      <div><h3>Buku Pengeluaran Desa — {{ $selected?->name ?? '-' }}</h3><div class="sub">Laporan dan saldo berubah setiap ada transaksi.</div></div>
    </div>
    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Tanggal</th><th>Uraian / Jenis Belanja</th><th>Bidang</th><th>Sumber</th><th>Penerima</th><th>No. Bukti</th><th>Jumlah</th><th>Dokumen</th></tr></thead>
        <tbody>
        @forelse($transactions as $t)
          <tr>
            <td>{{ $t->date?->format('d/m/Y') }}</td>
            <td><b>{{ $t->description }}</b><div class="v-code">{{ $t->spending_type ?? 'Belum diklasifikasi' }}</div></td>
            <td>{{ $t->category }}</td>
            <td>{{ $t->funding_source ?? 'Dana Desa (DD)' }}</td>
            <td>{{ $t->vendor ?? '—' }}</td>
            <td>{{ $t->receipt_number ?? '—' }}</td>
            <td><b>{{ rp($t->amount) }}</b></td>
            <td>
              @if($t->evidence_url)
                <a href="{{ $t->evidence_url }}" target="_blank" rel="noopener noreferrer nofollow">Lihat Bukti ↗</a>
              @else
                <span class="tag mid">Belum ada</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8"><div class="empty">Belum ada pengeluaran untuk desa ini.</div></td></tr>
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
