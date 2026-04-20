<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Modules\ThemeSettings\Support\ThemeDefaults;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThemeSetting>
 */
class ThemeSettingFactory extends Factory
{
    protected $model = ThemeSetting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'singleton_key' => 'default',
            'logo_media_id' => null,
            'primary_color' => ThemeDefaults::palette()['primary'],
            'secondary_color' => ThemeDefaults::palette()['secondary'],
            'accent_color' => ThemeDefaults::palette()['accent'],
            'is_active' => true,
            'updated_by' => null,
        ];
    }
}
