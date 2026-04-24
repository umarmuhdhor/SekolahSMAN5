<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Actions;

use App\Modules\NavigationMenus\Models\NavigationItem;

class NormalizeNavigationItemSortOrderAction
{
    public function execute(int $navigationMenuId, ?int $parentId): void
    {
        $items = NavigationItem::query()
            ->where('navigation_menu_id', $navigationMenuId)
            ->when(
                $parentId === null,
                static fn ($query) => $query->whereNull('parent_id'),
                static fn ($query) => $query->where('parent_id', $parentId),
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'sort_order']);

        $expectedSort = 1;

        foreach ($items as $item) {
            if ((int) $item->sort_order !== $expectedSort) {
                NavigationItem::query()
                    ->whereKey($item->id)
                    ->update(['sort_order' => $expectedSort]);
            }

            $expectedSort++;
        }
    }
}
