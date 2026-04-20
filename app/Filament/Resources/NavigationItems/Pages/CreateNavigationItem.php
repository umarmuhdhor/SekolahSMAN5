<?php

namespace App\Filament\Resources\NavigationItems\Pages;

use App\Filament\Resources\NavigationItems\NavigationItemResource;
use App\Modules\NavigationMenus\Actions\AssertUniqueNavigationSortOrderAction;
use App\Modules\NavigationMenus\Actions\AssertValidNavigationItemHierarchyAction;
use App\Modules\NavigationMenus\Actions\ValidateNavigationLinkAction;
use App\Modules\NavigationMenus\Support\NavigationItemTarget;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use Filament\Resources\Pages\CreateRecord;

class CreateNavigationItem extends CreateRecord
{
    protected static string $resource = NavigationItemResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $navigationMenuId = (int) ($data['navigation_menu_id'] ?? 0);
        $parentId = filled($data['parent_id'] ?? null)
            ? (int) $data['parent_id']
            : null;
        $sortOrder = max(1, (int) ($data['sort_order'] ?? 1));
        $linkType = in_array((string) ($data['link_type'] ?? ''), NavigationLinkType::all(), true)
            ? (string) $data['link_type']
            : NavigationLinkType::URL;

        $linkValue = app(ValidateNavigationLinkAction::class)->execute(
            linkType: $linkType,
            linkValue: (string) ($data['link_value'] ?? ''),
        );

        app(AssertValidNavigationItemHierarchyAction::class)->execute(
            navigationMenuId: $navigationMenuId,
            parentId: $parentId,
        );

        app(AssertUniqueNavigationSortOrderAction::class)->execute(
            navigationMenuId: $navigationMenuId,
            parentId: $parentId,
            sortOrder: $sortOrder,
        );

        return [
            ...$data,
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
            'link_type' => $linkType,
            'link_value' => $linkValue,
            'target' => in_array((string) ($data['target'] ?? ''), NavigationItemTarget::all(), true)
                ? (string) $data['target']
                : NavigationItemTarget::SELF,
            'is_visible' => (bool) ($data['is_visible'] ?? true),
            'updated_by' => auth()->id(),
        ];
    }
}
