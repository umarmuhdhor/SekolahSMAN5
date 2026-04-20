<?php

declare(strict_types=1);

namespace App\Listeners\RolesPermissions;

use App\Modules\AuditLogs\Actions\RecordRolePermissionAuditEventAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Permission\Events\RoleAttached;

class RecordRoleAttachedEvent
{
    public function handle(RoleAttached $event): void
    {
        app(RecordRolePermissionAuditEventAction::class)->execute(
            eventType: AuditEventType::ROLE_CHANGE,
            model: $event->model,
            changes: [
                'change' => 'attached',
                'roles' => $this->normalizeItems($event->rolesOrIds),
            ],
        );
    }

    /**
     * @return array<int, mixed>
     */
    private function normalizeItems(mixed $items): array
    {
        if ($items instanceof Collection) {
            return $this->normalizeItems($items->all());
        }

        if (is_array($items)) {
            return array_map(fn (mixed $item): mixed => $this->normalizeItem($item), $items);
        }

        return [$this->normalizeItem($items)];
    }

    /**
     * @return array<string, mixed>|int|string|null
     */
    private function normalizeItem(mixed $item): array|int|string|null
    {
        if ($item instanceof Model) {
            return [
                'id' => $item->getKey(),
                'type' => $item::class,
                'name' => $item->getAttribute('name'),
            ];
        }

        return is_scalar($item) ? $item : null;
    }
}
