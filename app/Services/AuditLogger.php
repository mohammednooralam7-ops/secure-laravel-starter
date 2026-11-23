<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuditLogger
{
    public static function log(string $action, ?int $userId = null, array $context = []): void
    {
        DB::table('audit_logs')->insert([
            'user_id'    => $userId,
            'action'     => $action,
            'ip'         => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            // never log passwords or tokens
            'context'    => json_encode(collect($context)->except(['password', 'token'])->all()),
            'created_at' => now(),
        ]);
    }
}
