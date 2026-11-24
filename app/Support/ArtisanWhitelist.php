<?php

namespace App\Support;

/**
 * Daftar putih perintah artisan yang boleh dijalankan dari Terminal internal.
 * TIDAK ADA input teks bebas — pengguna hanya dapat memilih perintah yang
 * sudah didefinisikan di sini, sehingga injeksi shell mustahil terjadi.
 * Eksekusi memakai Symfony Process (array args, tanpa shell).
 */
class ArtisanWhitelist
{
    /** @var array<string, array{label:string, command:array<int,string>, destructive?:bool, note?:string}> */
    public static function commands(): array
    {
        return [
            'migrate' => [
                'label' => 'php artisan migrate --force',
                'command' => ['migrate', '--force'],
                'note' => 'Menjalankan migrasi database yang belum dieksekusi.',
            ],
            'migrate_status' => [
                'label' => 'php artisan migrate:status',
                'command' => ['migrate:status'],
                'note' => 'Melihat status seluruh migrasi.',
            ],
            'migrate_rollback' => [
                'label' => 'php artisan migrate:rollback --force',
                'command' => ['migrate:rollback', '--force'],
                'destructive' => true,
                'note' => 'Mengembalikan batch migrasi terakhir (data batch itu hilang).',
            ],
            'seed' => [
                'label' => 'php artisan db:seed --force',
                'command' => ['db:seed', '--force'],
                'destructive' => true,
                'note' => 'Menjalankan ulang seeder (data demo dapat duplikat).',
            ],
            'storage_link' => [
                'label' => 'php artisan storage:link',
                'command' => ['storage:link'],
                'note' => 'Membuat symlink public/storage.',
            ],
            'cache_clear' => [
                'label' => 'php artisan cache:clear',
                'command' => ['cache:clear'],
                'note' => 'Membersihkan cache aplikasi.',
            ],
            'config_clear' => [
                'label' => 'php artisan config:clear',
                'command' => ['config:clear'],
                'note' => 'Menghapus cache konfigurasi.',
            ],
            'route_list' => [
                'label' => 'php artisan route:list',
                'command' => ['route:list'],
                'note' => 'Menampilkan daftar route aplikasi.',
            ],
            'optimize_clear' => [
                'label' => 'php artisan optimize:clear',
                'command' => ['optimize:clear'],
                'note' => 'Membersihkan semua cache (config, route, view).',
            ],
            'queue_restart' => [
                'label' => 'php artisan queue:restart',
                'command' => ['queue:restart'],
                'note' => 'Memulai ulang worker antrean.',
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return static::commands()[$key] ?? null;
    }
}
