<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use AppliesFilters;

    public function index(Request $request): View
    {
        $f = $this->filters($request);
        $villages = $this->query($request, $f)->get();

        $pagu = (int) $villages->sum('budget');
        $real = (int) $villages->sum('spent');

        $villageIds = $villages->pluck('id');

        $recent = FundTransaction::with('village:id,name,code')
            ->whereIn('village_id', $villageIds)
            ->orderByDesc('date')->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('dashboard', $f + [
            'villages' => $villages,
            'pagu' => $pagu,
            'real' => $real,
            'sisa' => $pagu - $real,
            'recent' => $recent,
        ]);
    }
}
