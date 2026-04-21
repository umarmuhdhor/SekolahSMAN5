<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Actions;

use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Support\NavigationItemTarget;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ResolvePublicNavigationMenuAction
{
    /**
     * @return array{
     *     name: string|null,
     *     items: array<int, array{
     *         label: string,
     *         url: string,
     *         target: string,
     *         is_external: bool,
     *         children: array<int, array{
     *             label: string,
     *             url: string,
     *             target: string,
     *             is_external: bool,
     *             children: array<int, mixed>
     *         }>
     *     }>
     * }
     */
    public function execute(string $location): array
    {
        if (! in_array($location, NavigationMenuLocation::all(), true)) {
            return [
                'name' => null,
                'items' => [],
            ];
        }

        if (! Schema::hasTable('navigation_menus') || ! Schema::hasTable('navigation_items')) {
            return [
                'name' => null,
                'items' => [],
            ];
        }

        $menu = NavigationMenu::query()
            ->where('location', $location)
            ->where('is_active', true)
            ->orderByDesc('updated_at')
            ->with([
                'rootItems' => static fn ($query) => $query
                    ->where('is_visible', true)
                    ->with([
                        'children' => static fn ($childQuery) => $childQuery
                            ->where('is_visible', true)
                            ->with([
                                'children' => static fn ($grandChildQuery) => $grandChildQuery
                                    ->where('is_visible', true),
                            ]),
                    ]),
            ])
            ->first();

        if ($menu === null) {
            return [
                'name' => null,
                'items' => [],
            ];
        }

        $items = [];

        foreach ($menu->rootItems as $item) {
            $resolved = $this->resolveItem($item);

            if ($resolved === null) {
                continue;
            }

            $items[] = $resolved;
        }

        return [
            'name' => $menu->name,
            'items' => $items,
        ];
    }

    /**
     * @return array{
     *     label: string,
     *     url: string,
     *     target: string,
     *     is_external: bool,
     *     children: array<int, array{
     *         label: string,
     *         url: string,
     *         target: string,
     *         is_external: bool,
     *         children: array<int, mixed>
     *     }>
     * }|null
     */
    private function resolveItem(NavigationItem $item, int $depth = 0): ?array
    {
        if ($depth > 2) {
            return null;
        }

        $url = $this->resolveUrl($item);

        if ($url === null) {
            return null;
        }

        $target = in_array($item->target, NavigationItemTarget::all(), true)
            ? $item->target
            : NavigationItemTarget::SELF;

        $children = [];

        foreach ($item->children as $child) {
            if (! $child->is_visible) {
                continue;
            }

            $resolvedChild = $this->resolveItem($child, $depth + 1);

            if ($resolvedChild === null) {
                continue;
            }

            $children[] = $resolvedChild;
        }

        return [
            'label' => trim($item->label) !== '' ? $item->label : 'Untitled',
            'url' => $url,
            'target' => $target,
            'is_external' => str_starts_with($url, 'http://') || str_starts_with($url, 'https://'),
            'children' => $children,
        ];
    }

    private function resolveUrl(NavigationItem $item): ?string
    {
        $linkValue = trim((string) $item->link_value);

        if ($linkValue === '') {
            return null;
        }

        if ($item->link_type === NavigationLinkType::ROUTE) {
            if (! Route::has($linkValue)) {
                return null;
            }

            try {
                return route($linkValue);
            } catch (Throwable) {
                return null;
            }
        }

        if (str_starts_with($linkValue, '/')) {
            return $linkValue;
        }

        $scheme = parse_url($linkValue, PHP_URL_SCHEME);

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        return filter_var($linkValue, FILTER_VALIDATE_URL) !== false
            ? $linkValue
            : null;
    }
}
