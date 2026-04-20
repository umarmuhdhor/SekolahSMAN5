<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'excerpt' => fake()->sentence(12),
            'content' => fake()->paragraphs(4, true),
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
            'author_id' => User::factory(),
            'cover_media_asset_id' => null,
        ];
    }

    public function published(): self
    {
        return $this->state(fn (): array => [
            'status' => NewsStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
