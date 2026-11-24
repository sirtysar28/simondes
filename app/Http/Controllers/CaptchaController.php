<?php

namespace App\Http\Controllers;

/**
 * CAPTCHA login — 5 karakter, teks JELAS (tanpa distorsi berat):
 * - Huruf besar & angka tanpa karakter ambigu (tanpa 0/O, 1/I/l)
 * - Font tebal besar, rotasi ringan ±6°, noise halus
 * - Kode disimpan di session dan WAJIB berlaku sekali pakai
 */
class CaptchaController extends Controller
{
    /** Karakter aman — tanpa kembaran ambigu */
    private const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function show()
    {
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= self::CHARS[random_int(0, strlen(self::CHARS) - 1)];
        }

        // Simpan & ganti kode lama (satu kode aktif per session)
        session(['login_captcha' => $code]);

        [$w, $h] = [220, 64];
        $img = imagecreatetruecolor($w, $h);

        // Latar lembut
        $bg = imagecolorallocate($img, 244, 248, 255);
        imagefilledrectangle($img, 0, 0, $w, $h, $bg);

        // Bingkai tipis
        $border = imagecolorallocate($img, 205, 218, 240);
        imagerectangle($img, 0, 0, $w - 1, $h - 1, $border);

        // Noise titik halus (tidak mengganggu keterbacaan)
        for ($i = 0; $i < 40; $i++) {
            $c = imagecolorallocate($img, 205 + random_int(0, 40), 215 + random_int(0, 35), 235 + random_int(0, 20));
            imagesetpixel($img, random_int(4, $w - 5), random_int(4, $h - 5), $c);
        }

        $font = resource_path('fonts/DejaVuSans-Bold.ttf');
        $hasFont = is_file($font) && function_exists('imagettftext');

        // Gambar 5 karakter satu per satu: geser & rotasi ringan
        $slot = ($w - 24) / 5;
        for ($i = 0; $i < 5; $i++) {
            $char = $code[$i];
            $x = 16 + $i * $slot + random_int(-2, 2);
            $y = (int) ($h / 2 + 11) + random_int(-4, 4);
            $angle = random_int(-6, 6);

            // Warna gelap kuat & kontras (navy/biru)
            $color = imagecolorallocate($img, random_int(20, 40), random_int(45, 70), random_int(120, 170));

            if ($hasFont) {
                imagettftext($img, 26, $angle, $x, $y, $color, $font, $char);
            } else {
                // Fallback tanpa TTF: font bawaan GD diskalakan agar tetap besar & jelas
                $tmp = imagecreatetruecolor(40, 48);
                $white = imagecolorallocate($tmp, 244, 248, 255);
                imagefilledrectangle($tmp, 0, 0, 39, 47, $white);
                $tmpColor = imagecolorallocate($tmp, 30, 55, 145);
                imagestring($tmp, 5, 12, 14, $char, $tmpColor);
                imagecopyresampled($img, $tmp, (int) $x, $y - 40, 0, 0, 32, 44, 40, 48);
            }
        }

        // Dua garis pengaman tipis melintang
        $line = imagecolorallocate($img, 190, 205, 232);
        imageline($img, 6, random_int(10, 20), $w - 6, random_int(10, 20), $line);
        imageline($img, 6, $h - random_int(10, 20), $w - 6, $h - random_int(10, 20), $line);

        ob_start();
        imagepng($img, null, 6);
        $data = ob_get_clean();
        imagedestroy($img);

        return response($data, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /** Validasi input vs kode session (case-insensitive) lalu hapus — sekali pakai */
    public static function check(?string $input): bool
    {
        $expected = strtoupper((string) session('login_captcha'));
        session()->forget('login_captcha');

        return $expected !== '' && hash_equals($expected, strtoupper(trim((string) $input)));
    }

    /** Hapus kode (mis. saat butuh refresh) */
    public static function flush(): void
    {
        session()->forget('login_captcha');
    }
}
