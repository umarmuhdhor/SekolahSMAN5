<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Models;

use App\Models\User;
use App\Modules\Galleries\Support\GalleryStatus;
use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
        'published_at',
        'author_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * @return Builder<Gallery>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', GalleryStatus::PUBLISHED)
            ->whereNotNull('published_at');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @return GalleryFactory
     */
    protected static function newFactory()
    {
        return GalleryFactory::new();
    }
}
