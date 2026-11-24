<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CaptchaController;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        if (Auth::check()) {
            redirect()->route('dashboard')->send();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email', 'max:150'],
                'password' => ['required', 'string', 'max:200'],
            ], [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ]);

        $key = strtolower('login:'.($request->input('email') ?? '').'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."]);
        }

        // CAPTCHA — sekali pakai, case-insensitive
        if (! CaptchaController::check($request->input('captcha'))) {
            return back()->withErrors(['captcha' => 'Kode captcha salah atau kedaluwarsa — masukkan kode baru.'])->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($key);
            ActivityLogger::log('login', 'Autentikasi', 'Login berhasil: '.$request->input('email'));

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($key, 300);
            ActivityLogger::log('login_gagal', 'Autentikasi', 'Percobaan login gagal: '.$request->input('email'));

            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        } catch (\Illuminate\Database\QueryException) {
            // Database belum dimigrasi (belum /install) — arahkan ke installer
            return redirect()->route('install')->with('toast', 'Database belum terpasang. Jalankan pemasangan di halaman ini.');
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLogger::log('logout', 'Autentikasi', 'Logout: '.($request->user()->email ?? '-'));
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
