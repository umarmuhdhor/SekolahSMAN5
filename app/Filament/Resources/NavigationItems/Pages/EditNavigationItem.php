<?php

namespace App\Filament\Resources\NavigationItems\Pages;

use App\Filament\Resources\NavigationItems\NavigationItemResource;
use App\Modules\NavigationMenus\Actions\AssertUniqueNavigationSortOrderAction;
use App\Modules\NavigationMenus\Actions\AssertValidNavigationItemHierarchyAction;
use App\Modules\NavigationMenus\Actions\ValidateNavigationLinkAction;
use App\Modules\NavigationMenus\Support\NavigationItemTarget;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNavigationItem extends EditRecord
{
    protected static string $resource = NavigationItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $navigationMenuId = (int) ($data['navigation_menu_id'] ?? $this->record->navigation_menu_id);
        $parentInput = $data['parent_id'] ?? $this->record->parent_id;
        $parentId = filled($parentInput)
            ? (int) $parentInput
            : null;
        $sortOrder = max(1, (int) ($data['sort_order'] ?? $this->record->sort_order));
        $linkType = in_array((string) ($data['link_type'] ?? ''), NavigationLinkType::all(), true)
            ? (string) $data['link_type']
            : NavigationLinkType::URL;

        $linkValue = app(ValidateNavigationLinkAction::class)->execute(
            linkType: $linkType,
            linkValue: (string) ($data['link_value'] ?? $this->record->link_value),
        );

        app(AssertValidNavigationItemHierarchyAction::class)->execute(
            navigationMenuId: $navigationMenuId,
            parentId: $parentId,
            currentItemId: (int) $this->record->getKey(),
        );

        app(AssertUniqueNavigationSortOrderAction::class)->execute(
            navigationMenuId: $navigationMenuId,
            parentId: $parentId,
            sortOrder: $sortOrder,
            ignoreItemId: (int) $this->record->getKey(),
        );

        return [
            ...$data,
            'navigation_menu_id' => $navigationMenuId,
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
            'link_type' => $linkType,
            'link_value' => $linkValue,
            'target' => in_array((string) ($data['target'] ?? ''), NavigationItemTarget::all(), true)
                ? (string) $data['target']
                : NavigationItemTarget::SELF,
            'is_visible' => (bool) ($data['is_visible'] ?? $this->record->is_visible),
            'updated_by' => auth()->id(),
        ];
    }
}
