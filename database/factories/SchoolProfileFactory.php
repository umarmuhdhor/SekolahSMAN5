<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\SchoolProfile\Models\SchoolProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolProfile>
 */
class SchoolProfileFactory extends Factory
{
    protected $model = SchoolProfile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'singleton_key' => 'default',
            'school_name' => 'SMA Dinamis Nusantara',
            'description' => fake()->sentence(16),
            'address' => fake()->address(),
            'email' => 'profil@sekolah.local',
            'phone' => '+6281234567890',
            'website' => 'https://sekolah.local',
            'facebook_url' => null,
            'instagram_url' => null,
            'youtube_url' => null,
            'updated_by' => null,
        ];
    }
}
