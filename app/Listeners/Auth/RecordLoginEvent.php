<?php

declare(strict_types=1);

namespace App\Listeners\Auth;

use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\Auth\Actions\RecordAuthEventAction;
use Illuminate\Auth\Events\Login;

class RecordLoginEvent
{
    /**
     * Menangani event login berhasil dan meneruskan ke action pencatatan audit auth.
     */
    public function handle(Login $event): void
    {
        app(RecordAuthEventAction::class)->execute(
            eventType: AuditEventType::LOGIN,
            userId: $event->user->getAuthIdentifier(),
            email: $event->user->email ?? null,
            metadata: [
                'guard' => $event->guard,
                'remember' => $event->remember,
            ],
        );
    }
}
