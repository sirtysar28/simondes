<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Pengunggah gambar yang aman (anti web-shell / php2shell):
 * 1. Validasi MIME asli via finfo + getimagesize (bukan sekadar ekstensi).
 * 2. Hanya izinkan PNG, JPEG, WebP (SVG DITOLAK karena dapat memuat script).
 * 3. Gambar DIRE-ENCODE via GD sehingga payload PHP yang disisipkan pada
 *    metadata/segmen APP diedit hilang sepenuhnya (stegano-shell netral).
 * 4. Nama file acak tanpa ekstensi yang dapat dieksekusi server.
 * 5. Disimpan di public/uploads (tanpa symlink/storage:link untuk hosting
 *    tanpa SSH seperti cPanel) + .htaccess anti-eksekusi otomatis dibuat.
 */
class SecureImageUpload
{
    private const ALLOWED_MIMES = ['image/png', 'image/jpeg', 'image/webp'];
    private const MAX_BYTES = 2097152; // 2 MB

    private const HTACCESS = "# Dibuat otomatis — blokir eksekusi skrip (anti web-shell)
<FilesMatch \"\\.(php|phtml|php[0-9]|phps|phar|pl|py|cgi|sh|asp|aspx|jsp|htaccess)$\">
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Deny from all
    </IfModule>
</FilesMatch>
Options -Indexes -ExecCGI
<IfModule mod_php.c>
    php_flag engine off
</IfModule>
";

    /**
     * @return string path file relatif terhadap public/ (untuk asset())
     */
    public static function store(UploadedFile $file, string $directory): string
    {
        if ($file->getSize() > self::MAX_BYTES) {
            throw new RuntimeException('Ukuran berkas maksimal 2 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->getRealPath());

        if (! in_array($mime, self::ALLOWED_MIMES, true)) {
            throw new RuntimeException('Format harus PNG, JPG, atau WebP.');
        }

        if (@getimagesize($file->getRealPath()) === false) {
            throw new RuntimeException('Berkas bukan gambar yang valid.');
        }

        $image = match ($mime) {
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
        };

        if ($image === false) {
            throw new RuntimeException('Gambar tidak dapat diproses.');
        }

        // Pertahankan transparansi PNG/WebP lalu re-encode (menyingkirkan payload tersembunyi)
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $filename = Str::random(40).'.png';
        $dir = public_path($directory);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Proteksi anti-eksekusi PHP di folder upload (otomatis, tanpa SSH)
        if (! file_exists($dir.'/.htaccess')) {
            @file_put_contents($dir.'/.htaccess', self::HTACCESS);
        }
        if (! file_exists(public_path('uploads').'/index.html')) {
            @file_put_contents(public_path('uploads').'/index.html', '');
        }

        $target = $dir.'/'.$filename;

        if (! imagepng($image, $target, 6) || ! file_exists($target)) {
            throw new RuntimeException('Gagal menyimpan gambar.');
        }

        // Validasi hasil re-encode benar-benar gambar valid
        if (@getimagesize($target) === false) {
            @unlink($target);
            throw new RuntimeException('Hasil proses tidak valid.');
        }

        return $directory.'/'.$filename;
    }

    public static function remove(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $path = public_path($relativePath);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}
