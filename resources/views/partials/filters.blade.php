@php($action = $action ?? request()->url())
<form method="GET" action="{{ $action }}" class="toolbar-left" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
    @if(!empty($withSearch))
    <input class="search" type="text" name="q" value="{{ $search ?? '' }}" placeholder="⌕ {{ $searchPlaceholder ?? 'Cari...' }}" aria-label="Cari">
    @endif
    <select class="field auto-submit" name="year" aria-label="Tahun anggaran">
        @foreach($years as $y)
            <option value="{{ $y }}" {{ (int)$y === $year ? 'selected' : '' }}>TA {{ $y }}</option>
        @endforeach
    </select>
    <select class="field auto-submit" name="district" aria-label="Kecamatan">
        <option value="all">Semua Kecamatan</option>
        @foreach($districts as $d)
            <option value="{{ $d }}" {{ $d === $district ? 'selected' : '' }}>{{ $d }}</option>
        @endforeach
    </select>
    @isset($extraFilters)
        {{ $extraFilters }}
    @endisset
</form>
