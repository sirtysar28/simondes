<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(string $action, ?string $module = null, ?string $description = null, ?int $userId = null): void
    {
        try {
            $request = request();

            ActivityLog::create([
                'user_id' => $userId ?? ($request?->user()?->id),
                'action' => $action,
                'module' => $module,
                'description' => mb_substr((string) $description, 0, 480),
                'ip' => $request?->ip(),
                'user_agent' => mb_substr((string) ($request?->userAgent() ?? ''), 0, 480),
            ]);
        } catch (\Throwable) {
            // Logging tidak boleh mengganggu jalannya aplikasi
        }
    }
}
