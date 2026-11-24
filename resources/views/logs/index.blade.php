@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('breadcrumb', 'Log Aktivitas')
@section('page_title', 'Log Aktivitas Pengguna')

@section('content')
<section>
  <div class="toolbar">
    <div>
      <h2>Log Aktivitas</h2>
      <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:6px 0 0">Jejak audit seluruh aksi penting: login, perubahan data, ekspor, hingga perintah terminal.</p>
    </div>
  </div>

  <div class="panel">
    <div class="toolbar">
      <form method="GET" action="{{ route('logs.index') }}" class="toolbar-left" style="display:flex;gap:10px;flex-wrap:wrap">
        <input class="search" type="text" name="q" value="{{ $search }}" placeholder="⌕ Cari aksi / pengguna...">
        <select class="field auto-submit" name="action">
          <option value="">Semua Aksi</option>
          @foreach($actions as $a)
            <option value="{{ $a }}" {{ $a === $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $a)) }}</option>
          @endforeach
        </select>
      </form>
    </div>

    <div class="table-scroll">
      <table class="data-table">
        <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Modul</th><th>Deskripsi</th><th>IP</th></tr></thead>
        <tbody>
        @forelse($logs as $log)
          @php($cls = match($log->action) { 'login' => 'log-login', 'create' => 'log-create', 'update' => 'log-update', 'delete' => 'log-delete', default => (str_contains($log->action, 'export') ? 'log-export' : (str_contains($log->action, 'terminal') ? 'log-terminal' : 'log-other')) }))
          <tr>
            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
            <td><div class="v-name">{{ $log->user?->name ?? 'Sistem' }}</div><div class="v-code">{{ $log->user?->email ?? '-' }}</div></td>
            <td><span class="log-icon {{ $cls }}">{{ strtoupper(mb_substr($log->action, 0, 2)) }}</span> {{ str_replace('_', ' ', ucfirst($log->action)) }}</td>
            <td>{{ $log->module ?? '—' }}</td>
            <td style="white-space:normal;max-width:420px">{{ $log->description ?? '—' }}</td>
            <td>{{ $log->ip ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="6"><div class="empty">Belum ada aktivitas tercatat.</div></td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    {{ $logs->links('partials.pagination') }}
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
