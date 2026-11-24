<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Rincian Dana Desa {{ $village->name }}</title></head>
<body style="font-family:DejaVu Sans,Arial,sans-serif;font-size:10px;color:#222;margin:30px 34px">
  <table style="width:100%;border-bottom:2px solid #14254d;padding-bottom:10px;margin-bottom:14px">
    <tr>
      <td style="font-size:15px;font-weight:bold;color:#14254d">LAPORAN RINCIAN PENGGUNAAN DANA DESA</td>
      <td style="text-align:right;color:#666">Dicetak: {{ now()->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
      <td style="color:#555">{{ $village->name }} — Kec. {{ $village->district }} — TA {{ $village->year }} ({{ $village->code }})</td>
      <td style="text-align:right;color:#666">Pagu: {{ 'Rp'.number_format($village->budget, 0, ',', '.') }}</td>
    </tr>
    <tr>
      <td style="color:#555">Realisasi: {{ 'Rp'.number_format($village->spent, 0, ',', '.') }} — Sisa: {{ 'Rp'.number_format($village->remaining, 0, ',', '.') }}</td>
      <td style="text-align:right;color:#666">Serapan: {{ $village->absorption }}%</td>
    </tr>
  </table>

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="background:#14254d;color:#fff">
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">Tanggal</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">Uraian</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">Bidang</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">Jenis</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">Sumber</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">Penerima</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:left">No. Bukti</th>
        <th style="padding:6px 8px;border:1px solid #14254d;text-align:right">Jumlah (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($village->transactions()->orderBy('date')->get() as $t)
        <tr>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->date->format('d/m/Y') }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->description }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->category }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->spending_type ?? '—' }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->funding_source ?? '—' }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->vendor ?? '—' }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd">{{ $t->receipt_number ?? '—' }}</td>
          <td style="padding:5px 8px;border:1px solid #ddd;text-align:right">{{ number_format($t->amount, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="8" style="padding:14px;border:1px solid #ddd;text-align:center;color:#888">Belum ada transaksi.</td></tr>
      @endforelse
    </tbody>
  </table>

  <p style="color:#777;font-size:8.5px;margin-top:20px">
    © {{ date('Y') }} — Developed by Cipta Karya Digital Indonesia (ciptakaryadigital.id)
  </p>
</body>
</html>
