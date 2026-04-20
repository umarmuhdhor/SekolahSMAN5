<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Actions;

use App\Modules\NavigationMenus\Models\NavigationItem;
use Illuminate\Validation\ValidationException;

class AssertValidNavigationItemHierarchyAction
{
    /**
     * @throws ValidationException
     */
    public function execute(int $navigationMenuId, ?int $parentId, ?int $currentItemId = null): void
    {
        if ($parentId === null) {
            return;
        }

        if ($currentItemId !== null && $parentId === $currentItemId) {
            throw ValidationException::withMessages([
                'data.parent_id' => 'Parent item tidak boleh menunjuk dirinya sendiri.',
            ]);
        }

        $parent = NavigationItem::query()->find($parentId);

        if ($parent === null) {
            throw ValidationException::withMessages([
                'data.parent_id' => 'Parent item tidak ditemukan.',
            ]);
        }

        if ((int) $parent->navigation_menu_id !== $navigationMenuId) {
            throw ValidationException::withMessages([
                'data.parent_id' => 'Parent item harus berasal dari menu yang sama.',
            ]);
        }

        if ($currentItemId === null) {
            return;
        }

        $ancestor = $parent;

        while ($ancestor !== null) {
            if ((int) $ancestor->id === $currentItemId) {
                throw ValidationException::withMessages([
                    'data.parent_id' => 'Circular hierarchy terdeteksi pada parent-child navigation item.',
                ]);
            }

            $ancestor = $ancestor->parent;
        }
    }
}
