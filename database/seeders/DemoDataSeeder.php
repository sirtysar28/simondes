<?php

namespace Database\Seeders;

use App\Models\FundTransaction;
use App\Models\User;
use App\Models\Village;
use App\Support\DataMaster;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@simondes.id')->first();
        $year = (int) date('Y');
        $districts = DataMaster::DISTRICTS;
        $budgets = [1150000000, 980000000, 1320000000, 1080000000, 1450000000, 1100000000, 1250000000, 990000000, 1190000000, 1040000000, 1360000000, 1120000000];
        $ratios = [.67, .82, .51, .76, .90, .44, .64, .71, .58, .86, .69, .36];

        foreach ([0 => $year, 1 => $year - 1] as $offset => $ta) {
            FundTransaction::whereHas('village', fn ($q) => $q->where('year', $ta))->delete();
            Village::where('year', $ta)->delete();

            foreach (range(1, 12) as $i) {
                $idx = $i - 1;
                $village = Village::create([
                    'code' => 'HAL-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'-'.$ta,
                    'name' => 'Desa Simulasi '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                    'district' => $districts[intdiv($idx, 2) % count($districts)],
                    'year' => $ta,
                    'budget' => $budgets[$idx] + ($offset * 50000000),
                ]);

                $total = (int) floor($village->budget * $ratios[$idx]);
                $first = (int) floor($total * 0.61);

                FundTransaction::create([
                    'village_id' => $village->id,
                    'user_id' => $admin?->id,
                    'date' => $ta.'-06-'.str_pad((string) (($i % 24) + 1), 2, '0', STR_PAD_LEFT),
                    'category' => DataMaster::CATEGORIES[$idx % count(DataMaster::CATEGORIES)],
                    'spending_type' => DataMaster::SPENDING_TYPES[1],
                    'funding_source' => DataMaster::FUNDING_SOURCES[0],
                    'description' => 'Kegiatan simulasi tahap I TA '.$ta,
                    'amount' => $first,
                    'vendor' => 'CV Simulasi Jaya',
                    'receipt_number' => 'SPJ/'.$ta.'/'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'/A',
                    'evidence_url' => 'https://example.com/bukti/'.$ta.'/'.$i.'a',
                ]);

                FundTransaction::create([
                    'village_id' => $village->id,
                    'user_id' => $admin?->id,
                    'date' => $ta.'-10-'.str_pad((string) (($i % 24) + 1), 2, '0', STR_PAD_LEFT),
                    'category' => DataMaster::CATEGORIES[($idx + 1) % count(DataMaster::CATEGORIES)],
                    'spending_type' => DataMaster::SPENDING_TYPES[2],
                    'funding_source' => DataMaster::FUNDING_SOURCES[0],
                    'description' => 'Kegiatan simulasi tahap II TA '.$ta,
                    'amount' => $total - $first,
                    'vendor' => 'Koperasi Desa Simulasi',
                    'receipt_number' => 'SPJ/'.$ta.'/'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'/B',
                ]);
            }
        }

        $this->command?->info('Data demo dimuat: 24 desa, 48 transaksi.');
    }
}
