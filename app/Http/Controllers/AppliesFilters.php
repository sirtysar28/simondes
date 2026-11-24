<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Support\DataMaster;
use Illuminate\Http\Request;

trait AppliesFilters
{
    protected function filters(Request $request): array
    {
        $years = Village::distinct()->orderByDesc('year')->pluck('year')->map(fn ($y) => (int) $y);
        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        $districts = Village::distinct()->orderBy('district')->pluck('district')
            ->merge(DataMaster::DISTRICTS)->unique()->values();

        $year = (int) $request->input('year', $years->first());
        if (! $years->contains($year)) {
            $year = (int) $years->first();
        }

        $district = $request->input('district', 'all');
        if ($district !== 'all' && ! $districts->contains($district)) {
            $district = 'all';
        }

        return compact('years', 'districts', 'year', 'district');
    }

    protected function query(Request $request, array $f)
    {
        return Village::withSum('transactions as spent', 'amount')
            ->where('year', $f['year'])
            ->when($f['district'] !== 'all', fn ($q) => $q->where('district', $f['district']))
            ->orderBy('name');
    }
}
