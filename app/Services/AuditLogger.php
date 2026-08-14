<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Log a user action or system data mutation.
     */
    public static function log(string $action, string $description, ?array $changes = null, ?User $user = null): ?AuditLog
    {
        try {
            $actor = $user ?? auth()->user();

            $request = request();

            return AuditLog::create([
                'user_id' => $actor?->id,
                'actor_name' => $actor?->name ?? 'System Guest / Visitor',
                'actor_role' => $actor?->role ?? 'guest',
                'action' => strtoupper($action),
                'description' => $description,
                'ip_address' => $request?->ip(),
                'user_agent' => substr((string) $request?->userAgent(), 0, 500),
                'changes' => $changes,
            ]);
        } catch (\Throwable $e) {
            Log::error('AuditLogger error: ' . $e->getMessage(), ['exception' => $e]);
            return null;
        }
    }
}
