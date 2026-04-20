<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\Auth\Actions\RecordAuthEventAction;
use Illuminate\Auth\Events\Logout;

class RecordLogoutEvent
{
    /**
     * Menangani event logout berhasil dan mencatat jejak audit auth.
     */
    public function handle(Logout $event): void
    {
        app(RecordAuthEventAction::class)->execute(
            eventType: AuditEventType::LOGOUT,
            userId: $event->user?->getAuthIdentifier(),
            email: $event->user?->email,
            metadata: [
                'guard' => $event->guard,
            ],
        );
    }
}
