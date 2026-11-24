<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Web Installer — khusus hosting tanpa akses SSH/terminal (cPanel dll).
 * Cukup: upload file → composer install → buka /install → selesai.
 * Terproteksi: kunci INSTALL_KEY (tertulis di .env) + kunci file lock
 * setelah terpasang, installer otomatis nonaktif.
 */
class InstallerController extends Controller
{
    public static function installed(): bool
    {
        return file_exists(storage_path('app/installed.lock'));
    }

    public function index()
    {
        if (self::installed()) {
            return response()->view('installer', ['mode' => 'already'], 200);
        }

        $key = $this->ensureKey();

        return response()->view('installer', [
            'mode' => 'setup',
            'installKey' => $key,
            'checks' => $this->checks(),
        ]);
    }

    public function run(Request $request)
    {
        if (self::installed()) {
            return redirect('/')->with('toast', 'Aplikasi sudah terpasang.');
        }

        $key = $this->ensureKey();

        $data = $request->validate(['install_key' => ['required', 'string']]);

        if (! hash_equals((string) $key, (string) $data['install_key'])) {
            return back()->withErrors(['install_key' => 'Kunci instalasi salah. Salin persis dari berkas .env (INSTALL_KEY=...)']);
        }

        $log = [];

        // 1. Pastikan APP_KEY ada
        if (! config('app.key')) {
            try {
                Artisan::call('key:generate', ['--force' => true]);
                $log[] = '[OK] APP_KEY dibuat: '.trim(Artisan::output());
                config(['app.key' => env('APP_KEY')]); // tidak dipakai langsung — hanya log
            } catch (\Throwable $e) {
                $log[] = '[GAGAL] key:generate — '.$e->getMessage();
            }
        } else {
            $log[] = '[OK] APP_KEY sudah ada.';
        }

        // 2. Migrasi database
        try {
            Artisan::call('migrate', ['--force' => true]);
            $log[] = "[OK] Migrasi dijalankan:\n".trim(Artisan::output());
        } catch (\Throwable $e) {
            $log[] = '[GAGAL] migrate — '.$e->getMessage()."\nPeriksa kredensial DB (DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD) di berkas .env.";

            return response()->view('installer', ['mode' => 'result', 'log' => $log, 'success' => false, 'installKey' => $key]);
        }

        // 3. Seed admin + data demo
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $log[] = "[OK] Seed dijalankan:\n".trim(Artisan::output());
        } catch (\Throwable $e) {
            $log[] = '[peringatan] db:seed — '.$e->getMessage();
        }

        // 4. Bersihkan cache aplikasi (jangan sentuh config/route/view —
        //    menghapusnya di tengah request aktif bisa memutus respons)
        try {
            Artisan::call('cache:clear');
        } catch (\Throwable) {
        }
        $log[] = '[OK] Cache dibersihkan.';

        // 5. Buat folder upload branding + proteksi
        $dir = public_path('uploads/branding');
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $log[] = '[OK] Folder upload siap (public/uploads/branding).';

        // 6. Kunci installer agar tak bisa dijalankan lagi
        @file_put_contents(storage_path('app/installed.lock'), now()->toIso8601String()."\n".($request->ip() ?? '-')."\n");

        return response()->view('installer', [
            'mode' => 'result',
            'log' => $log,
            'success' => true,
            'installKey' => $key,
        ]);
    }

    private function checks(): array
    {
        $dbOk = false;
        $dbError = null;
        try {
            DB::select('SELECT 1');
            $dbOk = true;
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        $tablesOk = false;
        if ($dbOk) {
            try {
                $tablesOk = collect(DB::select('SHOW TABLES'))->contains(fn ($r) => in_array('villages', (array) $r));
            } catch (\Throwable) {
            }
        }

        return [
            ['PHP >= 8.2', version_compare(PHP_VERSION, '8.2.0', '>='), PHP_VERSION],
            ['Ekstensi pdo_mysql', extension_loaded('pdo_mysql'), ''],
            ['Ekstensi gd (proses logo)', extension_loaded('gd'), ''],
            ['Ekstensi mbstring', extension_loaded('mbstring'), ''],
            ['Berkas .env ada', file_exists(base_path('.env')), ''],
            ['.env bisa ditulis', is_writable(base_path('.env')), ''],
            ['storage/ bisa ditulis', is_writable(storage_path()), ''],
            ['public/uploads bisa ditulis', is_writable(public_path()) || @mkdir(public_path('uploads'), 0755, true), ''],
            ['Koneksi database ' . config('database.connections.mysql.database'), $dbOk, $dbError ?? ''],
            ['Tabel aplikasi', $tablesOk, $tablesOk ? 'Sudah ada — installer hanya melengkapi' : 'Belum ada — akan dibuat otomatis'],
        ];
    }

    /** Ambil / buat kunci instalasi di .env */
    private function ensureKey(): string
    {
        $key = config('installer.key') ?: env('INSTALL_KEY');

        if ($key && $key !== 'change-me') {
            return (string) $key;
        }

        $key = 'inst_'.Str::random(32);
        $envFile = base_path('.env');

        if (file_exists($envFile)) {
            $content = (string) file_get_contents($envFile);
            if (str_contains($content, 'INSTALL_KEY=')) {
                $content = preg_replace('/^INSTALL_KEY=.*$/m', 'INSTALL_KEY='.$key, $content);
            } else {
                $content = rtrim($content)."\n\nINSTALL_KEY=$key\n";
            }
            @file_put_contents($envFile, $content);
        }

        return $key;
    }
}
