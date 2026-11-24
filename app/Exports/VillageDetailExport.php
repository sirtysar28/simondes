<?php

namespace App\Exports;

use App\Models\Village;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class VillageDetailExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private Village $village) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->village->transactions()->orderBy('date')->get();
    }

    public function title(): string
    {
        return 'Rincian '.$this->village->code;
    }

    public function headings(): array
    {
        return [
            'Rincian Penggunaan Dana Desa — '.$this->village->name.' / '.$this->village->district.' / TA '.$this->village->year,
            'Pagu: '.$this->village->budget.' | Realisasi: '.$this->village->spent.' | Sisa: '.$this->village->remaining,
            '',
            'Tanggal', 'Bidang', 'Jenis Belanja', 'Uraian', 'Sumber Dana', 'Penerima', 'No. Bukti', 'Nominal (Rp)',
        ];
    }

    public function map($t): array
    {
        return [
            $t->date?->format('Y-m-d'),
            $t->category,
            $t->spending_type,
            $t->description,
            $t->funding_source,
            $t->vendor,
            $t->receipt_number,
            $t->amount,
        ];
    }
}
