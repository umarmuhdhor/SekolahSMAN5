<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Models;

use App\Models\User;
use Database\Factories\NavigationMenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    /** @use HasFactory<NavigationMenuFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'name',
        'location',
        'description',
        'is_active',
        'updated_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'navigation_menu_id');
    }

    public function rootItems(): HasMany
    {
        return $this->items()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return NavigationMenuFactory
     */
    protected static function newFactory()
    {
        return NavigationMenuFactory::new();
    }
}
