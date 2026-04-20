<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Models;

use App\Modules\MediaLibrary\Models\MediaAsset;
use Database\Factories\GalleryItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    /** @use HasFactory<GalleryItemFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'gallery_id',
        'media_asset_id',
        'sort_order',
        'caption',
        'alt_text',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    /**
     * @return GalleryItemFactory
     */
    protected static function newFactory()
    {
        return GalleryItemFactory::new();
    }
}
