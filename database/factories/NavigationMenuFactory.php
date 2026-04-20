<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<NavigationMenu>
 */
class NavigationMenuFactory extends Factory
{
    protected $model = NavigationMenu::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'key' => Str::of($name)->slug('_')->value(),
            'name' => Str::title($name),
            'location' => NavigationMenuLocation::HEADER,
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'updated_by' => null,
        ];
    }
}
