<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginCaptchaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@simondes.id',
            'password' => bcrypt('Admin#2026'),
        ]);
    }

    public function test_halaman_login_menampilkan_captcha(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('name="captcha"', false)
            ->assertSee('id="captchaImg"', false);
    }

    public function test_endpoint_captcha_menghasilkan_gambar_png(): void
    {
        $this->get('/captcha')
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->assertEquals(5, strlen(session('login_captcha')));
    }

    public function test_login_ditolak_bila_captcha_salah(): void
    {
        $this->admin();
        session(['login_captcha' => 'ABCDE']);

        $this->post('/login', [
            'email' => 'admin@simondes.id',
            'password' => 'Admin#2026',
            'captcha' => 'WRONG',
        ])->assertSessionHasErrors('captcha');
    }

    public function test_captcha_case_insensitive_dan_berlaku_sekali(): void
    {
        $this->admin();
        session(['login_captcha' => 'ABCDE']);

        // huruf kecil tetap diterima — tapi password salah, login gagal
        // namun captcha sudah TERPAKAI (sekali pakai)
        $this->post('/login', [
            'email' => 'admin@simondes.id',
            'password' => 'PasswordSalah#1',
            'captcha' => 'abcde',
        ])->assertSessionHasErrors('email'); // lolos captcha, gagal password

        // kode lama dipakai lagi → harus ditolak meski password benar
        $this->post('/login', [
            'email' => 'admin@simondes.id',
            'password' => 'Admin#2026',
            'captcha' => 'ABCDE',
        ])->assertSessionHasErrors('captcha');
    }

    public function test_login_berhasil_dengan_captcha_benar(): void
    {
        $this->admin();
        session(['login_captcha' => 'X7K2M']);

        $this->post('/login', [
            'email' => 'admin@simondes.id',
            'password' => 'Admin#2026',
            'captcha' => 'X7K2M',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }
}
