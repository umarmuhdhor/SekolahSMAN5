<?php

declare(strict_types=1);

namespace App\Modules\Announcements\Observers;

use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use Illuminate\Support\Arr;

class AnnouncementAuditObserver
{
    public function created(Announcement $announcement): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::ANNOUNCEMENT_CREATED,
            module: AuditModule::ANNOUNCEMENTS,
            actorId: auth()->id(),
            entityType: Announcement::class,
            entityId: $announcement->getKey(),
            after: $this->snapshot($announcement),
        );
    }

    public function updated(Announcement $announcement): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($announcement->getChanges(), ['updated_at']);

        if ($changes === []) {
            return;
        }

        $originalStatus = (string) $announcement->getOriginal('status');
        $currentStatus = (string) $announcement->status;

        $isPublishedTransition = $originalStatus !== AnnouncementStatus::PUBLISHED
            && $currentStatus === AnnouncementStatus::PUBLISHED;

        $isUnpublishedTransition = $originalStatus === AnnouncementStatus::PUBLISHED
            && $currentStatus !== AnnouncementStatus::PUBLISHED;

        if ($isPublishedTransition) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::ANNOUNCEMENT_PUBLISHED,
                module: AuditModule::ANNOUNCEMENTS,
                actorId: auth()->id(),
                entityType: Announcement::class,
                entityId: $announcement->getKey(),
                before: [
                    'status' => $originalStatus,
                    'published_at' => $announcement->getOriginal('published_at'),
                ],
                after: [
                    'status' => $currentStatus,
                    'published_at' => $announcement->published_at,
                ],
            );
        }

        if ($isUnpublishedTransition) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::ANNOUNCEMENT_UNPUBLISHED,
                module: AuditModule::ANNOUNCEMENTS,
                actorId: auth()->id(),
                entityType: Announcement::class,
                entityId: $announcement->getKey(),
                before: [
                    'status' => $originalStatus,
                    'published_at' => $announcement->getOriginal('published_at'),
                ],
                after: [
                    'status' => $currentStatus,
                    'published_at' => $announcement->published_at,
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
            eventType: AuditEventType::ANNOUNCEMENT_UPDATED,
            module: AuditModule::ANNOUNCEMENTS,
            actorId: auth()->id(),
            entityType: Announcement::class,
            entityId: $announcement->getKey(),
            before: Arr::only($announcement->getOriginal(), $changedKeys),
            after: Arr::only($announcement->getAttributes(), $changedKeys),
        );
    }

    public function deleted(Announcement $announcement): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::ANNOUNCEMENT_DELETED,
            module: AuditModule::ANNOUNCEMENTS,
            actorId: auth()->id(),
            entityType: Announcement::class,
            entityId: $announcement->getKey(),
            before: $this->snapshot($announcement),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(Announcement $announcement): array
    {
        return [
            'title' => $announcement->title,
            'slug' => $announcement->slug,
            'status' => $announcement->status,
            'published_at' => $announcement->published_at,
            'publish_start_at' => $announcement->publish_start_at,
            'publish_end_at' => $announcement->publish_end_at,
            'author_id' => $announcement->author_id,
        ];
    }
}
