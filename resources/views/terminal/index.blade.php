@extends('layouts.app')
@section('title', 'Terminal Artisan')
@section('breadcrumb', 'Terminal')
@section('page_title', 'Terminal Artisan')

@section('content')
<section>
  <div class="toolbar">
    <div>
      <h2>Terminal Artisan (Aman)</h2>
      <p class="section-intro" style="color:#8694a8;line-height:1.7;font-size:12px;margin:6px 0 0">Jalankan perintah migrasi &amp; pemeliharaan Laravel. Hanya perintah pada <b>daftar putih</b> yang dapat dieksekusi — tanpa shell bebas, aman dari injeksi perintah.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('terminal.run') }}">
    @csrf
    <div class="term-cmd-grid">
      @foreach($commands as $key => $cmd)
        <label class="term-cmd {{ !empty($cmd['destructive']) ? 'destructive' : '' }}">
          <span style="display:flex;align-items:center">
            <input type="radio" name="command" value="{{ $key }}" required {{ $loop->first ? 'checked' : '' }}>
            <code>{{ $cmd['label'] }}</code>
          </span>
          <small>{{ $cmd['note'] }}{{ !empty($cmd['destructive']) ? ' ⚠ Perintah destruktif.' : '' }}</small>
        </label>
      @endforeach
    </div>

    <label class="checkline">
      <input type="checkbox" name="confirm" value="1" style="width:auto;accent-color:#c0384b">
      Saya mengerti risiko perintah destruktif (rollback/seed akan mengubah data).
    </label>

    <button type="submit" class="btn dark" style="height:44px">▶ Jalankan Perintah</button>
  </form>

  <div class="terminal-card" style="margin-top:18px">
    <div class="terminal-head">
      <i style="background:#ff5f57"></i><i style="background:#febc2e"></i><i style="background:#28c840"></i>
      <span style="margin-left:10px;font-size:12px;color:#7d8fb5;font-weight:700">{{ $terminal_ran ?? 'bash — simondes' }}</span>
      @if(isset($terminal_exit))
        <span style="margin-left:auto;font-size:11px;font-weight:800;padding:3px 10px;border-radius:20px;{{ $terminal_exit === 0 ? 'background:#0d3b2e;color:#39d5a0' : 'background:#4b1523;color:#ff7b8a' }}">exit {{ $terminal_exit }}</span>
      @endif
    </div>
    <div class="terminal-body">@if(isset($terminal_output)){{ $terminal_output }}@else<span style="color:#5b6e97">$ pilih perintah di atas lalu tekan "Jalankan Perintah"…</span>@endif</div>
  </div>

  <div class="notice" style="margin-top:16px">
    <b>Kenapa tidak ada input teks bebas?</b> Terminal ini dirancang anti web-shell: perintah dijalankan lewat Symfony Process (tanpa shell) dan hanya menerima argumen baku dari daftar putih — penyusup tidak dapat menyisipkan <code>system()</code>, <code>eval()</code>, atau perintah arbitrer.
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('js/forms.js') }}" defer></script>
@endpush
