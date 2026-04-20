<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'excerpt' => fake()->sentence(10),
            'content' => fake()->paragraphs(3, true),
            'status' => AnnouncementStatus::DRAFT,
            'published_at' => null,
            'publish_start_at' => null,
            'publish_end_at' => null,
            'author_id' => User::factory(),
        ];
    }

    public function published(): self
    {
        return $this->state(fn (): array => [
            'status' => AnnouncementStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
