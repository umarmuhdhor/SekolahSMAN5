<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use Illuminate\Support\Arr;

class NavigationMenuAuditObserver
{
    public function created(NavigationMenu $navigationMenu): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NAVIGATION_MENU_UPDATED,
            module: AuditModule::NAVIGATION,
            actorId: auth()->id(),
            entityType: NavigationMenu::class,
            entityId: $navigationMenu->getKey(),
            metadata: ['action' => 'created'],
            after: $this->snapshot($navigationMenu),
        );
    }

    public function updated(NavigationMenu $navigationMenu): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($navigationMenu->getChanges(), ['updated_at', 'updated_by']);

        if ($changes === []) {
            return;
        }

        $changedKeys = array_keys($changes);

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NAVIGATION_MENU_UPDATED,
            module: AuditModule::NAVIGATION,
            actorId: auth()->id(),
            entityType: NavigationMenu::class,
            entityId: $navigationMenu->getKey(),
            metadata: ['action' => 'updated'],
            before: Arr::only($navigationMenu->getOriginal(), $changedKeys),
            after: Arr::only($navigationMenu->getAttributes(), $changedKeys),
        );
    }

    public function deleted(NavigationMenu $navigationMenu): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NAVIGATION_MENU_UPDATED,
            module: AuditModule::NAVIGATION,
            actorId: auth()->id(),
            entityType: NavigationMenu::class,
            entityId: $navigationMenu->getKey(),
            metadata: ['action' => 'deleted'],
            before: $this->snapshot($navigationMenu),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(NavigationMenu $navigationMenu): array
    {
        return [
            'key' => $navigationMenu->key,
            'name' => $navigationMenu->name,
            'location' => $navigationMenu->location,
            'description' => $navigationMenu->description,
            'is_active' => $navigationMenu->is_active,
        ];
    }
}
