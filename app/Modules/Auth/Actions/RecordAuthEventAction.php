<?php

declare(strict_types=1);

namespace App\Modules\Auth\Actions;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditModule;
use Illuminate\Support\Facades\Log;

class RecordAuthEventAction
{
    /**
     * Mencatat event autentikasi ke audit table terstruktur dan log operasional.
     *
     * Konteks:
     * - Dipanggil oleh listener Login, Logout, dan Failed.
     * - Payload disusun konsisten untuk kebutuhan forensik.
     *
     * Parameter:
     *
     * @param  string  $eventType  Jenis event (login, logout, failed_login).
     * @param  int|string|null  $userId  ID user jika tersedia.
     * @param  string|null  $email  Email user jika tersedia.
     * @param  array<string, mixed>  $metadata  Metadata tambahan yang sudah disanitasi.
     *
     * Output:
     * - Tidak mengembalikan nilai.
     *
     * Validasi penting:
     * - Metadata kredensial sensitif harus disanitasi di listener sebelum dipanggil.
     *
     * Error cases:
     * - Jika channel log tidak tersedia, fallback ke channel default stack.
     *
     * Side effects:
     * - Menulis record pada tabel `audit_logs`.
     * - Menulis log operasional pada channel audit auth jika tersedia.
     */
    public function execute(string $eventType, int|string|null $userId, ?string $email, array $metadata = []): void
    {
        app(RecordAuditLogAction::class)->execute(
            eventType: $eventType,
            module: AuditModule::AUTH,
            actorId: $userId,
            after: [
                'email' => $email,
                'context' => $metadata,
            ],
        );

        $channel = (string) config('auth_session.audit_log_channel', 'auth_audit');

        $payload = [
            'event_type' => $eventType,
            'user_id' => $userId,
            'email' => $email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'occurred_at' => now()->toIso8601String(),
            'metadata' => $metadata,
        ];

        if (array_key_exists($channel, config('logging.channels', []))) {
            Log::channel($channel)->info('auth.activity', $payload);

            return;
        }

        Log::channel('stack')->info('auth.activity', $payload);
    }
}
