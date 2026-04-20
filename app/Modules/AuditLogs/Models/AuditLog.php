<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class AuditLog extends Model
{
    /**
     * Tabel audit log bersifat append-only.
     */
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'actor_id',
        'event_type',
        'module',
        'entity_type',
        'entity_id',
        'before_json',
        'after_json',
        'ip',
        'user_agent',
        'request_id',
        'metadata',
        'created_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Audit log is immutable and cannot be updated.');
        });

        static::deleting(static function (): never {
            throw new LogicException('Audit log is immutable and cannot be deleted.');
        });
    }
}
