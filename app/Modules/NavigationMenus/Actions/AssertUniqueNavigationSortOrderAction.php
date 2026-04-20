<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Actions;

use App\Modules\NavigationMenus\Models\NavigationItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class AssertUniqueNavigationSortOrderAction
{
    /**
     * @throws ValidationException
     */
    public function execute(int $navigationMenuId, ?int $parentId, int $sortOrder, ?int $ignoreItemId = null): void
    {
        $query = NavigationItem::query()
            ->where('navigation_menu_id', $navigationMenuId)
            ->where('sort_order', $sortOrder)
            ->when(
                $parentId === null,
                fn (Builder $query): Builder => $query->whereNull('parent_id'),
                fn (Builder $query): Builder => $query->where('parent_id', $parentId),
            );

        if ($ignoreItemId !== null) {
            $query->whereKeyNot($ignoreItemId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'data.sort_order' => 'Sort order sudah digunakan pada level parent yang sama.',
            ]);
        }
    }
}
