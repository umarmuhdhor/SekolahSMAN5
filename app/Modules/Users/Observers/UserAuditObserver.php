<?php

declare(strict_types=1);

namespace App\Modules\Users\Observers;

use App\Models\User;
use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use Illuminate\Support\Arr;

class UserAuditObserver
{
    public function created(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::USER_CREATED,
            module: AuditModule::USERS,
            actorId: auth()->id(),
            entityType: User::class,
            entityId: $user->getKey(),
            after: $this->snapshot($user),
        );
    }

    public function updated(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        if ($user->wasChanged('is_active')) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::USER_STATUS_CHANGED,
                module: AuditModule::USERS,
                actorId: auth()->id(),
                entityType: User::class,
                entityId: $user->getKey(),
                before: [
                    'is_active' => (bool) $user->getOriginal('is_active'),
                ],
                after: [
                    'is_active' => (bool) $user->is_active,
                ],
            );
        }

        $changedAttributes = Arr::except($user->getChanges(), ['updated_at', 'remember_token', 'is_active']);

        if ($changedAttributes === []) {
            return;
        }

        $changedKeys = array_keys($changedAttributes);

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::USER_UPDATED,
            module: AuditModule::USERS,
            actorId: auth()->id(),
            entityType: User::class,
            entityId: $user->getKey(),
            before: Arr::only($user->getOriginal(), $changedKeys),
            after: Arr::only($user->getAttributes(), $changedKeys),
        );
    }

    public function deleted(User $user): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::USER_DELETED,
            module: AuditModule::USERS,
            actorId: auth()->id(),
            entityType: User::class,
            entityId: $user->getKey(),
            before: $this->snapshot($user),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(User $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => (bool) $user->is_active,
        ];
    }
}
