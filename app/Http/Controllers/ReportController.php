<?php

namespace App\Http\Controllers;

use App\Exports\RecapExport;
use App\Support\ActivityLogger;
use App\Support\DataMaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use AppliesFilters;

    public function index(Request $request): View
    {
        $f = $this->filters($request);
        $villages = $this->query($request, $f)->with('transactions')->get();

        $pagu = (int) $villages->sum('budget');
        $real = (int) $villages->sum('spent');

        $districtChart = $villages->groupBy('district')->map(function ($rows, $district) {
            return [
                'district' => $district,
                'pagu' => (int) $rows->sum('budget'),
                'real' => (int) $rows->sum('spent'),
            ];
        })->sortByDesc('pagu')->values();

        $categoryChart = collect(DataMaster::CATEGORIES)->map(function ($cat) use ($villages) {
            $total = 0;
            $villages->each(function ($v) use (&$total, $cat) {
                $total += (int) $v->transactions->where('category', $cat)->sum('amount');
            });

            return ['category' => $cat, 'total' => $total];
        })->filter(fn ($c) => $c['total'] > 0)->values();

        return view('reports.index', $f + [
            'villages' => $villages,
            'pagu' => $pagu,
            'real' => $real,
            'districtChart' => $districtChart,
            'categoryChart' => $categoryChart,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $f = $this->filters($request);
        $rows = $this->query($request, $f)->get();
        ActivityLogger::log('export', 'Laporan', "Ekspor Excel laporan rekap TA {$f['year']}");

        return Excel::download(new RecapExport($rows, $f['year']), "laporan-dana-desa-{$f['year']}.xlsx");
    }

    public function exportPdf(Request $request)
    {
        $f = $this->filters($request);
        $villages = $this->query($request, $f)->get();
        ActivityLogger::log('export', 'Laporan', "Ekspor PDF laporan rekap TA {$f['year']}");

        return Pdf::loadView('exports.recap-pdf', [
            'villages' => $villages,
            'year' => $f['year'],
            'district' => $f['district'],
            'pagu' => (int) $villages->sum('budget'),
            'real' => (int) $villages->sum('spent'),
        ])->setPaper('a4', 'landscape')->download("laporan-dana-desa-{$f['year']}.pdf");
    }
}
