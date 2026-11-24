<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@simondes.id'],
            ['name' => 'Administrator DPMD', 'password' => bcrypt('Admin#2026'), 'email_verified_at' => now()]
        );

        $this->command?->info('Admin dibuat: admin@simondes.id / Admin#2026');
    }
}
