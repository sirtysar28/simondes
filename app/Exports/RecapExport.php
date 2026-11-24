<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RecapExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private $rows, private int $year) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Rekap TA '.$this->year;
    }

    public function headings(): array
    {
        return ['Desa', 'Kecamatan', 'Pagu (Rp)', 'Realisasi (Rp)', 'Sisa (Rp)', 'Persentase (%)', 'Evaluasi'];
    }

    public function map($v): array
    {
        $spent = (int) ($v->spent ?? 0);
        $pct = $v->budget > 0 ? round(100 * $spent / $v->budget, 1) : 0;

        return [
            $v->name,
            $v->district,
            $v->budget,
            $spent,
            $v->budget - $spent,
            $pct,
            $v->status[0],
        ];
    }
}
