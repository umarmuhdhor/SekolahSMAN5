<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryItem;
use App\Modules\MediaLibrary\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    protected $model = GalleryItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gallery_id' => Gallery::factory(),
            'media_asset_id' => MediaAsset::query()->value('id') ?? MediaAsset::query()->create([
                'disk' => 's3',
                'bucket' => 'cms-sekolah-media',
                'path' => 'gallery-items/'.Str::uuid().'.jpg',
                'file_name' => 'gallery-item.jpg',
                'original_name' => 'gallery-item.jpg',
                'extension' => 'jpg',
                'mime_type' => 'image/jpeg',
                'size_bytes' => 1024,
                'checksum' => str_repeat('a', 64),
                'visibility' => 'private',
                'uploaded_by' => null,
            ])->getKey(),
            'sort_order' => 1,
            'caption' => fake()->sentence(8),
            'alt_text' => fake()->sentence(4),
        ];
    }
}
