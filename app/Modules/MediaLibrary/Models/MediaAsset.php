<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaAsset extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'disk',
        'bucket',
        'path',
        'file_name',
        'original_name',
        'extension',
        'mime_type',
        'size_bytes',
        'checksum',
        'visibility',
        'alt_text',
        'caption',
        'uploaded_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
