<?php

namespace App\Http\Controllers;

use App\Exports\VillageDetailExport;
use App\Models\FundTransaction;
use App\Models\Village;
use App\Support\ActivityLogger;
use App\Support\DataMaster;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DetailController extends Controller
{
    use AppliesFilters;

    public function index(Request $request): View
    {
        $f = $this->filters($request);
        $villages = $this->query($request, $f)->get();

        $selected = $villages->firstWhere('id', (int) $request->input('village', 0))
            ?? $villages->first();

        $transactions = $selected
            ? $selected->transactions()->orderByDesc('date')->orderByDesc('id')->get()
            : collect();

        $byCategory = collect(DataMaster::CATEGORIES)->map(fn ($c) => [
            'category' => $c,
            'total' => (int) $transactions->where('category', $c)->sum('amount'),
        ])->filter(fn ($c) => $c['total'] > 0 || true)->values();

        $complete = $transactions->filter(fn ($t) => $t->receipt_number && $t->evidence_url)->count();
        $missing = $transactions->count() - $complete;

        return view('details.index', $f + [
            'villages' => $villages,
            'selected' => $selected,
            'transactions' => $transactions,
            'byCategory' => $byCategory,
            'completeDocs' => $complete,
            'missingDocs' => $missing,
        ]);
    }

    public function exportExcel(Request $request, Village $village)
    {
        ActivityLogger::log('export', 'Rincian', "Ekspor Excel rincian desa {$village->name} TA {$village->year}");

        return Excel::download(
            new VillageDetailExport($village),
            "rincian-{$village->code}-{$village->year}.xlsx"
        );
    }

    public function exportPdf(Request $request, Village $village)
    {
        ActivityLogger::log('export', 'Rincian', "Ekspor PDF rincian desa {$village->name} TA {$village->year}");

        return Pdf::loadView('exports.detail-pdf', ['village' => $village])
            ->setPaper('a4', 'landscape')
            ->download("rincian-{$village->code}-{$village->year}.pdf");
    }
}
