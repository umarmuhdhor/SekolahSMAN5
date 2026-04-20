<?php

declare(strict_types=1);

namespace App\Modules\News\Models;

use App\Models\User;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\News\Support\NewsStatus;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'author_id',
        'cover_media_asset_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * @return Builder<News>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', NewsStatus::PUBLISHED)
            ->whereNotNull('published_at');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_asset_id');
    }

    /**
     * @return NewsFactory
     */
    protected static function newFactory()
    {
        return NewsFactory::new();
    }
}
