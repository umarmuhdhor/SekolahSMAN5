<?php

declare(strict_types=1);

namespace App\Modules\SchoolProfile\Models;

use App\Models\User;
use Database\Factories\SchoolProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolProfile extends Model
{
    /** @use HasFactory<SchoolProfileFactory> */
    use HasFactory;

    protected $table = 'school_profile';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'singleton_key',
        'school_name',
        'description',
        'address',
        'email',
        'phone',
        'website',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'updated_by',
    ];

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return SchoolProfileFactory
     */
    protected static function newFactory()
    {
        return SchoolProfileFactory::new();
    }
}
