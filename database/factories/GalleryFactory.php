<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Support\GalleryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'description' => fake()->sentence(12),
            'status' => GalleryStatus::DRAFT,
            'published_at' => null,
            'author_id' => User::factory(),
        ];
    }

    public function published(): self
    {
        return $this->state(fn (): array => [
            'status' => GalleryStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
