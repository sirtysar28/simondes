<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\FundTransaction;
use App\Models\Village;
use App\Support\ActivityLogger;
use App\Support\DataMaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    use AppliesFilters;

    public function index(Request $request): View
    {
        $f = $this->filters($request);
        $search = trim((string) $request->input('q', ''));

        $base = FundTransaction::with('village:id,name,code,district,year')
            ->whereHas('village', function ($q) use ($f) {
                $q->where('year', $f['year']);
                if ($f['district'] !== 'all') {
                    $q->where('district', $f['district']);
                }
            });

        $totals = (clone $base)->selectRaw('count(*) as c, coalesce(sum(amount),0) s, count(distinct village_id) v')
            ->first();

        $rows = (clone $base)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('receipt_number', 'like', "%{$search}%")
                        ->orWhereHas('village', fn ($v) => $v->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('date')->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $villages = Village::where('year', $f['year'])
            ->when($f['district'] !== 'all', fn ($q) => $q->where('district', $f['district']))
            ->orderBy('name')->get();

        return view('transactions.index', $f + [
            'rows' => $rows,
            'totals' => $totals,
            'villages' => $villages,
            'search' => $search,
            'categories' => DataMaster::CATEGORIES,
            'spendingTypes' => DataMaster::SPENDING_TYPES,
            'fundingSources' => DataMaster::FUNDING_SOURCES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $village = Village::findOrFail($data['village_id']);

        $spent = (int) $village->transactions()->sum('amount');
        if ($spent + $data['amount'] > $village->budget) {
            return back()->withErrors(['amount' => 'Transaksi ditolak: total realisasi melebihi pagu desa.'])->withInput();
        }

        $data['user_id'] = $request->user()->id;
        $tx = FundTransaction::create($data);
        ActivityLogger::log('create', 'Realisasi', "Catat realisasi Rp ".number_format($tx->amount, 0, ',', '.')." — {$village->name}: {$tx->description}");

        return redirect()->route('transactions.index', ['year' => $village->year])
            ->with('toast', 'Realisasi berhasil dicatat.');
    }

    public function update(Request $request, FundTransaction $transaction): RedirectResponse
    {
        $data = $this->validated($request);
        $village = Village::findOrFail($data['village_id']);

        $spent = (int) $village->transactions()->where('id', '!=', $transaction->id)->sum('amount');
        if ($spent + $data['amount'] > $village->budget) {
            return back()->withErrors(['amount' => 'Transaksi ditolak: total realisasi melebihi pagu desa.'])->withInput();
        }

        $transaction->update($data);
        ActivityLogger::log('update', 'Realisasi', "Ubah realisasi #{$transaction->id} — {$village->name}");

        return redirect()->route('transactions.index', ['year' => $village->year])
            ->with('toast', 'Realisasi berhasil diperbarui.');
    }

    public function destroy(FundTransaction $transaction): RedirectResponse
    {
        $transaction->delete();
        ActivityLogger::log('delete', 'Realisasi', "Hapus realisasi #{$transaction->id}");

        return back()->with('toast', 'Transaksi realisasi dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $f = $this->filters($request);
        $rows = FundTransaction::with('village:id,name,code,district,year')
            ->whereHas('village', function ($q) use ($f) {
                $q->where('year', $f['year']);
                if ($f['district'] !== 'all') {
                    $q->where('district', $f['district']);
                }
            })->orderByDesc('date')->get();
        ActivityLogger::log('export', 'Realisasi', "Ekspor Excel realisasi TA {$f['year']}");

        return Excel::download(new TransactionsExport($rows, $f['year']), "simondes-realisasi-{$f['year']}.xlsx");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'village_id' => ['required', 'integer', 'exists:villages,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'category' => ['required', 'string', 'max:100', 'in:'.implode(',', DataMaster::CATEGORIES)],
            'spending_type' => ['required', 'string', 'max:100', 'in:'.implode(',', DataMaster::SPENDING_TYPES)],
            'funding_source' => ['required', 'string', 'max:100', 'in:'.implode(',', DataMaster::FUNDING_SOURCES)],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'integer', 'min:1', 'max:999999999999'],
            'vendor' => ['nullable', 'string', 'max:150'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
            'evidence_url' => ['nullable', 'url:https', 'max:500'],
        ], [
            'category.in' => 'Bidang kegiatan tidak valid.',
            'evidence_url.url' => 'Tautan bukti harus URL https:// yang valid.',
            'date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
        ]);

        $data['vendor'] = trim((string) ($data['vendor'] ?? '')) ?: null;
        $data['receipt_number'] = trim((string) ($data['receipt_number'] ?? '')) ?: null;
        $data['evidence_url'] = trim((string) ($data['evidence_url'] ?? '')) ?: null;

        return $data;
    }
}
