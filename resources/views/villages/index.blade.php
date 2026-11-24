@extends('layouts.app')
@section('title', 'Data Desa & Pagu')
@section('breadcrumb', 'Data Desa & Pagu')
@section('page_title', 'Master Data Desa')

@section('content')
<section id="section-desa">
  <div class="toolbar">
    <div>
      <h2>Master Data Desa &amp; Alokasi Anggaran</h2>
      <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:6px 0 0">Tambah, ubah, dan hapus informasi desa beserta pagu Dana Desa untuk setiap tahun anggaran.</p>
    </div>
    <div class="toolbar-right">
      <a class="btn white" href="{{ route('villages.export', ['year' => $year, 'district' => $district]) }}">↓ Ekspor Excel</a>
      <button class="btn primary" data-modal="modalVillage">＋ Tambah Data Desa</button>
    </div>
  </div>

  <div class="panel">
    <div class="toolbar">
      @include('partials.filters', ['action' => route('villages.index'), 'withSearch' => true, 'search' => $search, 'searchPlaceholder' => 'Cari nama / kode desa...'])
    </div>

    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Kode / Nama Desa</th><th>Kecamatan</th><th>Tahun</th><th>Pagu Dana Desa</th><th>Realisasi</th><th>Sisa Dana</th><th>Penyerapan</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($villages as $v)
          @php($editPayload = $v->only(['id', 'code', 'name', 'district', 'year', 'budget']))
          <tr>
            <td><div class="v-name">{{ $v->name }}</div><div class="v-code">{{ $v->code }}</div></td>
            <td>{{ $v->district }}</td>
            <td>{{ $v->year }}</td>
            <td>{{ rp($v->budget) }}</td>
            <td>{{ rp($v->spent) }}</td>
            <td>{{ rp($v->remaining) }}</td>
            <td>
              <div class="progress"><div style="width:{{ min(100, $v->absorption) }}%"></div></div>
              <div class="progress-info">{{ $v->absorption }}% terserap</div>
            </td>
            <td>
              <div class="action-group">
                <a class="btn sm white" href="{{ route('details.index', ['year' => $v->year, 'district' => 'all', 'village' => $v->id]) }}" data-nav>Rincian</a>
                <button class="btn sm soft" data-modal="modalVillageEdit" data-action="{{ route('villages.update', $v) }}" data-payload='@json($editPayload, JSON_HEX_APOS | JSON_HEX_AMP)'>Edit</button>
                <form method="POST" action="{{ route('villages.destroy', $v) }}" data-confirm="Hapus {{ $v->name }} dan seluruh transaksi terkait? Tindakan ini tidak dapat dibatalkan.">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn sm red">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="8"><div class="empty">Tidak ada data yang sesuai. Ubah filter atau tambah data.</div></td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    {{ $villages->links('partials.pagination') }}
  </div>
</section>
@endsection

@section('modals')
{{-- Modal Tambah Desa --}}
<div class="modal-wrap" id="modalVillage" role="dialog" aria-modal="true">
  <div class="modal">
    <h2>Tambah Data Desa</h2>
    <p class="desc">Lengkapi identitas desa dan pagu Dana Desa per tahun anggaran.</p>
    <form method="POST" action="{{ route('villages.store') }}">
      @csrf
      <div class="form-grid">
        <div class="form-group"><label for="v-code">Kode Desa</label><input id="v-code" name="code" required maxlength="20" placeholder="mis. HAL-001-{{ $year }}"></div>
        <div class="form-group"><label for="v-name">Nama Desa</label><input id="v-name" name="name" required maxlength="120"></div>
        <div class="form-group"><label for="v-district">Kecamatan</label>
          <select id="v-district" name="district" required>
            @foreach($districts as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label for="v-year">Tahun Anggaran</label><input id="v-year" name="year" type="number" min="2000" max="2100" value="{{ $year }}" required></div>
        <div class="form-group full"><label for="v-budget">Pagu Dana Desa (Rp)</label><input id="v-budget" name="budget" type="number" min="0" step="1" required></div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn white" data-close-modal>Batal</button>
        <button type="submit" class="btn primary">✓ Simpan Data</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit Desa (diisi otomatis dari data-payload) --}}
<div class="modal-wrap" id="modalVillageEdit" role="dialog" aria-modal="true">
  <div class="modal">
    <h2>Edit Data Desa</h2>
    <p class="desc">Perbarui identitas desa dan pagu anggaran.</p>
    <form method="POST" action="{{ route('villages.update', 0) }}" data-dynamic-action>
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="form-group"><label for="ve-code">Kode Desa</label><input id="ve-code" name="code" required maxlength="20"></div>
        <div class="form-group"><label for="ve-name">Nama Desa</label><input id="ve-name" name="name" required maxlength="120"></div>
        <div class="form-group"><label for="ve-district">Kecamatan</label><input id="ve-district" name="district" required maxlength="120"></div>
        <div class="form-group"><label for="ve-year">Tahun Anggaran</label><input id="ve-year" name="year" type="number" min="2000" max="2100" required></div>
        <div class="form-group full"><label for="ve-budget">Pagu Dana Desa (Rp)</label><input id="ve-budget" name="budget" type="number" min="0" step="1" required></div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn white" data-close-modal>Batal</button>
        <button type="submit" class="btn primary">✓ Perbarui Data</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
