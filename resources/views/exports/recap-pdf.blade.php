<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Laporan Rekapitulasi Dana Desa {{ $year }}</title></head>
<body style="font-family:DejaVu Sans,Arial,sans-serif;font-size:10px;color:#222;margin:30px 34px">
  <table style="width:100%;border-bottom:2px solid #14254d;padding-bottom:10px;margin-bottom:14px">
    <tr>
      <td style="font-size:15px;font-weight:bold;color:#14254d">LAPORAN REKAPITULASI DANA DESA</td>
      <td style="text-align:right;color:#666">Dicetak: {{ now()->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
      <td style="color:#555">DPMD Kabupaten Halmahera Barat — Tahun Anggaran {{ $year }}</td>
      <td style="text-align:right;color:#666">{{ $district === 'all' ? 'Semua Kecamatan' : 'Kec. '.$district }}</td>
    </tr>
  </table>

  <table style="width:100%;border-collapse:collapse;margin-bottom:16px">
    <tr>
      <td style="background:#eef3fc;padding:8px"><b>Total Pagu</b><br>{{ 'Rp'.number_format($pagu, 0, ',', '.') }}</td>
      <td style="background:#e9faf4;padding:8px"><b>Total Realisasi</b><br>{{ 'Rp'.number_format($real, 0, ',', '.') }}</td>
      <td style="background:#fff6e9;padding:8px"><b>Sisa Dana</b><br>{{ 'Rp'.number_format($pagu - $real, 0, ',', '.') }}</td>
      <td style="background:#f2eeff;padding:8px"><b>Serapan</b><br>{{ $pagu > 0 ? round(100 * $real / $pagu, 1) : 0 }}%</td>
    </tr>
  </table>

  <table style="width:100%;border-collapse:collapse">
    <thead>
      <tr style="background:#14254d;color:#fff">
        <th style="padding:7px 9px;text-align:left;border:1px solid #14254d">Desa</th>
        <th style="padding:7px 9px;text-align:left;border:1px solid #14254d">Kecamatan</th>
        <th style="padding:7px 9px;text-align:right;border:1px solid #14254d">Pagu (Rp)</th>
        <th style="padding:7px 9px;text-align:right;border:1px solid #14254d">Realisasi (Rp)</th>
        <th style="padding:7px 9px;text-align:right;border:1px solid #14254d">Sisa (Rp)</th>
        <th style="padding:7px 9px;text-align:right;border:1px solid #14254d">%</th>
        <th style="padding:7px 9px;text-align:left;border:1px solid #14254d">Evaluasi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($villages as $v)
        <tr>
          <td style="padding:6px 9px;border:1px solid #ddd">{{ $v->name }}</td>
          <td style="padding:6px 9px;border:1px solid #ddd">{{ $v->district }}</td>
          <td style="padding:6px 9px;border:1px solid #ddd;text-align:right">{{ number_format($v->budget, 0, ',', '.') }}</td>
          <td style="padding:6px 9px;border:1px solid #ddd;text-align:right">{{ number_format($v->spent, 0, ',', '.') }}</td>
          <td style="padding:6px 9px;border:1px solid #ddd;text-align:right">{{ number_format($v->remaining, 0, ',', '.') }}</td>
          <td style="padding:6px 9px;border:1px solid #ddd;text-align:right">{{ $v->absorption }}%</td>
          <td style="padding:6px 9px;border:1px solid #ddd">{{ $v->status[0] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p style="color:#777;font-size:8.5px;margin-top:20px">
    Dokumen dihasilkan otomatis oleh sistem SiMONDES. Nilai realisasi dihitung dari transaksi tercatat.
    © {{ date('Y') }} — Developed by Cipta Karya Digital Indonesia (ciptakaryadigital.id)
  </p>
</body>
</html>
