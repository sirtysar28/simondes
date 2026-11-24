<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Support\ActivityLogger;
use App\Support\DataMaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VillagesExport;

class VillageController extends Controller
{
    use AppliesFilters;

    public function index(Request $request): View
    {
        $f = $this->filters($request);
        $search = trim((string) $request->input('q', ''));

        $villages = $this->query($request, $f)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%");
                });
            })
            ->paginate(25)
            ->withQueryString();

        return view('villages.index', $f + ['villages' => $villages, 'search' => $search]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (Village::where('code', $data['code'])->where('year', $data['year'])->exists()) {
            return back()->withErrors(['code' => 'Kode desa pada tahun tersebut sudah digunakan.'])->withInput();
        }

        $village = Village::create($data);
        ActivityLogger::log('create', 'Data Desa', "Tambah desa {$village->name} ({$village->code}) TA {$village->year}");

        return redirect()->route('villages.index', ['year' => $village->year])
            ->with('toast', 'Data desa berhasil disimpan.');
    }

    public function update(Request $request, Village $village): RedirectResponse
    {
        $data = $this->validated($request, $village->id);

        if (Village::where('code', $data['code'])->where('year', $data['year'])->where('id', '!=', $village->id)->exists()) {
            return back()->withErrors(['code' => 'Kode desa pada tahun tersebut sudah digunakan.'])->withInput();
        }

        if ($data['budget'] < $village->spent) {
            return back()->withErrors(['budget' => 'Pagu tidak boleh kurang dari realisasi yang sudah dicatat.'])->withInput();
        }

        $village->update($data);
        ActivityLogger::log('update', 'Data Desa', "Ubah desa {$village->name} ({$village->code}) TA {$village->year}");

        return redirect()->route('villages.index', ['year' => $village->year])
            ->with('toast', 'Data desa berhasil diperbarui.');
    }

    public function destroy(Village $village): RedirectResponse
    {
        $name = $village->name;
        $count = $village->transactions()->count();
        $village->delete();
        ActivityLogger::log('delete', 'Data Desa', "Hapus desa {$name} beserta {$count} transaksi terkait");

        return back()->with('toast', "Desa {$name} dan {$count} transaksi terkait dihapus.");
    }

    public function exportExcel(Request $request)
    {
        $f = $this->filters($request);
        $rows = $this->query($request, $f)->get();
        ActivityLogger::log('export', 'Data Desa', "Ekspor Excel data desa TA {$f['year']}");

        return Excel::download(new VillagesExport($rows, $f['year']), "simondes-desa-{$f['year']}.xlsx");
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-\.]+$/'],
            'name' => ['required', 'string', 'max:120'],
            'district' => ['required', 'string', 'max:120'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'budget' => ['required', 'integer', 'min:0', 'max:999999999999'],
        ], [
            'code.regex' => 'Kode desa hanya boleh huruf, angka, titik, dan strip.',
            'budget.max' => 'Nilai pagu terlalu besar.',
        ]);
    }
}
