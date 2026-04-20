<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs\Actions;

use App\Modules\AuditLogs\Models\AuditLog;
use Illuminate\Support\Str;

class RecordAuditLogAction
{
    /**
     * @var array<int, string>
     */
    private const SENSITIVE_KEYS = [
        'password',
        'token',
        'secret',
        'authorization',
        'cookie',
        'api_key',
        'access_token',
        'refresh_token',
    ];

    /**
     * Menyimpan audit log terstruktur dengan redaksi data sensitif.
     *
     * @param  array<string, mixed>  $before
     * @param  array<string, mixed>  $after
     * @param  array<string, mixed>  $metadata
     */
    public function execute(
        string $eventType,
        string $module,
        int|string|null $actorId = null,
        ?string $entityType = null,
        int|string|null $entityId = null,
        array $before = [],
        array $after = [],
        array $metadata = [],
    ): void {
        $request = request();

        $sanitizedBefore = $this->sanitize($before);
        $sanitizedAfter = $this->sanitize($after);
        $sanitizedMetadata = $this->sanitize($metadata);

        AuditLog::query()->create([
            'actor_id' => $actorId !== null ? (int) $actorId : null,
            'event_type' => $eventType,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => $entityId !== null ? (string) $entityId : null,
            'before_json' => $sanitizedBefore !== [] ? $sanitizedBefore : null,
            'after_json' => $sanitizedAfter !== [] ? $sanitizedAfter : null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_id' => $this->resolveRequestId(),
            'metadata' => $sanitizedMetadata !== [] ? $sanitizedMetadata : null,
            'created_at' => now(),
        ]);
    }

    private function resolveRequestId(): ?string
    {
        $request = request();

        $requestId = $request->headers->get('X-Request-Id')
            ?? $request->headers->get('X-Request-ID');

        if (filled($requestId)) {
            return (string) $requestId;
        }

        return app()->runningInConsole() ? null : (string) Str::uuid();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function sanitize(array $payload): array
    {
        $sanitized = [];

        foreach ($payload as $key => $value) {
            if (is_string($key) && $this->shouldRedactKey($key)) {
                $sanitized[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);

                continue;
            }

            if ($value instanceof \JsonSerializable) {
                $sanitized[$key] = $value->jsonSerialize();

                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }

    private function shouldRedactKey(string $key): bool
    {
        $normalizedKey = Str::of($key)
            ->lower()
            ->replace('-', '_')
            ->value();

        foreach (self::SENSITIVE_KEYS as $sensitiveKey) {
            if (Str::contains($normalizedKey, $sensitiveKey)) {
                return true;
            }
        }

        return false;
    }
}
