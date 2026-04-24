<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Support\NavigationItemTarget;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NavigationItem>
 */
class NavigationItemFactory extends Factory
{
    protected $model = NavigationItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'navigation_menu_id' => NavigationMenu::factory(),
            'parent_id' => null,
            'label' => fake()->words(2, true),
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/'.fake()->slug(),
            'target' => NavigationItemTarget::SELF,
            'sort_order' => fake()->numberBetween(1, 9),
            'is_visible' => true,
            'updated_by' => null,
        ];
    }
}
