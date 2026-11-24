<?php

namespace App\Exports;

use App\Models\FundTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize
{
    public function __construct(private $rows, private int $year) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Realisasi TA '.$this->year;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kode Desa', 'Desa', 'Kecamatan', 'Bidang', 'Uraian', 'Jenis Belanja', 'Sumber Dana', 'Penerima/Penyedia', 'No. Bukti', 'Nominal (Rp)'];
    }

    public function map($t): array
    {
        return [
            $t->date?->format('Y-m-d'),
            $t->village?->code,
            $t->village?->name,
            $t->village?->district,
            $t->category,
            $t->description,
            $t->spending_type,
            $t->funding_source,
            $t->vendor,
            $t->receipt_number,
            $t->amount,
        ];
    }
}
