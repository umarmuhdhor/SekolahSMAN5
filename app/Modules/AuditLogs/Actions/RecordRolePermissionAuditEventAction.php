<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs\Actions;

use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use Illuminate\Database\Eloquent\Model;

class RecordRolePermissionAuditEventAction
{
    /**
     * @param  array<string, mixed>  $changes
     */
    public function execute(string $eventType, Model $model, array $changes): void
    {
        if (! auth()->check()) {
            return;
        }

        if (! in_array($eventType, [AuditEventType::ROLE_CHANGE, AuditEventType::PERMISSION_CHANGE], true)) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: $eventType,
            module: AuditModule::ROLES_PERMISSIONS,
            actorId: auth()->id(),
            entityType: $model::class,
            entityId: $model->getKey(),
            after: $changes,
        );
    }
}
