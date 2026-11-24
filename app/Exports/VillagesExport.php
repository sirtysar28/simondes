<?php

namespace App\Exports;

use App\Models\Village;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class VillagesExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private $rows, private int $year) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Data Desa TA '.$this->year;
    }

    public function headings(): array
    {
        return ['Tahun', 'Kode Desa', 'Nama Desa', 'Kecamatan', 'Pagu (Rp)', 'Realisasi (Rp)', 'Sisa Dana (Rp)', 'Penyerapan (%)'];
    }

    public function map($v): array
    {
        $spent = (int) ($v->spent ?? 0);

        return [
            $v->year,
            $v->code,
            $v->name,
            $v->district,
            $v->budget,
            $spent,
            $v->budget - $spent,
            $v->budget > 0 ? round(100 * $spent / $v->budget, 1) : 0,
        ];
    }
}
