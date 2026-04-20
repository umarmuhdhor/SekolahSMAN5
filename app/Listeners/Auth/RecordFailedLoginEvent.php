<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\Auth\Actions\RecordAuthEventAction;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Arr;

class RecordFailedLoginEvent
{
    /**
     * Menangani event gagal login dan mencatat detail aman (tanpa password) untuk kebutuhan audit.
     */
    public function handle(Failed $event): void
    {
        $sanitizedCredentials = Arr::except($event->credentials, ['password', 'token', '_token']);

        app(RecordAuthEventAction::class)->execute(
            eventType: AuditEventType::FAILED_LOGIN,
            userId: $event->user?->getAuthIdentifier(),
            email: $event->credentials['email'] ?? $event->user?->email,
            metadata: [
                'guard' => $event->guard,
                'credentials' => $sanitizedCredentials,
            ],
        );
    }
}
