<?php

declare(strict_types=1);

namespace App\Modules\SchoolProfile\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use Illuminate\Support\Arr;

class SchoolProfileAuditObserver
{
    public function updated(SchoolProfile $schoolProfile): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($schoolProfile->getChanges(), ['updated_at']);

        if ($changes === []) {
            return;
        }

        $changedKeys = array_keys($changes);

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::SCHOOL_PROFILE_UPDATE,
            module: AuditModule::SCHOOL_PROFILE,
            actorId: auth()->id(),
            entityType: SchoolProfile::class,
            entityId: $schoolProfile->getKey(),
            before: Arr::only($schoolProfile->getOriginal(), $changedKeys),
            after: Arr::only($schoolProfile->getAttributes(), $changedKeys),
        );
    }
}
