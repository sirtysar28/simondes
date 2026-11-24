<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Models\Village;
use App\Support\ActivityLogger;
use App\Support\SecureImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'appName' => Setting::get('app_name', config('app.name')),
            'appShort' => Setting::get('app_short', 'SiMONDES'),
            'logoPath' => Setting::get('logo_path'),
            'faviconPath' => Setting::get('favicon_path'),
            'smtp' => [
                'host' => Setting::get('smtp_host', ''),
                'port' => Setting::get('smtp_port', '587'),
                'encryption' => Setting::get('smtp_encryption', 'tls'),
                'username' => Setting::get('smtp_username', ''),
                'password_set' => (bool) Setting::get('smtp_password'),
                'from_address' => Setting::get('smtp_from_address', ''),
                'from_name' => Setting::get('smtp_from_name', 'SiMONDES'),
            ],
        ]);
    }

    /** Pengaturan umum + logo + favicon */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'app_short' => ['required', 'string', 'max:40'],
            'logo' => ['nullable', 'file', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
            'favicon' => ['nullable', 'file', 'max:512', 'mimes:png,jpg,jpeg,webp'],
        ]);

        Setting::set('app_name', $data['app_name']);
        Setting::set('app_short', $data['app_short']);

        try {
            if ($request->hasFile('logo')) {
                $old = Setting::get('logo_path');
                Setting::set('logo_path', SecureImageUpload::store($request->file('logo'), 'uploads/branding'));
                SecureImageUpload::remove($old);
            }
            if ($request->hasFile('favicon')) {
                $old = Setting::get('favicon_path');
                Setting::set('favicon_path', SecureImageUpload::store($request->file('favicon'), 'uploads/branding'));
                SecureImageUpload::remove($old);
            }
            if ($request->boolean('remove_logo')) {
                SecureImageUpload::remove(Setting::get('logo_path'));
                Setting::forget('logo_path');
            }
            if ($request->boolean('remove_favicon')) {
                SecureImageUpload::remove(Setting::get('favicon_path'));
                Setting::forget('favicon_path');
            }
        } catch (\RuntimeException $e) {
            return back()->withErrors(['logo' => $e->getMessage()]);
        }

        ActivityLogger::log('update', 'Pengaturan', 'Perbarui pengaturan umum/logo/favicon');

        return back()->with('toast', 'Pengaturan umum berhasil disimpan.');
    }

    /** Pengaturan SMTP */
    public function updateSmtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'smtp_host' => ['required', 'string', 'max:190'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_encryption' => ['required', 'in:tls,ssl,none'],
            'smtp_username' => ['nullable', 'string', 'max:190'],
            'smtp_password' => ['nullable', 'string', 'max:200'],
            'smtp_from_address' => ['required', 'email', 'max:190'],
            'smtp_from_name' => ['required', 'string', 'max:100'],
        ]);

        Setting::set('smtp_host', $data['smtp_host']);
        Setting::set('smtp_port', (string) $data['smtp_port']);
        Setting::set('smtp_encryption', $data['smtp_encryption']);
        Setting::set('smtp_username', (string) $data['smtp_username']);
        if (($password = (string) $data['smtp_password']) !== '') {
            Setting::set('smtp_password', encrypt($password));
        }
        Setting::set('smtp_from_address', $data['smtp_from_address']);
        Setting::set('smtp_from_name', $data['smtp_from_name']);

        ActivityLogger::log('update', 'Pengaturan', 'Perbarui konfigurasi SMTP');

        return back()->with('toast', 'Konfigurasi SMTP tersimpan. Gunakan tombol "Kirim Email Uji" untuk memverifikasi.')->with('tab', 'smtp');
    }

    /** Kirim email uji */
    public function testSmtp(Request $request): RedirectResponse
    {
        $data = $request->validate(['test_to' => ['required', 'email', 'max:190']]);

        $this->applyMailConfig();

        try {
            Mail::raw('Ini adalah email uji dari aplikasi SiMONDES. Jika Anda menerima email ini, konfigurasi SMTP sudah benar.', function ($message) use ($data) {
                $message->to($data['test_to'])->subject('[SiMONDES] Email Uji SMTP');
            });
            ActivityLogger::log('test_smtp', 'Pengaturan', 'Kirim email uji ke '.$data['test_to']);

            return back()->with('toast', 'Email uji terkirim ke '.$data['test_to'].'.')->with('tab', 'smtp');
        } catch (\Throwable $e) {
            ActivityLogger::log('test_smtp_gagal', 'Pengaturan', 'Gagal kirim email uji: '.mb_substr($e->getMessage(), 0, 200));

            return back()->withErrors(['smtp_test' => 'Gagal mengirim: '.mb_substr($e->getMessage(), 0, 250)])->with('tab', 'smtp');
        }
    }

    /** Ganti password akun yang sedang login */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:10', 'max:200', 'confirmed',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.min' => 'Password minimal 10 karakter.',
            'password.regex' => 'Password harus mengandung huruf kecil, huruf besar, angka, dan simbol.',
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);
        ActivityLogger::log('update', 'Keamanan', 'Ganti password akun');

        return back()->with('toast', 'Password berhasil diganti.')->with('tab', 'keamanan');
    }

    /** Cadangkan data (JSON) */
    public function backup()
    {
        $payload = [
            'version' => 2,
            'exported_at' => now()->toIso8601String(),
            'villages' => Village::get(),
            'transactions' => FundTransaction::get(),
        ];
        ActivityLogger::log('backup', 'Data', 'Unduh cadangan JSON');

        return response()->json($payload)
            ->header('Content-Disposition', 'attachment; filename="simondes-cadangan-'.now()->format('Ymd-His').'.json"');
    }

    /** Impor cadangan */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate(['restore_file' => ['required', 'file', 'max:10240', 'mimes:json']]);

        $raw = file_get_contents($request->file('restore_file')->getRealPath());
        $data = json_decode($raw, true);

        if (! is_array($data) || ! isset($data['villages'], $data['transactions'])) {
            return back()->withErrors(['restore_file' => 'Struktur berkas cadangan tidak valid.'])->with('tab', 'data');
        }

        \DB::transaction(function () use ($data) {
            FundTransaction::query()->delete();
            Village::query()->delete();

            foreach ($data['villages'] as $v) {
                if (! isset($v['code'], $v['name'], $v['district'], $v['year'], $v['budget'])) {
                    throw new \RuntimeException('Data desa tidak valid.');
                }
                Village::create([
                    'code' => mb_substr((string) $v['code'], 0, 20),
                    'name' => mb_substr((string) $v['name'], 0, 120),
                    'district' => mb_substr((string) $v['district'], 0, 120),
                    'year' => (int) $v['year'],
                    'budget' => max(0, (int) $v['budget']),
                ]);
            }

            $map = Village::pluck('id', 'code');
            foreach ($data['transactions'] as $t) {
                $newId = $map[$t['code'] ?? ''] ?? null;
                if (! $newId || empty($t['date']) || empty($t['amount'])) {
                    continue;
                }
                FundTransaction::create([
                    'village_id' => $newId,
                    'date' => substr((string) $t['date'], 0, 10),
                    'category' => mb_substr((string) ($t['category'] ?? 'Lainnya'), 0, 100),
                    'spending_type' => mb_substr((string) ($t['spending_type'] ?? ''), 0, 100) ?: null,
                    'funding_source' => mb_substr((string) ($t['funding_source'] ?? ''), 0, 100) ?: null,
                    'description' => mb_substr((string) ($t['description'] ?? '-'), 0, 500),
                    'amount' => max(0, (int) $t['amount']),
                    'vendor' => mb_substr((string) ($t['vendor'] ?? ''), 0, 150) ?: null,
                    'receipt_number' => mb_substr((string) ($t['receipt_number'] ?? ''), 0, 100) ?: null,
                    'evidence_url' => mb_substr((string) ($t['evidence_url'] ?? ''), 0, 500) ?: null,
                ]);
            }
        });

        ActivityLogger::log('restore', 'Data', 'Impor cadangan JSON');

        return back()->with('toast', 'Cadangan berhasil dipulihkan.')->with('tab', 'data');
    }

    /** Reset ke data demo */
    public function resetDemo(): RedirectResponse
    {
        \Artisan::call('db:seed', ['--class' => 'DemoDataSeeder', '--force' => true]);
        ActivityLogger::log('reset', 'Data', 'Reset data ke contoh demo');

        return back()->with('toast', 'Data demo berhasil dipulihkan.')->with('tab', 'data');
    }

    public static function applyMailConfig(): void
    {
        $host = Setting::get('smtp_host');
        if (! $host) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'host' => $host,
                'port' => (int) Setting::get('smtp_port', 587),
                'encryption' => ($enc = Setting::get('smtp_encryption', 'tls')) === 'none' ? null : $enc,
                'username' => Setting::get('smtp_username'),
                'password' => Setting::get('smtp_password') ? decrypt(Setting::get('smtp_password')) : null,
                'timeout' => 15,
            ],
            'mail.from.address' => Setting::get('smtp_from_address', config('mail.from.address')),
            'mail.from.name' => Setting::get('smtp_from_name', config('mail.from.name')),
        ]);
    }
}
