<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\NavigationMenus\Actions\NormalizeNavigationItemSortOrderAction;
use App\Modules\NavigationMenus\Models\NavigationItem;
use Illuminate\Support\Arr;

class NavigationItemAuditObserver
{
    public function created(NavigationItem $navigationItem): void
    {
        app(NormalizeNavigationItemSortOrderAction::class)->execute(
            navigationMenuId: (int) $navigationItem->navigation_menu_id,
            parentId: $navigationItem->parent_id,
        );

        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NAVIGATION_ITEM_CREATED,
            module: AuditModule::NAVIGATION,
            actorId: auth()->id(),
            entityType: NavigationItem::class,
            entityId: $navigationItem->getKey(),
            after: $this->snapshot($navigationItem),
        );
    }

    public function updated(NavigationItem $navigationItem): void
    {
        $originalMenuId = (int) $navigationItem->getOriginal('navigation_menu_id');
        $originalParentId = $navigationItem->getOriginal('parent_id');

        app(NormalizeNavigationItemSortOrderAction::class)->execute(
            navigationMenuId: (int) $navigationItem->navigation_menu_id,
            parentId: $navigationItem->parent_id,
        );

        if (
            $originalMenuId !== (int) $navigationItem->navigation_menu_id
            || (int) $originalParentId !== (int) $navigationItem->parent_id
        ) {
            app(NormalizeNavigationItemSortOrderAction::class)->execute(
                navigationMenuId: $originalMenuId,
                parentId: $originalParentId !== null ? (int) $originalParentId : null,
            );
        }

        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($navigationItem->getChanges(), ['updated_at', 'updated_by']);

        if ($changes === []) {
            return;
        }

        $changedKeys = array_keys($changes);

        if (in_array('sort_order', $changedKeys, true) || in_array('parent_id', $changedKeys, true)) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::NAVIGATION_ITEMS_REORDERED,
                module: AuditModule::NAVIGATION,
                actorId: auth()->id(),
                entityType: NavigationItem::class,
                entityId: $navigationItem->getKey(),
                before: [
                    'parent_id' => $navigationItem->getOriginal('parent_id'),
                    'sort_order' => $navigationItem->getOriginal('sort_order'),
                ],
                after: [
                    'parent_id' => $navigationItem->parent_id,
                    'sort_order' => $navigationItem->sort_order,
                ],
            );

            $changedKeys = array_values(array_diff($changedKeys, ['parent_id', 'sort_order']));
        }

        if ($changedKeys === []) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NAVIGATION_ITEM_UPDATED,
            module: AuditModule::NAVIGATION,
            actorId: auth()->id(),
            entityType: NavigationItem::class,
            entityId: $navigationItem->getKey(),
            before: Arr::only($navigationItem->getOriginal(), $changedKeys),
            after: Arr::only($navigationItem->getAttributes(), $changedKeys),
        );
    }

    public function deleted(NavigationItem $navigationItem): void
    {
        app(NormalizeNavigationItemSortOrderAction::class)->execute(
            navigationMenuId: (int) $navigationItem->navigation_menu_id,
            parentId: $navigationItem->parent_id,
        );

        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NAVIGATION_ITEM_DELETED,
            module: AuditModule::NAVIGATION,
            actorId: auth()->id(),
            entityType: NavigationItem::class,
            entityId: $navigationItem->getKey(),
            before: $this->snapshot($navigationItem),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(NavigationItem $navigationItem): array
    {
        return [
            'navigation_menu_id' => $navigationItem->navigation_menu_id,
            'parent_id' => $navigationItem->parent_id,
            'label' => $navigationItem->label,
            'link_type' => $navigationItem->link_type,
            'link_value' => $navigationItem->link_value,
            'target' => $navigationItem->target,
            'sort_order' => $navigationItem->sort_order,
            'is_visible' => $navigationItem->is_visible,
        ];
    }
}
