<?php

declare(strict_types=1);

namespace App\Modules\ThemeSettings\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use Illuminate\Support\Arr;

class ThemeSettingAuditObserver
{
    public function created(ThemeSetting $themeSetting): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::THEME_UPDATED,
            module: AuditModule::THEME,
            actorId: auth()->id(),
            entityType: ThemeSetting::class,
            entityId: $themeSetting->getKey(),
            after: $this->snapshot($themeSetting),
        );

        if ($themeSetting->logo_media_id !== null) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::THEME_LOGO_CHANGED,
                module: AuditModule::THEME,
                actorId: auth()->id(),
                entityType: ThemeSetting::class,
                entityId: $themeSetting->getKey(),
                before: [
                    'logo_media_id' => null,
                ],
                after: [
                    'logo_media_id' => $themeSetting->logo_media_id,
                ],
            );
        }
    }

    public function updated(ThemeSetting $themeSetting): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($themeSetting->getChanges(), ['updated_at', 'updated_by']);

        if ($changes === []) {
            return;
        }

        $changedKeys = array_keys($changes);

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::THEME_UPDATED,
            module: AuditModule::THEME,
            actorId: auth()->id(),
            entityType: ThemeSetting::class,
            entityId: $themeSetting->getKey(),
            before: Arr::only($themeSetting->getOriginal(), $changedKeys),
            after: Arr::only($themeSetting->getAttributes(), $changedKeys),
        );

        if (in_array('logo_media_id', $changedKeys, true)) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::THEME_LOGO_CHANGED,
                module: AuditModule::THEME,
                actorId: auth()->id(),
                entityType: ThemeSetting::class,
                entityId: $themeSetting->getKey(),
                before: [
                    'logo_media_id' => $themeSetting->getOriginal('logo_media_id'),
                ],
                after: [
                    'logo_media_id' => $themeSetting->logo_media_id,
                ],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(ThemeSetting $themeSetting): array
    {
        return [
            'logo_media_id' => $themeSetting->logo_media_id,
            'primary_color' => $themeSetting->primary_color,
            'secondary_color' => $themeSetting->secondary_color,
            'accent_color' => $themeSetting->accent_color,
            'is_active' => $themeSetting->is_active,
        ];
    }
}
