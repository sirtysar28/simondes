@extends('layouts.app')
@section('title', 'Realisasi Penggunaan')
@section('breadcrumb', 'Realisasi Penggunaan')
@section('page_title', 'Realisasi Dana Desa')

@section('content')
<section id="section-realisasi">
  <div class="toolbar">
    <div>
      <h2>Realisasi Penggunaan Dana Desa</h2>
      <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:6px 0 0">Catat penggunaan dana: kegiatan pembangunan, pemberdayaan, dan operasional desa.</p>
    </div>
    <div class="toolbar-right">
      <a class="btn white" href="{{ route('transactions.export', ['year' => $year, 'district' => $district]) }}">↓ Ekspor Excel</a>
      <button class="btn primary" data-modal="modalTx">＋ Catat Realisasi</button>
    </div>
  </div>

  <div class="kpi-row">
    <div class="mini-kpi"><span>Total Transaksi</span><b>{{ number_format($totals->c) }}</b></div>
    <div class="mini-kpi"><span>Nominal Terealisasi</span><b>{{ rp_compact($totals->s) }}</b></div>
    <div class="mini-kpi"><span>Desa dengan Transaksi</span><b>{{ number_format($totals->v) }}</b></div>
  </div>

  <div class="panel">
    <div class="toolbar">
      @include('partials.filters', ['action' => route('transactions.index'), 'withSearch' => true, 'search' => $search, 'searchPlaceholder' => 'Cari kegiatan / desa / no. bukti...'])
    </div>

    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Tanggal</th><th>Desa</th><th>Bidang Kegiatan</th><th>Uraian / Keterangan</th><th>Jenis Belanja</th><th>Sumber Dana</th><th>Nomor Bukti</th><th>Jumlah Realisasi</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($rows as $t)
          @php($editPayload = $t->only(['village_id', 'date', 'category', 'spending_type', 'funding_source', 'description', 'amount', 'vendor', 'receipt_number', 'evidence_url']))
          <tr>
            <td>{{ $t->date?->format('d/m/Y') }}</td>
            <td><div class="v-name">{{ $t->village?->name ?? '—' }}</div><div class="v-code">{{ $t->village?->district }}</div></td>
            <td>{{ $t->category }}</td>
            <td>{{ $t->description }}</td>
            <td>{{ $t->spending_type }}</td>
            <td>{{ $t->funding_source }}</td>
            <td>{{ $t->receipt_number ?? '—' }}</td>
            <td><b>{{ rp($t->amount) }}</b></td>
            <td>
              <div class="action-group">
                <button class="btn sm soft" data-modal="modalTxEdit" data-action="{{ route('transactions.update', $t) }}" data-payload='@json($editPayload, JSON_HEX_APOS | JSON_HEX_AMP)'>Edit</button>
                <form method="POST" action="{{ route('transactions.destroy', $t) }}" data-confirm="Hapus catatan realisasi ini?">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn sm red">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="9"><div class="empty">Belum ada transaksi realisasi pada filter ini.</div></td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    {{ $rows->links('partials.pagination') }}
  </div>
</section>
@endsection

@section('modals')
{{-- Form transaksi bersama untuk Tambah/Edit --}}
<div class="modal-wrap" id="modalTx" role="dialog" aria-modal="true">
  <div class="modal wide">
    <h2>Catat Realisasi Anggaran</h2>
    <p class="desc">Pilih desa dan masukkan rincian transaksi realisasi penggunaan dana.</p>
    <form method="POST" action="{{ route('transactions.store') }}">
      @csrf
      @include('transactions._fields', ['villages' => $villages])
      <div class="modal-actions">
        <button type="button" class="btn white" data-close-modal>Batal</button>
        <button type="submit" class="btn primary">✓ Simpan Transaksi</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-wrap" id="modalTxEdit" role="dialog" aria-modal="true">
  <div class="modal wide">
    <h2>Edit Realisasi Anggaran</h2>
    <p class="desc">Perbarui rincian transaksi. Total realisasi tetap dibatasi pagu desa.</p>
    <form method="POST" action="{{ route('transactions.update', 0) }}" data-dynamic-action>
      @csrf
      @method('PUT')
      @include('transactions._fields', ['villages' => $villages])
      <div class="modal-actions">
        <button type="button" class="btn white" data-close-modal>Batal</button>
        <button type="submit" class="btn primary">✓ Perbarui Transaksi</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
