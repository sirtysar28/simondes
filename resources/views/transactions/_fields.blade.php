<div class="form-grid">
  <div class="form-group">
    <label for="tx-village">Desa / Tahun Anggaran</label>
    <select id="tx-village" name="village_id" required>
      @forelse($villages as $v)
        <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->year }}) — {{ $v->district }}</option>
      @empty
        <option value="">Tidak ada desa pada filter ini</option>
      @endforelse
    </select>
  </div>
  <div class="form-group"><label for="tx-date">Tanggal Realisasi</label><input id="tx-date" name="date" type="date" max="{{ now()->toDateString() }}" required></div>
  <div class="form-group">
    <label for="tx-category">Bidang Kegiatan</label>
    <select id="tx-category" name="category" required>
      @foreach($categories as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
    </select>
  </div>
  <div class="form-group"><label for="tx-amount">Nominal (Rp)</label><input id="tx-amount" name="amount" type="number" min="1" step="1" required></div>
  <div class="form-group full"><label for="tx-description">Uraian Kegiatan</label><textarea id="tx-description" name="description" required maxlength="500"></textarea></div>
  <div class="form-group">
    <label for="tx-type">Jenis Belanja</label>
    <select id="tx-type" name="spending_type" required>
      @foreach($spendingTypes as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="tx-source">Sumber Pendanaan</label>
    <select id="tx-source" name="funding_source" required>
      @foreach($fundingSources as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
    </select>
  </div>
  <div class="form-group"><label for="tx-vendor">Penerima / Penyedia (opsional)</label><input id="tx-vendor" name="vendor" maxlength="150"></div>
  <div class="form-group"><label for="tx-receipt">Nomor Bukti / SPJ (opsional)</label><input id="tx-receipt" name="receipt_number" maxlength="100"></div>
  <div class="form-group full"><label for="tx-evidence">Tautan Dokumen Bukti (harus https:// — opsional)</label><input id="tx-evidence" name="evidence_url" type="url" placeholder="https://..." maxlength="500"></div>
</div>
