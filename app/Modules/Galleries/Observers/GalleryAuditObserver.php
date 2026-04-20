<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Support\GalleryStatus;
use Illuminate\Support\Arr;

class GalleryAuditObserver
{
    public function created(Gallery $gallery): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::GALLERY_CREATED,
            module: AuditModule::GALLERIES,
            actorId: auth()->id(),
            entityType: Gallery::class,
            entityId: $gallery->getKey(),
            after: $this->snapshot($gallery),
        );
    }

    public function updated(Gallery $gallery): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($gallery->getChanges(), ['updated_at']);

        if ($changes === []) {
            return;
        }

        $originalStatus = (string) $gallery->getOriginal('status');
        $currentStatus = (string) $gallery->status;

        $isPublishedTransition = $originalStatus !== GalleryStatus::PUBLISHED
            && $currentStatus === GalleryStatus::PUBLISHED;

        $isUnpublishedTransition = $originalStatus === GalleryStatus::PUBLISHED
            && $currentStatus !== GalleryStatus::PUBLISHED;

        if ($isPublishedTransition) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::GALLERY_PUBLISHED,
                module: AuditModule::GALLERIES,
                actorId: auth()->id(),
                entityType: Gallery::class,
                entityId: $gallery->getKey(),
                before: [
                    'status' => $originalStatus,
                    'published_at' => $gallery->getOriginal('published_at'),
                ],
                after: [
                    'status' => $currentStatus,
                    'published_at' => $gallery->published_at,
                ],
            );
        }

        if ($isUnpublishedTransition) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::GALLERY_UNPUBLISHED,
                module: AuditModule::GALLERIES,
                actorId: auth()->id(),
                entityType: Gallery::class,
                entityId: $gallery->getKey(),
                before: [
                    'status' => $originalStatus,
                    'published_at' => $gallery->getOriginal('published_at'),
                ],
                after: [
                    'status' => $currentStatus,
                    'published_at' => $gallery->published_at,
                ],
            );
        }

        $changedKeys = array_keys($changes);

        if ($isPublishedTransition || $isUnpublishedTransition) {
            $changedKeys = array_values(array_diff($changedKeys, ['status', 'published_at']));
        }

        if ($changedKeys === []) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::GALLERY_UPDATED,
            module: AuditModule::GALLERIES,
            actorId: auth()->id(),
            entityType: Gallery::class,
            entityId: $gallery->getKey(),
            before: Arr::only($gallery->getOriginal(), $changedKeys),
            after: Arr::only($gallery->getAttributes(), $changedKeys),
        );
    }

    public function deleted(Gallery $gallery): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::GALLERY_DELETED,
            module: AuditModule::GALLERIES,
            actorId: auth()->id(),
            entityType: Gallery::class,
            entityId: $gallery->getKey(),
            before: $this->snapshot($gallery),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(Gallery $gallery): array
    {
        return [
            'title' => $gallery->title,
            'slug' => $gallery->slug,
            'status' => $gallery->status,
            'published_at' => $gallery->published_at,
            'author_id' => $gallery->author_id,
        ];
    }
}
