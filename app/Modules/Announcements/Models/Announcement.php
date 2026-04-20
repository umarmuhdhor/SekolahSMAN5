<?php

declare(strict_types=1);

namespace App\Modules\Announcements\Models;

use App\Models\User;
use App\Modules\Announcements\Support\AnnouncementStatus;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
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
        'publish_start_at',
        'publish_end_at',
        'author_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'publish_start_at' => 'datetime',
        'publish_end_at' => 'datetime',
    ];

    /**
     * @return Builder<Announcement>
     */
    public function scopeVisibleOnPublic(Builder $query): Builder
    {
        return $query
            ->where('status', AnnouncementStatus::PUBLISHED)
            ->whereNotNull('published_at')
            ->where(static function (Builder $query): void {
                $query
                    ->whereNull('publish_start_at')
                    ->orWhere('publish_start_at', '<=', now());
            })
            ->where(static function (Builder $query): void {
                $query
                    ->whereNull('publish_end_at')
                    ->orWhere('publish_end_at', '>=', now());
            });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return AnnouncementFactory
     */
    protected static function newFactory()
    {
        return AnnouncementFactory::new();
    }
}
