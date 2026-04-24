<?php

declare(strict_types=1);

namespace App\Modules\ThemeSettings\Models;

use App\Models\User;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\ThemeSettings\Support\ThemeDefaults;
use Database\Factories\ThemeSettingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeSetting extends Model
{
    /** @use HasFactory<ThemeSettingFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'singleton_key',
        'logo_media_id',
        'primary_color',
        'secondary_color',
        'accent_color',
        'is_active',
        'updated_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return Builder<ThemeSetting>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'logo_media_id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function resolvedPrimaryColor(): string
    {
        return ThemeDefaults::colorOrDefault($this->primary_color, 'primary');
    }

    public function resolvedSecondaryColor(): string
    {
        return ThemeDefaults::colorOrDefault($this->secondary_color, 'secondary');
    }

    public function resolvedAccentColor(): string
    {
        return ThemeDefaults::colorOrDefault($this->accent_color, 'accent');
    }

    /**
     * @return ThemeSettingFactory
     */
    protected static function newFactory()
    {
        return ThemeSettingFactory::new();
    }
}
