<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Village;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        // Degrade anggun: jika database belum dimigrasi (belum /install),
        // halaman depan tetap tampil dengan nilai kosong.
        try {
            $data = $this->compute();
        } catch (\Throwable) {
            $data = [
                'year' => (int) date('Y'),
                'pagu' => 0,
                'real' => 0,
                'sisa' => 0,
                'totalDesa' => 0,
                'totalKecamatan' => 0,
                'countTx' => 0,
                'absorption' => 0,
                'top' => collect(),
                'byDistrict' => collect(),
            ];
        }

        return view('landing', $data);
    }

    private function compute(): array
    {
        $year = (int) (Village::max('year') ?? date('Y'));

        $villages = Village::withSum('transactions as spent', 'amount')
            ->where('year', $year)
            ->orderBy('name')
            ->get();

        $pagu = (int) $villages->sum('budget');
        $real = (int) $villages->sum('spent');
        $countTx = FundTransaction::whereHas('village', fn ($q) => $q->where('year', $year))->count();
        $districts = $villages->pluck('district')->unique()->values();

        $top = $villages->sortByDesc('spent')->take(6)->values();

        $byDistrict = $districts->map(function ($d) use ($villages) {
            $rows = $villages->where('district', $d);

            return [
                'district' => $d,
                'pagu' => (int) $rows->sum('budget'),
                'real' => (int) $rows->sum('spent'),
            ];
        })->sortByDesc('real')->values();

        return [
            'year' => $year,
            'pagu' => $pagu,
            'real' => $real,
            'sisa' => $pagu - $real,
            'totalDesa' => $villages->count(),
            'totalKecamatan' => $districts->count(),
            'countTx' => $countTx,
            'absorption' => $pagu > 0 ? (int) round(100 * $real / $pagu) : 0,
            'top' => $top,
            'byDistrict' => $byDistrict,
        ];
    }
}
