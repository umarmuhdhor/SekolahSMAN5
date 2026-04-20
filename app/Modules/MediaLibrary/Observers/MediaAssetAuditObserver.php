<?php

declare(strict_types=1);

namespace App\Modules\MediaLibrary\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\MediaLibrary\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;

class MediaAssetAuditObserver
{
    public function created(MediaAsset $mediaAsset): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::MEDIA_UPLOAD,
            module: AuditModule::MEDIA_LIBRARY,
            actorId: auth()->id(),
            entityType: MediaAsset::class,
            entityId: $mediaAsset->getKey(),
            after: $this->snapshot($mediaAsset),
        );
    }

    public function updated(MediaAsset $mediaAsset): void
    {
        if (! auth()->check()) {
            return;
        }

        if (! $mediaAsset->wasChanged('path')) {
            return;
        }

        $oldPath = (string) $mediaAsset->getOriginal('path');
        $oldDisk = (string) $mediaAsset->getOriginal('disk');

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::MEDIA_REPLACE,
            module: AuditModule::MEDIA_LIBRARY,
            actorId: auth()->id(),
            entityType: MediaAsset::class,
            entityId: $mediaAsset->getKey(),
            before: [
                'disk' => $oldDisk,
                'path' => $oldPath,
            ],
            after: [
                'disk' => $mediaAsset->disk,
                'path' => $mediaAsset->path,
            ],
        );

        if (filled($oldPath) && Storage::disk($oldDisk)->exists($oldPath)) {
            Storage::disk($oldDisk)->delete($oldPath);
        }
    }

    public function deleted(MediaAsset $mediaAsset): void
    {
        if (filled($mediaAsset->path) && Storage::disk($mediaAsset->disk)->exists($mediaAsset->path)) {
            Storage::disk($mediaAsset->disk)->delete($mediaAsset->path);
        }

        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::MEDIA_DELETE,
            module: AuditModule::MEDIA_LIBRARY,
            actorId: auth()->id(),
            entityType: MediaAsset::class,
            entityId: $mediaAsset->getKey(),
            before: $this->snapshot($mediaAsset),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(MediaAsset $mediaAsset): array
    {
        return [
            'disk' => $mediaAsset->disk,
            'bucket' => $mediaAsset->bucket,
            'path' => $mediaAsset->path,
            'file_name' => $mediaAsset->file_name,
            'original_name' => $mediaAsset->original_name,
            'mime_type' => $mediaAsset->mime_type,
            'size_bytes' => $mediaAsset->size_bytes,
            'checksum' => $mediaAsset->checksum,
            'uploaded_by' => $mediaAsset->uploaded_by,
        ];
    }
}
