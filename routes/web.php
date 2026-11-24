<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VillageController;
use Illuminate\Support\Facades\Route;

// ===== Halaman depan (publik) =====
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ===== Web Installer (hosting tanpa SSH — cPanel) =====
Route::get('/install', [InstallerController::class, 'index'])->name('install');
Route::post('/install', [InstallerController::class, 'run'])->name('install.run');

// ===== Autentikasi =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
});

// Gambar CAPTCHA login (kode 5 karakter, teks jelas)
Route::get('/captcha', [CaptchaController::class, 'show'])->name('captcha');

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ===== Aplikasi (wajib login) =====
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data desa & pagu
    Route::get('/desa', [VillageController::class, 'index'])->name('villages.index');
    Route::post('/desa', [VillageController::class, 'store'])->name('villages.store');
    Route::put('/desa/{village}', [VillageController::class, 'update'])->name('villages.update');
    Route::delete('/desa/{village}', [VillageController::class, 'destroy'])->name('villages.destroy');
    Route::get('/desa-export/excel', [VillageController::class, 'exportExcel'])->name('villages.export');

    // Realisasi
    Route::get('/realisasi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/realisasi', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/realisasi/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/realisasi/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/realisasi-export/excel', [TransactionController::class, 'exportExcel'])->name('transactions.export');

    // Laporan
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan-export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/laporan-export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    // Rincian per desa
    Route::get('/rincian', [DetailController::class, 'index'])->name('details.index');
    Route::get('/rincian/{village}/export/excel', [DetailController::class, 'exportExcel'])->name('details.export.excel');
    Route::get('/rincian/{village}/export/pdf', [DetailController::class, 'exportPdf'])->name('details.export.pdf');

    // Pengaturan
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/umum', [SettingController::class, 'updateGeneral'])->name('settings.general');
    Route::post('/pengaturan/smtp', [SettingController::class, 'updateSmtp'])->name('settings.smtp');
    Route::post('/pengaturan/smtp/test', [SettingController::class, 'testSmtp'])->name('settings.smtp.test');
    Route::post('/pengaturan/password', [SettingController::class, 'updatePassword'])->name('settings.password');
    Route::get('/pengaturan/backup', [SettingController::class, 'backup'])->name('settings.backup');
    Route::post('/pengaturan/restore', [SettingController::class, 'restore'])->name('settings.restore');
    Route::post('/pengaturan/reset-demo', [SettingController::class, 'resetDemo'])->name('settings.reset');

    // Log aktivitas & terminal
    Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('/terminal', [TerminalController::class, 'index'])->name('terminal.index');
    Route::post('/terminal/run', [TerminalController::class, 'run'])->name('terminal.run');
});
